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
use Illuminate\Support\Facades\Log;

class DeliveryAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $agents = DeliveryAgent::with('user')
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Delivery agents fetched successfully',
                'data' => $agents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
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


    // public function store(DeliveryAgentRequest $request)
    // {
    //     Log::info('Delivery Agent Registration Started', [
    //         'phone' => $request->phone ?? null,
    //         'email' => $request->email ?? null,
    //         'device_id' => $request->device_id,
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         // 1️⃣ Create User (Login credentials)

    //         $user = User::create([
    //             'name'      => $request->name,
    //             'phone'     => $request->phone, // ✅ phone, not mobile
    //             'email'     => $request->email,
    //             'password'  => Hash::make(Str::random(10)), // OTP-based login
    //             'is_active' => false, // admin approval later
    //         ]);

    //         Log::info('User created for delivery agent', [
    //             'user_id' => $user->id,
    //             'phone' => $user->phone,
    //         ]);

    //         // 2️⃣ Assign Spatie role
    //         $user->assignRole('delivery_agent');

    //         Log::info('Delivery agent role assigned', [
    //             'user_id' => $user->id,
    //             'role' => 'delivery_agent',
    //         ]);

    //         // 3️⃣ Create Delivery Agent Profile
    //         $agent = DeliveryAgent::create([
    //             'user_id' => $user->id,
    //             'rating_avg' => 5.0,
    //             'dead_phone_incidents' => 0,
    //             'is_available' => false,
    //         ]);

    //         Log::info('Delivery agent profile created', [
    //             'agent_id' => $agent->id,
    //             'user_id' => $user->id,
    //         ]);

    //         DB::commit();

    //         Log::info('Delivery Agent Registration Completed Successfully', [
    //             'user_id' => $user->id,
    //             'agent_id' => $agent->id,
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Registration successful. Awaiting approval.',
    //             'data' => [
    //                 'user_id' => $user->id,
    //                 'phone' => $user->phone,
    //             ]
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         Log::error('Delivery Agent Registration Failed', [
    //             'error_message' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'request_data' => $request->except(['password']),
    //         ]);

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Registration failed',
    //         ], 500);
    //     }
    // }

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
            // $documents = [];

            // if ($request->hasFile('driving_license_doc')) {
            //     $documents['driving_license_doc'] =
            //         $request->file('driving_license_doc')->store('delivery_agents/licenses', 'public');
            // }

            // if ($request->hasFile('vehicle_registration_doc')) {
            //     $documents['vehicle_registration_doc'] =
            //         $request->file('vehicle_registration_doc')->store('delivery_agents/vehicle_docs', 'public');
            // }

            // if ($request->hasFile('insurance_doc')) {
            //     $documents['insurance_doc'] =
            //         $request->file('insurance_doc')->store('delivery_agents/insurance', 'public');
            // }

            // if ($request->hasFile('aadhar_doc')) {
            //     $documents['aadhar_doc'] =
            //         $request->file('aadhar_doc')->store('delivery_agents/aadhar', 'public');
            // }

            // if ($request->hasFile('pan_doc')) {
            //     $documents['pan_doc'] =
            //         $request->file('pan_doc')->store('delivery_agents/pan', 'public');
            // }

            $documents = [];

            if ($request->hasFile('driving_license_doc')) {

                $file = $request->file('driving_license_doc');
                $fileName = $file->getClientOriginalName(); // prevent duplicate names

                $file->storeAs('delivery_agents/licenses', $fileName, 'public');

                $documents['driving_license_doc'] = $fileName;
            }

            if ($request->hasFile('vehicle_registration_doc')) {

                $file = $request->file('vehicle_registration_doc');
                $fileName =$file->getClientOriginalName();

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
                $fileName =$file->getClientOriginalName();

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
