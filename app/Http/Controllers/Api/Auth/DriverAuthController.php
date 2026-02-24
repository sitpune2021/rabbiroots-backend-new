<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Http\Requests\DeliveryAgentRequest;
use App\Models\DeliveryAgent;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DriverAuthController extends Controller
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

    public function logout(Request $request)
    {
        try {

            $user = auth()->user();

            Log::info('Logout Request', [
                'user_id' => $user->id,
                'role_id' => $user->role_id
            ]);

            // If Driver → Make Offline
            if ($user->role_id == 3) {   // your driver role_id

                DB::table('delivery_agents')
                    ->where('user_id', $user->id)
                    ->update([
                        'is_online' => 0,
                        'current_order_id' => null,
                        'is_available' => 0
                    ]);

                Log::info('Driver set offline', [
                    'user_id' => $user->id
                ]);
            }

            // Delete Current Token
            $request->user()->currentAccessToken()->delete();

            Log::info('User Logged Out Successfully', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {

            Log::error('Logout Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Logout failed'
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        Log::info('Forgot Password API Hit', $request->all());

        $request->validate([
            'phone' => [
                'required',
                'regex:/^[6-9]\d{9}$/',
                'exists:users,phone'
            ]
        ]);

        $user = User::where('phone', $request->phone)->first();

        $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5)
        ]);

        Log::info('OTP Generated', [
            'user_id' => $user->id,
            'otp' => $otp
        ]);

        // 👉 Send SMS using Twilio
        // Twilio::sendSMS($user->phone, "Your reset OTP is $otp");

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your phone'
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
     * Register delivery agent API
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
