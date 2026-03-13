<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderAssignmentService
{
    // public function assignOrder($orderId)
    // {
    //     $order = Order::findOrFail($orderId);

    //     $storeLat = $order->store_lat;  //18.5204300
    //     $storeLng = $order->store_lng; //73.8567430

    //     // Haversine Formula
    //     $nearestDriver = User::join('delivery_agents', 'users.id', '=', 'delivery_agents.user_id')
    //         ->join('agent_locations', 'users.id', '=', 'agent_locations.agent_id')
    //         ->select(
    //             'users.*',
    //             DB::raw("
    //         (6371 * acos(
    //             cos(radians($storeLat)) *
    //             cos(radians(agent_locations.latitude)) *
    //             cos(radians(agent_locations.longitude) - radians($storeLng)) +
    //             sin(radians($storeLat)) *
    //             sin(radians(agent_locations.latitude))
    //         )) AS distance
    //     ")
    //         )
    //         ->where('users.role', 3)   // driver role
    //         ->where('delivery_agents.is_available', 1)
    //         ->whereNull('delivery_agents.current_order_id')
    //         ->orderBy('distance', 'asc')
    //         ->first();

    //     if (!$nearestDriver) {
    //         return ['status' => false, 'message' => 'No driver available'];
    //     }

    //     // Assign order
    //     $order->update([
    //         'agent_id' => $nearestDriver->id,
    //         'status' => 'assigned'
    //     ]);

    //     $nearestDriver->update([
    //         'current_order_id' => $order->id,
    //         'is_available' => 0
    //     ]);

    //     return [
    //         'status' => true,
    //         'message' => 'Order Assigned',
    //         'driver_id' => $nearestDriver->id
    //     ];
    // }

    public function assignOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        $storeLat = $order->store_lat;
        $storeLng = $order->store_lng;

        $nearestDriver = User::join('delivery_agents', 'users.id', '=', 'delivery_agents.user_id')
            ->join('agent_locations', 'delivery_agents.user_id', '=', 'agent_locations.agent_id')
            ->select(
                'users.*',
                DB::raw("
                (6371 * ACOS(
                    LEAST(1,
                        COS(RADIANS($storeLat)) *
                        COS(RADIANS(agent_locations.latitude)) *
                        COS(RADIANS(agent_locations.longitude) - RADIANS($storeLng)) +
                        SIN(RADIANS($storeLat)) *
                        SIN(RADIANS(agent_locations.latitude))
                    )
                )) AS distance
            ")
            )
            ->where('users.role', 3)
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

        // Update delivery_agents table (IMPORTANT FIX)
        DB::table('delivery_agents')
            ->where('user_id', $nearestDriver->id)
            ->update([
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
