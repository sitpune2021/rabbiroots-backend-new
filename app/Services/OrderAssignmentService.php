<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderAssignmentService
{
    public function assignOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        $storeLat = $order->store_lat;
        $storeLng = $order->store_lng;

        // Haversine Formula
        $nearestDriver = User::join('delivery_agents', 'users.id', '=', 'delivery_agents.user_id')
            ->join('agent_locations', 'users.id', '=', 'agent_locations.agent_id')
            ->select(
                'users.*',
                DB::raw("
            (6371 * acos(
                cos(radians($storeLat)) *
                cos(radians(agent_locations.latitude)) *
                cos(radians(agent_locations.longitude) - radians($storeLng)) +
                sin(radians($storeLat)) *
                sin(radians(agent_locations.latitude))
            )) AS distance
        ")
            )
            ->where('users.role', 3)   // driver role
            ->where('delivery_agents.is_available', 1)
            ->whereNull('delivery_agents.current_order_id')
            ->orderBy('distance', 'asc')
            ->first();

        if (!$nearestDriver) {
            return ['status' => false, 'message' => 'No driver available'];
        }

        // Assign order
        $order->update([
            'agent_id' => $nearestDriver->id,
            'status' => 'assigned'
        ]);

        $nearestDriver->update([
            'current_order_id' => $order->id,
            'is_available' => 0
        ]);

        return [
            'status' => true,
            'message' => 'Order Assigned',
            'driver_id' => $nearestDriver->id
        ];
    }
}
