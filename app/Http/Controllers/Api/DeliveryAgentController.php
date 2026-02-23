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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class DeliveryAgentController extends Controller
{
   

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

    public function store(DeliveryAgentRequest $request)
    {
        Log::info('Delivery Agent Registration Started', [
            'phone' => $request->phone ?? null,
            'email' => $request->email ?? null,
            'device_id' => $request->device_id,
        ]);

        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | 1️⃣ Create User
        |--------------------------------------------------------------------------
        */
            $user = User::create([
                'role' => 3,
                'name'      => $request->name,
                'phone'     => $request->phone,
                'email'     => $request->email,
                'password'  => Hash::make(Str::random(10)),
                'is_active' => false,
            ]);

            $user->assignRole('delivery_agent');

            /*
        |--------------------------------------------------------------------------
        | 2️⃣ Upload Documents
        |--------------------------------------------------------------------------
        */

            $documents = [];

            if ($request->hasFile('driving_license_doc')) {

                $file = $request->file('driving_license_doc');
                $fileName = $file->getClientOriginalName(); // prevent duplicate names

                $file->storeAs('delivery_agents/licenses', $fileName, 'public');

                $documents['driving_license_doc'] = $fileName;
            }

            if ($request->hasFile('vehicle_registration_doc')) {

                $file = $request->file('vehicle_registration_doc');
                $fileName = $file->getClientOriginalName();

                $file->storeAs('delivery_agents/vehicle_docs', $fileName, 'public');

                $documents['vehicle_registration_doc'] = $fileName;
            }

            if ($request->hasFile('insurance_doc')) {

                $file = $request->file('insurance_doc');
                $fileName = $file->getClientOriginalName();

                $file->storeAs('delivery_agents/insurance', $fileName, 'public');

                $documents['insurance_doc'] = $fileName;
            }

            if ($request->hasFile('aadhar_doc')) {

                $file = $request->file('aadhar_doc');
                $fileName = $file->getClientOriginalName();

                $file->storeAs('delivery_agents/aadhar', $fileName, 'public');

                $documents['aadhar_doc'] = $fileName;
            }

            if ($request->hasFile('pan_doc')) {

                $file = $request->file('pan_doc');
                $fileName = $file->getClientOriginalName();

                $file->storeAs('delivery_agents/pan', $fileName, 'public');

                $documents['pan_doc'] = $fileName;
            }


            /*
        |--------------------------------------------------------------------------
        | 3️⃣ Create Delivery Agent Profile
        |--------------------------------------------------------------------------
        */
            $agent = DeliveryAgent::create([

                // Relation
                'user_id' => $user->id,

                // Default system fields
                'rating_avg' => 5.0,
                'dead_phone_incidents' => 0,
                'is_available' => false,

                // Personal Details
                'dob' => $request->dob,
                'aadhar_number' => $request->aadhar_number,
                'pan_number' => $request->pan_number,
                'permanent_address' => $request->permanent_address,
                'temporary_address' => $request->temporary_address,

                // License Details
                'license_number' => $request->license_number,
                'license_type' => $request->license_type,
                'license_issue_date' => $request->license_issue_date,
                'license_expiry_date' => $request->license_expiry_date,

                // Vehicle Details
                'vehicle_type' => $request->vehicle_type,
                'vehicle_name' => $request->vehicle_name,
                'vehicle_model' => $request->vehicle_model,
                'vehicle_number' => $request->vehicle_number,
                'license_plate' => $request->license_plate,
                'vehicle_capacity' => $request->vehicle_capacity,
                'registration_number' => $request->registration_number,
                'insurance_policy_number' => $request->insurance_policy_number,

                // Documents
                'driving_license_doc' => $documents['driving_license_doc'] ?? null,
                'vehicle_registration_doc' => $documents['vehicle_registration_doc'] ?? null,
                'insurance_doc' => $documents['insurance_doc'] ?? null,
                'aadhar_doc' => $documents['aadhar_doc'] ?? null,
                'pan_doc' => $documents['pan_doc'] ?? null,

                // App Info
                'device_id' => $request->device_id,
                'app_version' => $request->app_version,

                'vendor_id' => $request->vendor_id,
            ]);

            DB::commit();

            Log::info('Delivery Agent Registration Completed', [
                'user_id' => $user->id,
                'agent_id' => $agent->id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Registration successful. Awaiting approval.',
                'data' => [
                    'user_id' => $user->id,
                    'phone' => $user->phone,
                ]
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Delivery Agent Registration Failed', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Registration failed',
            ], 500);
        }
    }


    // goOnline API
    public function goOnline(Request $request)
    {
        $agent = auth()->user();

        $agent->update([
            'is_online' => true,
            'is_available' => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent is now online'
        ]);
    }

    // goOffline API
    public function goOffline()
    {
        $agent = auth()->user();

        $agent->update([
            'is_online' => false,
            'is_available' => false
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent is now offline'
        ]);
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

    // Haversine Formula (Distance Calculation)
    // function calculateDistance($lat1, $lon1, $lat2, $lon2)
    // {
    //     $earthRadius = 6371;

    //     $dLat = deg2rad($lat2 - $lat1);
    //     $dLon = deg2rad($lon2 - $lon1);

    //     $a = sin($dLat / 2) * sin($dLat / 2) +
    //         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
    //         sin($dLon / 2) * sin($dLon / 2);

    //     $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    //     return $earthRadius * $c;
    // }



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

    // AGENT ACCEPT / REJECT ORDER
    // Accept Order
    // public function acceptOrder(Request $request)
    // {
    //     Log::info('Accept Order API Hit', [
    //         'request_data' => $request->all(),
    //         'agent_id' => auth()->id()
    //     ]);

    //     $order = Order::find($request->order_id);

    //     if (!$order) {
    //         Log::error('Order not found', [
    //             'order_id' => $request->order_id
    //         ]);

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Order not found'
    //         ], 404);
    //     }

    //     Log::info('Order Found', [
    //         'order_id' => $order->id,
    //         'current_status' => $order->status
    //     ]);

    //     if ($order->status != 'assigned') {
    //         Log::warning('Invalid order status', [
    //             'order_id' => $order->id,
    //             'status' => $order->status
    //         ]);

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid order status'
    //         ], 400);
    //     }

    //     $order->update(['status' => 'accepted']);

    //     Log::info('Order Accepted', [
    //         'order_id' => $order->id,
    //         'accepted_by' => auth()->id()
    //     ]);

    //     OrderStatusLog::create([
    //         'order_id' => $order->id,
    //         'status' => 'accepted',
    //         'updated_by' => auth()->id()
    //     ]);

    //     Log::info('OrderStatusLog Inserted', [
    //         'order_id' => $order->id
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Order accepted'
    //     ]);
    // }


    // Battery level must be sent from mobile app to backend.
    public function updateBattery(Request $request)
    {
        $request->validate([
            'battery_level' => 'required|integer|min:0|max:100'
        ]);

        $agent = auth()->user();

        $agent->battery_level = $request->battery_level;
        $agent->battery_updated_at = now();

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
        if ($agent->battery_updated_at->diffInMinutes(now()) > 10) {
            Log::warning('Battery status outdated', [
                'agent_id' => $agent->id,
                'battery_updated_at' => $agent->battery_updated_at
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
}
