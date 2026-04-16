<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerAuthController extends Controller
{
    /**
     * customer send otp for login
     */
    public function sendOtp(Request $request)
    {
        try {

            Log::info('Customer OTP request received', [
                'phone' => $request->phone
            ]);

            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits:10'
            ]);

            if ($validator->fails()) {

                Log::warning('OTP validation failed', [
                    'phone' => $request->phone,
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $otp = rand(100000, 999999);

            Log::info('Generated OTP for customer', [
                'phone' => $request->phone,
                'otp' => $otp // ⚠️ Remove in production
            ]);

            $user = User::where('phone', $request->phone)->first();
            
            if ($user->is_delete_requested == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Account scheduled for deletion'
                ], 403);
            }

            if (!$user) {

                Log::info('New customer creating for OTP login', [
                    'phone' => $request->phone
                ]);

                $user = User::create([
                    'phone'   => $request->phone,
                    'role_id' => 4, // customer
                ]);
            } else {

                Log::info('Existing customer found for OTP login', [
                    'user_id' => $user->id,
                    'phone' => $request->phone
                ]);
            }

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(5),
            ]);

            Log::info('OTP stored successfully in database', [
                'user_id' => $user->id,
                'expires_at' => $user->otp_expires_at
            ]);

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp // REMOVE IN PRODUCTION
            ]);
        } catch (\Exception $e) {

            Log::error('Customer OTP send failed', [
                'phone' => $request->phone ?? null,
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    /**
     * customer verify otp for login
     */
    public function verifyOtp(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits:10',
                'otp'    => 'required|digits:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::where('phone', $request->phone)
                ->where('otp', $request->otp)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ], 400);
            }

            if (Carbon::now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP expired'
                ], 400);
            }

            DB::beginTransaction();

            $user->update([
                'otp'  => null,
                'otp_expires_at' => null,
                'role' => 4
            ]);

            // Generate Sanctum Token
            $token = $user->createToken('customer_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Registration successful',
                'token' => $token,
                'data' => [
                    'id' => $user->id,
                    'phone' => $user->phone
                ]
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }



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
