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
        $agentId = auth()->id();

        $result = $service->assignOrder($id, $agentId);

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


    // Get All New Orders api & get single new order data
    public function getNewOrders(Request $request)
    {
        try {

            Log::info('Fetching new orders list', [
                'requested_id' => $request->id
            ]);

            // If ID is passed → return single order
            if ($request->id) {

                $order = Order::where('id', $request->id)
                    ->where('status', 'placed')
                    ->first();

                if (!$order) {

                    Log::warning('Order not found or not in placed status', [
                        'order_id' => $request->id
                    ]);

                    return response()->json([
                        'status' => false,
                        'message' => 'Order not found'
                    ], 404);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Order fetched successfully',
                    'data' => $order
                ], 200);
            }

            // If no ID → return all placed orders
            $orders = Order::where('status', 'placed')
                ->latest()
                ->get();

            if ($orders->isEmpty()) {

                Log::info('No new orders found');

                return response()->json([
                    'status' => true,
                    'message' => 'No new orders found',
                    'data' => []
                ], 200);
            }

            Log::info('New orders fetched successfully', [
                'total_orders' => $orders->count()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'New orders fetched successfully',
                'data' => $orders
            ], 200);
        } catch (\Throwable $e) {

            Log::error('Error while fetching new orders', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching orders'
            ], 500);
        }
    }

    // Get All cancelled Orders api
    public function getCancelledOrders(Request $request, $id = null)
    {
        try {

            Log::info('Fetching cancelled orders', [
                'requested_id' => $id
            ]);

            // ✅ If ID passed → return single cancelled order
            if ($id) {

                $order = Order::where('id', $id)
                    ->where('status', 'cancelled')
                    ->first();

                if (!$order) {

                    Log::warning('Cancelled order not found', [
                        'order_id' => $id
                    ]);

                    return response()->json([
                        'status' => false,
                        'message' => 'Cancelled order not found'
                    ], 404);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Cancelled order fetched successfully',
                    'data' => $order
                ], 200);
            }

            // ✅ If no ID → return all cancelled orders
            $orders = Order::where('status', 'cancelled')
                ->latest()
                ->get();

            if ($orders->isEmpty()) {

                Log::info('No cancelled orders found');

                return response()->json([
                    'status' => true,
                    'message' => 'No cancelled orders found',
                    'data' => []
                ], 200);
            }

            Log::info('Cancelled orders fetched successfully', [
                'total_orders' => $orders->count()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cancelled orders fetched successfully',
                'data' => $orders
            ], 200);
        } catch (\Throwable $e) {

            Log::error('Error while fetching cancelled orders', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching cancelled orders'
            ], 500);
        }
    }

    // Completed Orders API
    public function getCompletedOrders(Request $request, $id = null)
    {
        try {

            Log::info('Fetching completed orders', [
                'requested_id' => $id
            ]);

            // ✅ If ID provided → return single completed order
            if ($id) {

                $order = Order::where('id', $id)
                    ->where('status', 'completed')
                    ->first();

                if (!$order) {

                    Log::warning('Completed order not found', [
                        'order_id' => $id
                    ]);

                    return response()->json([
                        'status' => false,
                        'message' => 'Completed order not found'
                    ], 404);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Completed order fetched successfully',
                    'data' => $order
                ], 200);
            }

            // ✅ If no ID → return all completed orders
            $orders = Order::where('status', 'completed')
                ->latest()
                ->get();

            if ($orders->isEmpty()) {

                return response()->json([
                    'status' => true,
                    'message' => 'No completed orders found',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Completed orders fetched successfully',
                'data' => $orders
            ], 200);
        } catch (\Throwable $e) {

            Log::error('Error fetching completed orders', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    // Completed Orders API
    public function getPickedOrders(Request $request, $id = null)
    {
        try {

            Log::info('Fetching picked orders', [
                'requested_id' => $id
            ]);

            // ✅ If ID provided → return single picked order
            if ($id) {

                $order = Order::where('id', $id)
                    ->where('status', 'picked')
                    ->first();

                if (!$order) {

                    Log::warning('Picked order not found', [
                        'order_id' => $id
                    ]);

                    return response()->json([
                        'status' => false,
                        'message' => 'Picked order not found'
                    ], 404);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Picked order fetched successfully',
                    'data' => $order
                ], 200);
            }

            // ✅ If no ID → return all picked orders
            $orders = Order::where('status', 'picked')
                ->latest()
                ->get();

            if ($orders->isEmpty()) {

                return response()->json([
                    'status' => true,
                    'message' => 'No picked orders found',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Picked orders fetched successfully',
                'data' => $orders
            ], 200);
        } catch (\Throwable $e) {

            Log::error('Error fetching picked orders', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
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
