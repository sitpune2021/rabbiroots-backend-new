<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Requests\DeliveryAgentRequest;
use App\Models\AgentLocation;
use App\Models\Order;
use App\Models\OrderStatusLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class DeliveryAgentController extends Controller
{

    // goOnline & goOffline API
    public function updateOnlineStatus(Request $request)
    {

        try {

            $agent = auth()->user();

            Log::info('Update online status API called', [
                'user_id' => $agent?->id,
                'role' => $agent?->role,
                'request_data' => $request->all()
            ]);

            if (!$agent) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // ✅ Allow only delivery agents (change role_id accordingly)
            if ($agent->role != 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Only delivery agents can update status'
                ], 403);
            }

            $request->validate([
                'status' => 'required|in:online,offline'
            ]);

            $isOnline = $request->status === 'online' ? 1 : 0;

            $agent->deliveryAgent()->update([
                'is_online' => $isOnline,
                'is_available' => $isOnline
            ]);

            Log::info('Agent status updated successfully', [
                'agent_id' => $agent->id,
                'new_status' => $request->status
            ]);

            return response()->json([
                'status' => true,
                'message' => $request->status === 'online'
                    ? 'Agent is now online'
                    : 'Agent is now offline'
            ]);
        } catch (\Exception $e) {

            Log::error('Failed to update agent status', [
                'error_message' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function updateLocation(Request $request)
    {
        Log::info('Update location API called', [
            'request_data' => $request->all()
        ]);

        try {

            $request->validate([
                'latitude' => 'required',
                'longitude' => 'required',
                'battery_percentage' => 'required'
            ]);

            Log::info('Validation passed');

            $agent = auth()->user();

            if (!$agent) {
                Log::warning('Agent not authenticated');
                return response()->json([
                    'status' => false,
                    'message' => 'Agent not logged in'
                ], 401);
            }

            Log::info('Agent authenticated', [
                'agent_id' => $agent->id,
                'name' => $agent->name ?? null
            ]);

            $location = AgentLocation::create([
                'agent_id' => $agent->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'battery_percentage' => $request->battery_percentage
            ]);

            Log::info('Location saved successfully', [
                'location_id' => $location->id,
                'agent_id' => $agent->id
            ]);

            // Block if battery < 15%
            if ($request->battery_percentage < 15) {

                $agent->update(['is_available' => false]);

                Log::warning('Agent blocked due to low battery', [
                    'agent_id' => $agent->id,
                    'battery' => $request->battery_percentage
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Location updated'
            ]);
        } catch (\Exception $e) {

            Log::error('Update location failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    // Assign Logic (Service Layer Recommended)
    public function assignOrder($orderId)
    {
        $order = Order::find($orderId);

        $agents = User::where('role', 'driver')
            ->where('is_online', true)
            ->where('is_available', true)
            ->get();

        $nearestAgent = null;
        $minDistance = 9999;

        foreach ($agents as $agent) {

            $location = AgentLocation::where('agent_id', $agent->id)
                ->latest()
                ->first();

            if (!$location) continue;

            $distance = $this->calculateDistance(
                $order->store_lat,
                $order->store_lng,
                $location->latitude,
                $location->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestAgent = $agent;
            }
        }

        if ($nearestAgent) {
            $order->update([
                'agent_id' => $nearestAgent->id,
                'status' => 'assigned'
            ]);

            $nearestAgent->update([
                'is_available' => false,
                'current_order_id' => $order->id
            ]);
        }

        return true;
    }

    // Battery level must be sent from mobile app to backend.
    public function updateBattery(Request $request)
    {
        $request->validate([
            'battery_level' => 'required|integer|min:0|max:100'
        ]);

        $agent = auth()->user();

        $agent->battery_level = $request->battery_level;
        $agent->battery_updated_at = now();
  
        // dd($agent->battery_updated_at->diffInMinutes(now()));
        // If battery < 15%, block order acceptance
        if ($request->battery_level < 15) {
            $agent->can_accept_orders = false;
        } else {
            $agent->can_accept_orders = true;
        }

        $agent->save();

        return response()->json([
            'message' => 'Battery updated successfully'
        ]);
    }

    public function acceptOrder(Request $request)
    {

        Log::info('Accept Order API Hit', [
            'request_data' => $request->all(),
            'agent_id' => auth()->id()
        ]);

        $agent = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | 🔋 Battery Check Start
        |--------------------------------------------------------------------------
        */

        // 1️⃣ Check if battery data exists
        if (is_null($agent->battery_level) || is_null($agent->battery_updated_at)) {

            Log::warning('Battery data missing', [
                'agent_id' => $agent->id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Battery status not available. Please refresh app.'
            ], 403);
        }

        // 2️⃣ Check if battery update is older than 10 minutes (anti-cheat protection)
        if ($agent->battery_updated_at->addMinutes(10)->isPast()) {
            Log::warning('Battery status outdated', [
                'agent_id' => $agent->id,
                'battery_updated_at' => $agent->battery_level
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Battery status outdated. Please refresh app.'
            ], 403);
        }

        // 3️⃣ Block if battery < 15%
        if ($agent->battery_level < 15) {

            Log::warning('Battery too low for order acceptance', [
                'agent_id' => $agent->id,
                'battery_level' => $agent->battery_level
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Battery too low. Please charge your phone to accept orders.'
            ], 403);
        }

        /*
    |--------------------------------------------------------------------------
    | 🔋 Battery Check End
    |--------------------------------------------------------------------------
    */

        $order = Order::find($request->order_id);

        if (!$order) {
            Log::error('Order not found', [
                'order_id' => $request->order_id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        Log::info('Order Found', [
            'order_id' => $order->id,
            'current_status' => $order->status
        ]);

        if ($order->status != 'assigned') {
            Log::warning('Invalid order status', [
                'order_id' => $order->id,
                'status' => $order->status
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Invalid order status'
            ], 400);
        }

        $order->update(['status' => 'accepted']);

        Log::info('Order Accepted', [
            'order_id' => $order->id,
            'accepted_by' => auth()->id()
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'accepted',
            'updated_by' => auth()->id()
        ]);

        Log::info('OrderStatusLog Inserted', [
            'order_id' => $order->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order accepted'
        ]);
    }

    public function rejectOrder(Request $request)
    {
        Log::info('Reject Order API Hit', [
            'request_data' => $request->all(),
            'agent_id' => auth()->id()
        ]);

        try {
            $request->validate(['order_id' => 'required']);

            $agent = auth()->user();

            Log::info('Agent Found', [
                'agent_id' => $agent->id,
                'current_order_id' => $agent->current_order_id
            ]);

            $order = Order::find($request->order_id);

            if (!$order) {
                Log::error('Order Not Found', [
                    'order_id' => $request->order_id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            Log::info('Order Found', [
                'order_id' => $order->id,
                'status' => $order->status,
                'assigned_agent' => $order->agent_id
            ]);

            // Optional check → Only assigned driver can reject
            if ($order->agent_id != $agent->id) {
                Log::warning('Unauthorized Reject Attempt', [
                    'order_id' => $order->id,
                    'agent_id' => $agent->id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'You are not assigned to this order'
                ], 403);
            }

            // Update Order
            $order->update([
                'agent_id' => null,
                'status' => 'placed'
            ]);

            Log::info('Order Reset To Placed', [
                'order_id' => $order->id
            ]);

            // Update Driver
            $agent->update([
                'current_order_id' => null,
                'is_available' => 1
            ]);

            Log::info('Driver Updated After Reject', [
                'agent_id' => $agent->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Order Rejected Successfully'
            ]);
        } catch (\Exception $e) {

            Log::error('Reject Order Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    // UPDATE ORDER STATUS (PICKED → OUT_FOR_DELIVERY → DELIVERED)
    public function updateOrderStatus(Request $request)
    {
        Log::info('Update Order Status API Hit', [
            'request_data' => $request->all(),
            'agent_id' => auth()->id()
        ]);

        try {
            $request->validate([
                'order_id' => 'required',
                'status' => 'required'
            ]);

            $allowed = [
                'picked',
                'out_for_delivery',
                'delivered',
                'delivery_attempted'
            ];

            if (!in_array($request->status, $allowed)) {

                Log::warning('Invalid Order Status Attempt', [
                    'status' => $request->status,
                    'order_id' => $request->order_id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid status'
                ], 400);
            }

            $agent = auth()->user();

            Log::info('Agent Found', [
                'agent_id' => $agent->id,
                'current_order_id' => $agent->current_order_id
            ]);

            $order = Order::find($request->order_id);

            if (!$order) {

                Log::error('Order Not Found', [
                    'order_id' => $request->order_id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            Log::info('Order Found', [
                'order_id' => $order->id,
                'old_status' => $order->status,
                'agent_assigned' => $order->agent_id
            ]);

            // Optional → check agent assigned to this order
            if ($order->agent_id != $agent->id) {

                Log::warning('Unauthorized Status Update Attempt', [
                    'order_id' => $order->id,
                    'agent_id' => $agent->id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'You are not assigned to this order'
                ], 403);
            }

            // Update Order Status
            $order->update([
                'status' => $request->status
            ]);

            Log::info('Order Status Updated', [
                'order_id' => $order->id,
                'new_status' => $request->status
            ]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $request->status,
                'updated_by' => $agent->id
            ]);

            Log::info('Order Status Log Created', [
                'order_id' => $order->id
            ]);

            // If Delivered → Free Agent
            if ($request->status == 'delivered') {

                $agent->update([
                    'is_available' => true,
                    'current_order_id' => null
                ]);

                Log::info('Agent Freed After Delivery', [
                    'agent_id' => $agent->id
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Order status updated'
            ]);
        } catch (\Exception $e) {

            Log::error('Update Order Status Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    // get profile details
    public function profile()
    {
        try {

            Log::info('Profile API Hit', [
                'user_id' => auth()->id()
            ]);

            $user = auth()->user()->load('deliveryAgent.location');

            if (!$user->deliveryAgent) {

                Log::warning('Agent profile not found', [
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Agent profile not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'vehicle' => $user->deliveryAgent,
                    'location' => $user->deliveryAgent->location
                ]
            ]);
        } catch (\Exception $e) {

            Log::error('Profile API Failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }


    // Start Delivery Attempt API - Start Delivery Attempt API
    public function startAttempt($orderId)
    {
        try {
            Log::info('Start delivery attempt called', [
                'order_id' => $orderId,
                'agent_id' => auth()->id()
            ]);

            $order = Order::findOrFail($orderId);

            $order->delivery_attempt_started_at = now();
            $order->save();

            Log::info('Delivery attempt started successfully', [
                'order_id' => $order->id,
                'started_at' => $order->delivery_attempt_started_at
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Delivery attempt started'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            Log::warning('Order not found while starting attempt', [
                'order_id' => $orderId,
                'agent_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {

            Log::error('Start delivery attempt failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'order_id' => $orderId,
                'agent_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    // Call Attempt API
    public function callCustomer(Request $request, $orderId)
    {
        try {

            Log::info('Call customer attempt triggered', [
                'order_id' => $orderId,
                'type' => $request->type,
                'agent_id' => auth()->id()
            ]);

            // ✅ Validate type
            $request->validate([
                'type' => 'required|in:primary,secondary'
            ]);

            $order = Order::findOrFail($orderId);

            // ❗ Ensure attempt started
            if (!$order->delivery_attempt_started_at) {
                return response()->json([
                    'status' => false,
                    'message' => 'Delivery attempt not started yet'
                ], 400);
            }

            // ❗ Check 10-minute limit
            if (now()->diffInMinutes($order->delivery_attempt_started_at) >= 10) {
                $order->status = 'delivery_attempted';
                $order->save();

                return response()->json([
                    'status' => false,
                    'message' => 'Delivery attempt time expired'
                ], 400);
            }
            $type = $request->type;

            if ($type === 'primary') {

                $order->primary_attempt_count += 1;

                if ($order->primary_attempt_count >= 3 && !$order->sms_sent) {

                    Log::warning('Primary attempts reached 3. Sending SMS fallback.', [
                        'order_id' => $order->id
                    ]);

                    // $this->sendSmsFallback($order);  //when twilio or any gateway provided then can use it
                    $order->sms_sent = true;

                    Log::info('SMS fallback sent', [
                        'order_id' => $order->id
                    ]);
                }
            }

            if ($type === 'secondary') {

                // ❗ Block secondary until 3 primary attempts
                if ($order->primary_attempt_count < 3) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Secondary number allowed only after 3 primary attempts'
                    ], 400);
                }

                $order->secondary_attempt_count += 1;

                Log::info('Secondary attempt incremented', [
                    'order_id' => $order->id,
                    'secondary_attempt_count' => $order->secondary_attempt_count
                ]);
            }

            // ✅ Save once (better performance)
            $order->save();

            return response()->json([
                'status' => true,
                'primary_attempts' => $order->primary_attempt_count,
                'secondary_attempts' => $order->secondary_attempt_count
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            Log::warning('Order not found while calling customer', [
                'order_id' => $orderId,
                'agent_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {

            Log::error('Call customer API failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'order_id' => $orderId,
                'agent_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
    public function customerAnswered($orderId)
    {
        try {

            Log::info('Customer answered. Resetting attempts.', [
                'order_id' => $orderId,
                'agent_id' => auth()->id()
            ]);

            $order = Order::findOrFail($orderId);

            // Reset values
            $order->primary_attempt_count = 0;
            $order->secondary_attempt_count = 0;
            $order->delivery_attempt_started_at = null;

            $order->save();

            Log::info('Attempts reset successfully', [
                'order_id' => $order->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Attempts reset'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            Log::warning('Order not found while resetting attempts', [
                'order_id' => $orderId,
                'agent_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {

            Log::error('CustomerAnswered API failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'order_id' => $orderId,
                'agent_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {}


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

    public function store() {}
}
