<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;


use App\Services\StoreCodeGenerator;



class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::latest()
            ->paginate(20);

        return view('pages.store.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nextSerial = (Store::withTrashed()->max('id') ?? 0) + 1;
        return view('pages.store.create', compact('nextSerial'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1️⃣ Validate request
        $validated = $request->validate([
            // Store fields
            'name'                  => 'required|string|max:255',
            'contact_phone'         => 'nullable|string|max:15',
            'contact_email'         => 'nullable|email',
            'address'               => 'required|string',
            'latitude'              => 'required|numeric|between:-90,90',
            'longitude'             => 'required|numeric|between:-180,180',
            'delivery_radius_km'    => 'required|numeric|min:1|max:50',

            'opening_time'          => 'nullable|date_format:H:i',
            'closing_time'          => 'nullable|date_format:H:i',

            'max_orders_per_slot'   => 'nullable|integer|min:1',
            'order_cutoff_minutes' => 'nullable|integer|min:1',

            'daily_cash_limit'      => 'nullable|numeric|min:0',

            'is_active'             => 'nullable|boolean',
            'accepting_orders'      => 'nullable|boolean',

            // Manager (conditional)
            'assign_manager'        => 'nullable|boolean',
            'manager_name'          => 'required_if:assign_manager,1|string|max:255',
            'manager_email'         => 'required_if:assign_manager,1|email|unique:users,email',
            'manager_phone'         => 'nullable|string|max:15',
            'manager_password'      => 'required_if:assign_manager,1|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            // 2️⃣ Create Store
            $store = Store::create([
                'name'                  => $validated['name'],
                'code'                  => StoreCodeGenerator::generate($validated['name']),
                'contact_phone'         => $validated['contact_phone'] ?? null,
                'contact_email'         => $validated['contact_email'] ?? null,

                'address'               => $validated['address'],
                'latitude'              => $validated['latitude'],
                'longitude'             => $validated['longitude'],
                'delivery_radius_km'    => $validated['delivery_radius_km'],

                'opening_time'          => $validated['opening_time'] ?? null,
                'closing_time'          => $validated['closing_time'] ?? null,
                'max_orders_per_slot'   => $validated['max_orders_per_slot'] ?? null,
                'order_cutoff_minutes'  => $validated['order_cutoff_minutes'] ?? 5,

                'daily_cash_limit'      => $validated['daily_cash_limit'] ?? null,
                'pending_cash_amount'   => 0,

                'is_active'             => $validated['is_active'] ?? true,
                'is_open'               => false, // store opens explicitly
                'accepting_orders'      => $validated['accepting_orders'] ?? true,
            ]);

            // 3️⃣ Create Store Manager (optional)
            if ($request->boolean('assign_manager')) {

                $manager = User::create([
                    'name'      => $validated['manager_name'],
                    'email'     => $validated['manager_email'],
                    'phone'     => $validated['manager_phone'] ?? null,
                    'password'  => Hash::make($validated['manager_password']),
                    'store_id'  => $store->id,
                    'is_active' => true,
                ]);
                

                // Assign role (Spatie)
                $manager->assignRole('store_manager');

                // Link manager to store
                $store->update([
                    'manager_id' => $manager->id,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('stores.index')
                ->with('success', 'Store created successfully.');

        } catch (\Throwable $e) {

            DB::rollBack();

            // 4️⃣ Log error for debugging / audits
            Log::error('Store creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the store. Please try again.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $store = Store::with('manager')->findOrFail($id);
        return view('pages.store.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $store = Store::with('manager')->findOrFail($id);
        return view('pages.store.create', compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $store = Store::findOrFail($id);

        $validated = $request->validate([

            // Store fields
            'name'                  => 'required|string|max:255',
            'contact_phone'         => 'nullable|string|max:15',
            'contact_email'         => 'nullable|email',
            'address'               => 'required|string',
            'latitude'              => 'required|numeric|between:-90,90',
            'longitude'             => 'required|numeric|between:-180,180',
            'delivery_radius_km'    => 'required|numeric|min:1|max:50',

            'opening_time'          => 'nullable|date_format:H:i',
            'closing_time'          => 'nullable|date_format:H:i',

            'max_orders_per_slot'   => 'nullable|integer|min:1',
            'order_cutoff_minutes' => 'nullable|integer|min:1',

            'daily_cash_limit'      => 'nullable|numeric|min:0',

            'is_active'             => 'nullable|boolean',
            'accepting_orders'      => 'nullable|boolean',

            // Manager handling
            'assign_manager'        => 'nullable|boolean',

            'manager_name'          => 'required_if:assign_manager,1|string|max:255',
            'manager_email'         => [
                'required_if:assign_manager,1',
                'email',
                Rule::unique('users', 'email')->ignore($store->manager_id),
            ],
            'manager_phone'         => 'nullable|string|max:15',
            'manager_password'      => 'nullable|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            // 2️⃣ Update Store
            $store->update([
                'name'                  => $validated['name'],
                'contact_phone'         => $validated['contact_phone'] ?? null,
                'contact_email'         => $validated['contact_email'] ?? null,

                'address'               => $validated['address'],
                'latitude'              => $validated['latitude'],
                'longitude'             => $validated['longitude'],
                'delivery_radius_km'    => $validated['delivery_radius_km'],

                'opening_time'          => $validated['opening_time'] ?? null,
                'closing_time'          => $validated['closing_time'] ?? null,
                'max_orders_per_slot'   => $validated['max_orders_per_slot'] ?? null,
                'order_cutoff_minutes'  => $validated['order_cutoff_minutes'] ?? 5,

                'daily_cash_limit'      => $validated['daily_cash_limit'] ?? null,

                'is_active'             => $validated['is_active'] ?? $store->is_active,
                'accepting_orders'      => $validated['accepting_orders'] ?? $store->accepting_orders,
            ]);

            /**
             * 3️⃣ MANAGER LOGIC
             */
            if ($request->boolean('assign_manager')) {

                if ($store->manager_id) {
                    // Update existing manager
                    $manager = User::findOrFail($store->manager_id);

                    $manager->update([
                        'name'  => $validated['manager_name'],
                        'email' => $validated['manager_email'],
                        'phone' => $validated['manager_phone'] ?? null,
                    ]);

                    // Update password only if provided
                    if (!empty($validated['manager_password'])) {
                        $manager->update([
                            'password' => Hash::make($validated['manager_password']),
                        ]);
                    }

                } else {
                    // Create new manager
                    $manager = User::create([
                        'name'      => $validated['manager_name'],
                        'email'     => $validated['manager_email'],
                        'phone'     => $validated['manager_phone'] ?? null,
                        'password'  => Hash::make($validated['manager_password']),
                        'store_id'  => $store->id,
                        'is_active' => true,
                    ]);

                    $manager->assignRole('store_manager');

                    $store->update([
                        'manager_id' => $manager->id,
                    ]);
                }

            } else {
                /**
                 * Remove manager assignment (soft behavior)
                 * We DO NOT delete the user
                 */
                if ($store->manager_id) {
                    $manager = User::find($store->manager_id);

                    if ($manager) {
                        $manager->update([
                            'store_id' => null,
                        ]);
                    }

                    $store->update([
                        'manager_id' => null,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('stores.index')
                ->with('success', 'Store updated successfully.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Store update failed', [
                'store_id' => $store->id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the store.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
