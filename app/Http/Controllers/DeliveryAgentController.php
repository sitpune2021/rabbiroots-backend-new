<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAgent;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agents = DeliveryAgent::with('user')->latest()->paginate(10);
        return view('pages.driver.index', compact('agents'));
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
        $agent = DeliveryAgent::with('user')->findOrFail($id);

        return view('pages.driver.show', compact('agent'));
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,on-leave,suspended'
        ]);

        try {
            $agent = DeliveryAgent::findOrFail($id);

            $agent->update([
                'status' => $request->status
            ]);

            return redirect()
                ->back()
                ->with('success', 'Driver status updated successfully.');
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', 'Something went wrong.');
        }
    }

    // delivery agent approval 
    public function approve($id)
    {
        Log::info('Driver approval process started', [
            'driver_id' => $id,
            'admin_id' => auth()->id() ?? null,
        ]);

        DB::beginTransaction();

        try {

            $agent = DeliveryAgent::with('user')->findOrFail($id);

            Log::info('Driver record fetched', [
                'driver_id' => $agent->id,
                'current_status' => $agent->status,
            ]);

            if ($agent->status !== 'pending') {

                Log::warning('Driver already processed', [
                    'driver_id' => $agent->id,
                    'status' => $agent->status,
                ]);

                return back()->with('error', 'Driver already processed.');
            }

            // Update delivery agent
            $agent->update([
                'status' => 'active',
                'is_available' => true,
            ]);

            Log::info('Driver status updated to active', [
                'driver_id' => $agent->id,
            ]);

            // Update user table
            $agent->user->update([
                'is_active' => true
            ]);

            Log::info('User account activated', [
                'user_id' => $agent->user->id,
            ]);

            DB::commit();

            Log::info('Driver approved successfully', [
                'driver_id' => $agent->id,
            ]);

            return back()->with('success', 'Driver approved successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Driver approval failed', [
                'driver_id' => $id,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Something went wrong while approving the driver.');
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
