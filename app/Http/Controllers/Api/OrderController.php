<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\OrderAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Assign order to delivery agent
    public function assign($id, OrderAssignmentService $service)
    {
        $result = $service->assignOrder($id);
        return response()->json($result);
    }

    // delivery delivered to customer
    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'status' => 'required'
        ]);

        $agent = auth()->user();

        $order = Order::where('id', $request->order_id)
            ->where('agent_id', $agent->id)
            ->firstOrFail();

        $order->update(['status' => $request->status]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'updated_by' => $agent->id
        ]);

        // free agent after delivery
        if ($request->status == 'delivered') {
            $agent->update([
                'current_order_id' => null,
                'is_available' => 1
            ]);
        }

        return response()->json(['message' => 'Status Updated']);
    }

      // Allow completion even if battery < 15%
    public function completeOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'battery_percentage' => 'required|numeric'
        ]);

        $agent = auth()->user();

        $order = Order::where('id', $request->order_id) //1
            ->where('agent_id', $agent->id) //3
            ->where('status', 'out_for_delivery')
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found or not assigned to you'
            ], 404);
        }

        // Allow completion even if battery < 15%
        if ($request->battery_percentage < 15) {
            Log::warning('Low battery but completing order', [
                'agent_id' => $agent->id,
                'order_id' => $order->id
            ]);
        }

        $order->update([
            'status' => 'delivered'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order delivered successfully'
        ]);
    }

    // deadphone detection - heartbeat

    public function heartbeat(Request $request)
    {
        $agent = auth()->user();

        $request->validate([
            'battery_percentage' => 'required|numeric'
        ]);

        $agent->update([
            'battery_percentage' => $request->battery_percentage,
            'last_seen_at' => now(),
            'is_online' => $request->battery_percentage > 0 ? 1 : 0
        ]);

        return response()->json([
            'status' => true
        ]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
