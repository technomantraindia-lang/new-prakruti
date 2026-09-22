<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\BillDiscountService;
use App\Services\GstService;
use App\Services\ShippingChargeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartApiController extends Controller
{
    private function getOrCreateCart(Request $request)
    {
        $user = auth()->user();
        $sessionId = $request->header('X-Session-ID') ?: $request->cookie('cart_session');

        if ($user) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['session_id' => $sessionId]
            );

            if ($sessionId) {
                $guestCart = Cart::where('session_id', $sessionId)
                    ->where(function ($query) {
                        $query->whereNull('user_id')->orWhere('user_id', 0);
                    })
                    ->where('id', '!=', $cart->id)
                    ->first();

                if ($guestCart) {
                    foreach ($guestCart->items as $item) {
                        $existing = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $item->product_id)
                            ->where('var_id', $item->var_id)
                            ->first();

                        if ($existing) {
                            $existing->update(['qty' => $existing->qty + $item->qty]);
                            $item->delete();
                        } else {
                            $item->update(['cart_id' => $cart->id]);
                        }
                    }
                    $guestCart->delete();
                }
            }

            return $cart;
        }

        if (! $sessionId) {
            $sessionId = (string) Str::uuid();
        }

        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null]
        );
    }

    private function unitPrice($item): float
    {
        $product = $item->product;
        $variation = $item->variation;

        if ($variation) {
            return (float) ($variation->sale_price ?? $variation->price ?? $product->sale_price ?? $product->price ?? 0);
        }

        return (float) ($product->sale_price ?? $product->price ?? $item->sale_price ?? $item->price ?? 0);
    }

    private function serializeCart(Cart $cart): array
    {
        $cart->load(['items.product.category', 'items.product.subCategory', 'items.product.subSubCategory', 'items.product.variations', 'items.variation']);

        $items = $cart->items->map(function ($item) {
            $product = $item->product;
            $displayVariation = $item->variation ?: $product?->variations?->where('status', 'active')->first();
            $price = $displayVariation
                ? (float) ($displayVariation->sale_price ?? $displayVariation->price ?? $product->sale_price ?? $product->price ?? 0)
                : $this->unitPrice($item);
            $gstPct = $product ? app(GstService::class)->productGstPercentage($product) : 0;
            $packageLabel = $displayVariation?->attr_val
                ?: ($displayVariation?->weight ? rtrim(rtrim(number_format((float) $displayVariation->weight, 2, '.', ''), '0'), '.') . ' kg' : null);

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'var_id' => $item->var_id ?: $displayVariation?->id,
                'package' => $packageLabel,
                'weight' => $packageLabel ?: ($product?->weight ? rtrim(rtrim(number_format((float) $product->weight, 2, '.', ''), '0'), '.') . ' kg' : null),
                'name' => $product ? $product->name : 'Product',
                'product_name' => $product ? $product->name : 'Product',
                'image' => $product?->image_url,
                'price' => $price,
                'qty' => (int) $item->qty,
                'quantity' => (int) $item->qty,
                'line_total' => round($price * $item->qty, 2),
                'gst_pct' => $gstPct,
                'stock_qty' => $displayVariation ? (int) $displayVariation->stock_qty : ($product ? (int) $product->stock_qty : 0),
            ];
        })->values();

        $subtotal = round($items->sum('line_total'), 2);
        $discountRule = app(BillDiscountService::class)->calculate($subtotal);
        $discount = round((float) $discountRule['discount'], 2);
        $taxableSubtotal = max(0, $subtotal - $discount);
        $gstAmt = app(GstService::class)->calculateTotal($items->all(), $subtotal, $discount);
        $shipping = app(ShippingChargeService::class)->calculate($subtotal);
        $shipCharge = $shipping['charge'];

        return [
            'id' => $cart->id,
            'session_id' => $cart->session_id,
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discount_rule' => $discountRule,
            'taxable_subtotal' => round($taxableSubtotal, 2),
            'gst_amt' => $gstAmt,
            'ship_charge' => $shipCharge,
            'shipping_rule' => $shipping,
            'free_shipping_min_amount' => $shipping['free_shipping_min_amount'],
            'free_shipping_remaining' => $shipping['free_shipping_remaining'],
            'free_shipping_unlocked' => $shipping['free_shipping_unlocked'],
            'total' => round($taxableSubtotal + $gstAmt + $shipCharge, 2),
        ];
    }

    public function index(Request $request)
    {
        $cart = $this->getOrCreateCart($request);

        return response()->json([
            'success' => true,
            'data' => $this->serializeCart($cart),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'var_id' => 'nullable|exists:product_variations,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $variation = null;
        if (! empty($validated['var_id'])) {
            $variation = ProductVariation::where('id', $validated['var_id'])
                ->where('product_id', $product->id)
                ->first();

            if (! $variation || $variation->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected package is not available for this product.',
                ], 422);
            }
        } else {
            $variation = $product->variations()
                ->where('status', 'active')
                ->orderBy('id')
                ->first();
        }

        $stockQty = $variation ? (int) $variation->stock_qty : (int) $product->stock_qty;
        if ($stockQty < $validated['qty']) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient stock. Only {$stockQty} items available.",
            ], 422);
        }

        $cart = $this->getOrCreateCart($request);
        $unitPrice = $variation
            ? (float) ($variation->sale_price ?? $variation->price ?? $product->sale_price ?? $product->price)
            : (float) ($product->sale_price ?? $product->price);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('var_id', $variation?->id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->qty + $validated['qty'];
            if ($stockQty < $newQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot add more. Stock limit of {$stockQty} reached.",
                ], 422);
            }
            $cartItem->update(['qty' => $newQty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'var_id' => $variation?->id,
                'qty' => $validated['qty'],
                'price' => $unitPrice,
                'sale_price' => $variation?->sale_price ?? $product->sale_price,
            ]);
        }

        return $this->index($request);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($id);
        $product = $cartItem->product;
        $variation = $cartItem->variation;
        $stockQty = $variation ? (int) $variation->stock_qty : ($product ? (int) $product->stock_qty : 0);

        if (($product || $variation) && $stockQty < $validated['qty']) {
            return response()->json([
                'success' => false,
                'message' => "Requested quantity exceeds available stock ({$stockQty}).",
            ], 422);
        }

        $cartItem->update(['qty' => $validated['qty']]);

        return $this->index($request);
    }

    public function destroy(Request $request, $id)
    {
        $cartItem = CartItem::find($id);
        if ($cartItem) {
            $cartItem->delete();
        }

        return $this->index($request);
    }

    public function clear(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        CartItem::where('cart_id', $cart->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
            'data' => ['items' => [], 'subtotal' => 0, 'session_id' => $cart->session_id],
        ]);
    }
}
