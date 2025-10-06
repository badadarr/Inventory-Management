<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order): void
    {
        $customer = $order->customer;
        
        if ($customer && $customer->status_customer === 'new') {
            $previousOrders = Order::where('customer_id', $customer->id)
                ->where('id', '!=', $order->id)
                ->count();
            
            if ($previousOrders > 0) {
                $customer->update(['status_customer' => 'repeat']);
            }
        }
    }
}
