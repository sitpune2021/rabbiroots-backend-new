<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AccountDeletionController extends Controller
{

    /**
     * Request Delete Account
     */

    public function requestDeletion(Request $request)
    {
        $user = $request->user();

        Log::info('🚀 Account deletion request started', [
            'user_id' => $user->id,
            'email'   => $user->email ?? null
        ]);

        try {

            // 🔍 Check if already requested
            $exists = AccountDeletion::where('user_id', $user->id)
                ->where('is_processed', false)
                ->exists();

            if ($exists) {

                Log::warning('⚠️ Deletion request already exists', [
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Deletion request already exists'
                ]);
            }

            // 📝 Create deletion request
            $deletion = AccountDeletion::create([
                'user_id' => $user->id,
                'reason' => $request->reason,
                'requested_at' => now(),
                'scheduled_delete_at' => now()->addDays(7),
                // 'scheduled_delete_at' => now()->addMinutes(1),

            ]);

            Log::info('✅ Deletion request created', [
                'user_id' => $user->id,
                'deletion_id' => $deletion->id,
                'scheduled_delete_at' => $deletion->scheduled_delete_at
            ]);

            // 🔒 Mark user as delete requested
            $user->update([
                'is_delete_requested' => true
            ]);

            // 🔐 Logout user
            $request->user()->currentAccessToken()->delete();

            Log::info('🔓 User logged out after deletion request', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Account deletion requested successfully',
                'data' => $deletion
            ]);
        } catch (\Throwable $e) {

            Log::error('❌ Account deletion request failed', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Send Message after Deletion of Account 
     */

    public function sendDeleteOtp(Request $request)
    {
        $user = $request->user();

        Log::info('🚀 Send Delete OTP started', [
            'user_id' => $user->id,
            'email'   => $user->email ?? null
        ]);

        try {

            // 🔢 Generate OTP
            $otp = rand(100000, 999999);

            Log::info('🔐 OTP generated', [
                'user_id' => $user->id
                // ⚠️ Do NOT log OTP in production
                // 'otp' => $otp
            ]);

            // 💾 Store in DB
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
                'otp_verified' => false
            ]);

            Log::info('💾 OTP stored in DB', [
                'user_id' => $user->id,
                'expires_at' => $user->otp_expires_at
            ]);

            // 📩 Send OTP (Twilio / Email)
            Log::info('📩 OTP sending triggered', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'OTP sent',
                'otp' => $otp // ⚠️ remove in production
            ]);
        } catch (\Throwable $e) {

            Log::error('❌ Failed to send delete OTP', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP'
            ]);
        }
    }


    /**
     * Verify Otp
     */
    public function verifyDeleteOtp(Request $request)
    {
        Log::info('🚀 Verify Delete OTP started', [
            'user_id' => $request->user()->id ?? null
        ]);

        try {

            // ✅ 1. Validate input
            $request->validate([
                'otp' => 'required|digits:6'
            ]);

            $user = $request->user();

            // ✅ 2. Check OTP exists
            if (empty($user->otp)) {

                Log::warning('⚠️ OTP not found', [
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'OTP not found. Please request again'
                ]);
            }

            // ✅ 3. Check OTP match
            if ($user->otp != $request->otp) {

                Log::warning('❌ Invalid OTP attempt', [
                    'user_id' => $user->id,
                    // ⚠️ Don't log OTP in production
                    // 'entered_otp' => $request->otp,
                    // 'stored_otp' => $user->otp
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ]);
            }

            // ✅ 4. Check expiry
            if (empty($user->otp_expires_at) || now()->gt($user->otp_expires_at)) {

                Log::warning('⏰ OTP expired', [
                    'user_id' => $user->id,
                    'expires_at' => $user->otp_expires_at
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'OTP expired'
                ]);
            }

            // ✅ 5. Mark verified + clear OTP
            $user->update([

                'otp_verified' => true,
                'otp' => null,
                'otp_expires_at' => null
            ]);

            Log::info('✅ OTP verified successfully', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully'
            ]);
        } catch (\Throwable $e) {

            Log::error('❌ OTP verification failed', [
                'user_id' => $request->user()->id ?? null,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /**
     * ❌ Cancel Deletion (Optional)
     */
    public function cancelDeletion(Request $request)
    {
        $user = $request->user();

        AccountDeletion::where('user_id', $user->id)
            ->where('is_processed', false)
            ->delete();

        $user->update([
            'is_delete_requested' => false,
            'otp_verified' => false
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Deletion cancelled'
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
    //     User clicks delete
    //         ↓
    // Send OTP
    //         ↓
    // Verify OTP
    //         ↓
    // Request Deletion
    //         ↓
    // Set is_delete_requested = 1
    //         ↓
    // User cannot login
    //         ↓
    // Wait 7 days
    //         ↓
    // CRON runs
    //         ↓
    // deleted_at filled (soft delete)
    //         ↓
    // Account gone

}
