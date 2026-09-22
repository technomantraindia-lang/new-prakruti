<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ShippingMethod;
use App\Services\BillDiscountService;
use App\Services\GstService;
use App\Services\InventoryService;
use App\Services\ShippingChargeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CheckoutApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.var_id' => 'nullable|exists:product_variations,id',
            'items.*.qty' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'shipping_method_id' => 'nullable|exists:shipping_methods,id',
            'payment_method' => 'nullable|string',
            'order_type' => 'nullable|in:standard,family_pack',
        ]);

        $user = auth()->user();

        return DB::transaction(function () use ($request, $user) {
            $subtotal = 0;
            $orderItemsData = [];

            // 1. Calculate Authoritative Subtotal & Validate Stock
            foreach ($request->items as $itemReq) {
                $product = Product::with(['category', 'subCategory', 'subSubCategory'])->lockForUpdate()->find($itemReq['product_id']);

                if (! $product || $product->status !== 'active') {
                    throw new \Exception("Product #{$itemReq['product_id']} is unavailable.");
                }

                $variation = null;
                if (! empty($itemReq['var_id'])) {
                    $variation = ProductVariation::where('id', $itemReq['var_id'])
                        ->where('product_id', $product->id)
                        ->lockForUpdate()
                        ->first();

                    if (! $variation || $variation->status !== 'active') {
                        throw new \Exception("Selected package for '{$product->name}' is unavailable.");
                    }
                } else {
                    $variation = ProductVariation::where('product_id', $product->id)
                        ->where('status', 'active')
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->first();
                }

                $stockItem = $variation ?: $product;
                $availableStock = (int) $stockItem->stock_qty;
                if ($availableStock < $itemReq['qty']) {
                    throw new \Exception("Product '{$product->name}' does not have sufficient stock. Available: {$availableStock}.");
                }

                // Authoritative Database Price
                $itemPrice = $variation
                    ? (float) ($variation->sale_price ?? $variation->price ?? $product->sale_price ?? $product->price)
                    : (float) ($product->sale_price ?? $product->price);
                $lineTotal = $itemPrice * $itemReq['qty'];
                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'product' => $product,
                    'stock_item' => $stockItem,
                    'variation' => $variation,
                    'product_id' => $product->id,
                    'var_id' => $variation?->id,
                    'product_name' => $product->name,
                    'sku' => $variation?->sku ?: $product->sku,
                    'qty' => $itemReq['qty'],
                    'price' => $itemPrice,
                    'gst_pct' => app(GstService::class)->productGstPercentage($product),
                    'line_total' => $lineTotal,
                ];
            }

            // 2. Automatic bill discount from admin settings
            $discountRule = app(BillDiscountService::class)->calculate($subtotal);
            $discount = (float) $discountRule['discount'];

            // 3. Authoritative GST Calculation from category GST slabs
            $taxableSubtotal = max(0, $subtotal - $discount);
            $gstAmt = app(GstService::class)->calculateTotal($orderItemsData, $subtotal, $discount);

            // 4. Authoritative Shipping Calculation
            $shipId = null;
            $shipMethod = null;
            if ($request->filled('shipping_method_id')) {
                $shipMethod = ShippingMethod::find($request->shipping_method_id);
                if ($shipMethod) {
                    $shipId = $shipMethod->id;
                }
            }
            $shipping = app(ShippingChargeService::class)->calculate($subtotal, $shipMethod);
            $shipCharge = $shipping['charge'];

            $total = $taxableSubtotal + $gstAmt + $shipCharge;

            $orderNum = 'PRK-' . strtoupper(Str::random(6)) . '-' . rand(100, 999);

            // 5. Create Order Record
            $orderPayload = [
                'order_num' => $orderNum,
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'gst_amt' => $gstAmt,
                'ship_charge' => $shipCharge,
                'total' => $total,
                'ship_id' => $shipId,
                'status' => 'pending',
                'pay_status' => 'pending',
                'payment_method' => $request->get('payment_method', 'cod'),
                'ship_addr' => $request->get('shipping_address', 'Default Address'),
                'bill_addr' => $request->get('billing_address', 'Default Address'),
            ];

            if (Schema::hasColumn('orders', 'order_type')) {
                $orderPayload['order_type'] = $request->get('order_type') === 'family_pack' ? 'family_pack' : 'standard';
            }

            $order = Order::create($orderPayload);

            // 6. Create Order Items & Reduce Stock Once
            $inventoryService = app(InventoryService::class);
            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'var_id' => $itemData['var_id'],
                    'product_name' => $itemData['product_name'],
                    'sku' => $itemData['sku'],
                    'qty' => $itemData['qty'],
                    'price' => $itemData['price'],
                    'gst_pct' => $itemData['gst_pct'],
                    'line_total' => $itemData['line_total'],
                ]);

                $inventoryService->decreaseStock(
                    $itemData['stock_item'],
                    $itemData['qty'],
                    "Order #{$order->order_num}",
                    $order
                );
            }

            // 7. Record Order History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Order created via storefront checkout',
            ]);

            // 8. Record Payment Entry
            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'method' => $request->get('payment_method', 'cod'),
                'status' => 'pending',
                'txn_id' => 'TXN-' . time() . '-' . rand(1000, 9999),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully.',
                'data' => new OrderResource($order->load(['items.product', 'items.variation', 'payment'])),
            ], 201);
        });
    }
}
