<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderActivity;
use Illuminate\Support\Facades\Auth;

class OrderActivityService
{
    /**
     * Log order creation
     */
    public function logCreated(Order $order): OrderActivity
    {
        return OrderActivity::create([
            'order_id' => $order->id,
            'user_id' => Auth::id() ?? $order->created_by,
            'activity_type' => OrderActivity::TYPE_CREATED,
            'description' => 'Order created',
            'new_values' => [
                'order_number' => $order->order_number,
                'customer' => $order->customer?->name,
                'total' => $order->total,
                'status' => $order->status,
            ],
        ]);
    }

    /**
     * Log order update
     */
    public function logUpdated(Order $order, array $oldValues, array $newValues): OrderActivity
    {
        $changes = [];
        foreach ($newValues as $key => $value) {
            if (isset($oldValues[$key]) && $oldValues[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $value,
                ];
            }
        }

        $description = $this->generateUpdateDescription($changes);

        return OrderActivity::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'activity_type' => OrderActivity::TYPE_UPDATED,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Log status change
     */
    public function logStatusChanged(Order $order, string $oldStatus, string $newStatus, ?string $notes = null): OrderActivity
    {
        return OrderActivity::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'activity_type' => OrderActivity::TYPE_STATUS_CHANGED,
            'description' => "Status changed from '{$oldStatus}' to '{$newStatus}'",
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $newStatus],
            'notes' => $notes,
        ]);
    }

    /**
     * Log payment added
     */
    public function logPaymentAdded(Order $order, float $amount, string $method): OrderActivity
    {
        return OrderActivity::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'activity_type' => OrderActivity::TYPE_PAYMENT_ADDED,
            'description' => "Payment added: " . number_format($amount, 0, ',', '.') . " via {$method}",
            'new_values' => [
                'amount' => $amount,
                'method' => $method,
                'total_paid' => $order->paid,
            ],
        ]);
    }

    /**
     * Log order cancelled
     */
    public function logCancelled(Order $order, ?string $reason = null): OrderActivity
    {
        return OrderActivity::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'activity_type' => OrderActivity::TYPE_CANCELLED,
            'description' => 'Order cancelled',
            'notes' => $reason,
            'old_values' => ['status' => $order->getOriginal('status')],
            'new_values' => ['status' => 'cancelled'],
        ]);
    }

    /**
     * Log order completed
     */
    public function logCompleted(Order $order): OrderActivity
    {
        return OrderActivity::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'activity_type' => OrderActivity::TYPE_COMPLETED,
            'description' => 'Order completed',
            'old_values' => ['status' => $order->getOriginal('status')],
            'new_values' => ['status' => 'completed'],
        ]);
    }

    /**
     * Generate human-readable update description
     */
    private function generateUpdateDescription(array $changes): string
    {
        if (empty($changes)) {
            return 'Order updated';
        }

        $descriptions = [];
        foreach ($changes as $field => $change) {
            $fieldName = ucfirst(str_replace('_', ' ', $field));
            $descriptions[] = "{$fieldName} changed";
        }

        return 'Updated: ' . implode(', ', $descriptions);
    }

    /**
     * Get all activities for an order
     */
    public function getOrderActivities(int $orderId)
    {
        return OrderActivity::where('order_id', $orderId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
