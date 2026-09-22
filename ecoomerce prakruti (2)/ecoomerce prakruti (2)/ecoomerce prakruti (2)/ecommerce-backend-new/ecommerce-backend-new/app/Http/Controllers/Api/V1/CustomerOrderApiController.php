<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\ProductVariation;
use App\Services\BillDiscountService;
use App\Services\GstService;
use App\Services\ShippingChargeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CustomerOrderApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $orders = Order::with(['items.product', 'items.variation'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Customer orders fetched successfully.',
            'data' => OrderResource::collection($orders->getCollection()),
            'pagination' => [
                'total' => $orders->total(),
                'current_page' => $orders->currentPage(),
                'total_pages' => $orders->lastPage(),
            ],
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $order = Order::with(['items.product', 'items.variation'])
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order details fetched successfully.',
            'data' => new OrderResource($order),
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $user = auth()->user();
        $order = Order::with(['items.product', 'items.variation'])
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized.',
            ], 404);
        }

        if (! in_array($order->status, ['pending', 'processing'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or processing orders can be cancelled.',
            ], 422);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $order->update([
            'status' => 'cancelled',
            'admin_note' => trim(($order->admin_note ? $order->admin_note . "\n" : '') . 'Customer cancellation request: ' . ($validated['reason'] ?? 'No reason provided.')),
        ]);

        \App\Models\OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'cancelled',
            'note' => 'Cancelled by customer' . (! empty($validated['reason']) ? ': ' . $validated['reason'] : '.'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
            'data' => new OrderResource($order->fresh()->load(['items.product', 'items.variation'])),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.var_id' => 'nullable|exists:product_variations,id',
            'items.*.qty' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'order_type' => 'nullable|in:standard,family_pack',
        ]);

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($validated['items'] as $item) {
            $product = \App\Models\Product::with(['category', 'subCategory', 'subSubCategory'])->find($item['product_id']);
            if (!$product) continue;

            $variation = null;
            if (! empty($item['var_id'])) {
                $variation = ProductVariation::where('id', $item['var_id'])
                    ->where('product_id', $product->id)
                    ->first();
            } else {
                $variation = ProductVariation::where('product_id', $product->id)
                    ->where('status', 'active')
                    ->orderBy('id')
                    ->first();
            }

            $itemPrice = $variation
                ? ($variation->sale_price ?? $variation->price ?? $product->sale_price ?? $product->price)
                : ($product->sale_price ?? $product->price);
            $lineSubtotal = $itemPrice * $item['qty'];
            $subtotal += $lineSubtotal;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'var_id' => $variation?->id,
                'product_name' => $product->name,
                'sku' => $variation?->sku ?: $product->sku,
                'qty' => $item['qty'],
                'price' => $itemPrice,
                'gst_pct' => app(GstService::class)->productGstPercentage($product),
                'line_total' => $lineSubtotal,
            ];
        }

        $discountRule = app(BillDiscountService::class)->calculate($subtotal);
        $discount = (float) $discountRule['discount'];
        $taxableSubtotal = max(0, $subtotal - $discount);
        $gstAmt = app(GstService::class)->calculateTotal($orderItemsData, $subtotal, $discount);
        $shipping = app(ShippingChargeService::class)->calculate($subtotal);
        $shipCharge = $shipping['charge'];
        $total = $taxableSubtotal + $gstAmt + $shipCharge;

        $orderNum = 'PRK-' . strtoupper(Str::random(6)) . '-' . rand(100, 999);

        $orderPayload = [
            'order_num' => $orderNum,
            'user_id' => $user->id,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'gst_amt' => $gstAmt,
            'ship_charge' => $shipCharge,
            'total' => $total,
            'status' => 'pending',
            'pay_status' => 'pending',
            'payment_method' => $request->get('payment_method', 'cod'),
            'ship_addr' => $request->get('shipping_address', ''),
            'bill_addr' => $request->get('billing_address', ''),
        ];

        if (Schema::hasColumn('orders', 'order_type')) {
            $orderPayload['order_type'] = $request->get('order_type') === 'family_pack' ? 'family_pack' : 'standard';
        }

        $order = Order::create($orderPayload);

        foreach ($orderItemsData as $itemData) {
            $itemData['order_id'] = $order->id;
            \App\Models\OrderItem::create($itemData);
        }

        \App\Models\OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'note' => 'Order placed via API',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully.',
            'data' => new OrderResource($order->load(['items.product', 'items.variation'])),
        ], 201);
    }
}
