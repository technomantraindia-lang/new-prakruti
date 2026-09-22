<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RefundService
{
    public function getRefundableAmount(Order $order, bool $includePending = true): float
    {
        $statuses = $includePending ? ['pending', 'approved', 'completed'] : ['approved', 'completed'];

        $previouslyRefunded = (float) Refund::where('order_id', $order->id)
            ->whereIn('status', $statuses)
            ->sum('amount');

        return max((float) $order->total - $previouslyRefunded, 0.0);
    }

    public function requestRefund(Order $order, float $amount, string $reason = '', ?User $byUser = null): Refund
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Refund amount must be greater than zero.');
        }

        return DB::transaction(function () use ($order, $amount, $reason, $byUser) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (! $lockedOrder) {
                throw new InvalidArgumentException('Order not found.');
            }

            $refundable = $this->getRefundableAmount($lockedOrder, true);
            if ($amount > $refundable + 0.01) {
                throw new InvalidArgumentException("Refund amount (₹{$amount}) exceeds maximum refundable amount (₹{$refundable}).");
            }

            $refundNum = 'REF-' . str_pad((string) (Refund::max('id') + 1), 6, '0', STR_PAD_LEFT);
            $userId = $byUser?->id ?? Auth::id();

            $refund = Refund::create([
                'order_id' => $lockedOrder->id,
                'refund_num' => $refundNum,
                'amount' => $amount,
                'reason' => $reason,
                'status' => 'pending',
                'processed_by' => $userId,
            ]);

            ActivityLogService::log(
                'refund_requested',
                'orders',
                "Requested refund #{$refund->refund_num} of ₹{$amount} for Order #{$lockedOrder->order_num} (Reason: {$reason})",
                $refund
            );

            return $refund;
        });
    }

    public function approveRefund(Refund $refund, ?User $byUser = null): bool
    {
        return DB::transaction(function () use ($refund, $byUser) {
            $lockedRefund = Refund::where('id', $refund->id)->lockForUpdate()->first();
            if (! $lockedRefund || $lockedRefund->status !== 'pending') {
                $status = $lockedRefund?->status ?? 'non-existent';
                throw new InvalidArgumentException("Refund #{$refund->id} cannot be approved because current status is '{$status}'.");
            }

            $userId = $byUser?->id ?? Auth::id();

            $lockedRefund->update([
                'status' => 'approved',
                'processed_by' => $userId,
                'processed_at' => now(),
            ]);

            $order = Order::where('id', $lockedRefund->order_id)->lockForUpdate()->first();
            $totalRefunded = (float) Refund::where('order_id', $order->id)
                ->whereIn('status', ['approved', 'completed'])
                ->sum('amount');

            if ($totalRefunded >= (float) $order->total) {
                $order->update([
                    'pay_status' => 'refunded',
                    'status' => 'refunded',
                ]);
            } else {
                $order->update([
                    'pay_status' => 'refunded',
                ]);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'note' => "Approved refund #{$lockedRefund->refund_num} (Amount: ₹{$lockedRefund->amount})",
            ]);

            ActivityLogService::log(
                'refund_approved',
                'orders',
                "Approved refund #{$lockedRefund->refund_num} (₹{$lockedRefund->amount}) for Order #{$order->order_num}",
                $lockedRefund
            );

            return true;
        });
    }

    public function rejectRefund(Refund $refund, string $reason = '', ?User $byUser = null): bool
    {
        return DB::transaction(function () use ($refund, $reason, $byUser) {
            $lockedRefund = Refund::where('id', $refund->id)->lockForUpdate()->first();
            if (! $lockedRefund || $lockedRefund->status !== 'pending') {
                $status = $lockedRefund?->status ?? 'non-existent';
                throw new InvalidArgumentException("Refund #{$refund->id} cannot be rejected because current status is '{$status}'.");
            }

            $userId = $byUser?->id ?? Auth::id();

            $lockedRefund->update([
                'status' => 'rejected',
                'reason' => $reason ?: $lockedRefund->reason,
                'processed_by' => $userId,
                'processed_at' => now(),
            ]);

            ActivityLogService::log(
                'refund_rejected',
                'orders',
                "Rejected refund #{$lockedRefund->refund_num} for Order #{$lockedRefund->order?->order_num} (Reason: {$reason})",
                $lockedRefund
            );

            return true;
        });
    }
}
