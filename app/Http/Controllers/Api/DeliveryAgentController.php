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

class DeliveryAgentController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10'
        ]);

        try {

            $agent = DeliveryAgent::with('user')
                ->whereHas('user', function ($q) use ($request) {
                    $q->where('phone', $request->phone);
                })
                ->first();

            if (!$agent) {
                return response()->json([
                    'status' => false,
                    'message' => 'Driver not registered.'
                ], 404);
            }

            $otp = rand(100000, 999999);

            $agent->user->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(5),
            ]);

            // TODO: Integrate SMS gateway here

            Log::info("Driver OTP generated", [
                'driver_id' => $agent->id,
                'otp' => $otp
            ]);

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully.'
            ]);
        } catch (Exception $e) {

            Log::error("Send OTP failed", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required|digits:6',
            'device_id' => 'required|string'
        ]);

        DB::beginTransaction();

        try {

            $agent = DeliveryAgent::with('user')
                ->whereHas('user', function ($q) use ($request) {
                    $q->where('phone', $request->phone);
                })
                ->first();

            if (!$agent) {
                return response()->json([
                    'status' => false,
                    'message' => 'Driver not found.'
                ], 404);
            }

            $user = $agent->user;

            // ✅ Check OTP existence
            if (!$user->otp || !$user->otp_expires_at) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please request a new OTP.'
                ], 400);
            }

            // ✅ Check OTP match
            if ($user->otp != $request->otp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP.'
                ], 401);
            }

            // ✅ Check expiry safely
            if (now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP expired.'
                ], 401);
            }

            // ✅ Check approval
            if ($agent->status !== 'active') {
                return response()->json([
                    'status' => false,
                    'message' => 'Account not approved yet.'
                ], 403);
            }

            // ✅ Device binding (single device login)
            if ($user->device_id && $user->device_id !== $request->device_id) {
                Log::warning("Driver login from new device", [
                    'driver_id' => $agent->id,
                    'old_device' => $user->device_id,
                    'new_device' => $request->device_id
                ]);
            }

            // ✅ Update login data
            $user->update([
                'device_id' => $request->device_id,
                'otp' => null,
                'otp_expires_at' => null,
                'is_active' => true,
            ]);

            $agent->update([
                'last_login_at' => now()
            ]);

            // ✅ Create Sanctum token
            $token = $user->createToken('driver-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
                'token' => $token,
                'driver' => [
                    'id' => $agent->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'status' => $agent->status,
                    'is_available' => $agent->is_available
                ]
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error("Driver login failed", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Login failed.'
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
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'battery_percentage' => 'required'
        ]);

        $agent = auth()->user();

        AgentLocation::create([
            'agent_id' => $agent->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'battery_percentage' => $request->battery_percentage
        ]);

        // Block if battery < 15%
        if ($request->battery_percentage < 15) {
            $agent->update(['is_available' => false]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Location updated'
        ]);
    }


    // Haversine Formula (Distance Calculation)
    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
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

    // AGENT ACCEPT / REJECT ORDER
    // Accept Order
    public function acceptOrder(Request $request)
    {
        $order = Order::find($request->order_id);

        if ($order->status != 'assigned') {
            return response()->json(['message' => 'Invalid order'], 400);
        }

        $order->update([
            'status' => 'accepted'
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'accepted',
            'updated_by' => auth()->id()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order accepted'
        ]);
    }

    // UPDATE ORDER STATUS (PICKED → OUT_FOR_DELIVERY → DELIVERED)
    public function updateOrderStatus(Request $request)
    {
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
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $order = Order::find($request->order_id);

        $order->update([
            'status' => $request->status
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'updated_by' => auth()->id()
        ]);

        if ($request->status == 'delivered') {
            auth()->user()->update([
                'is_available' => true,
                'current_order_id' => null
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order status updated'
        ]);
    }
}
