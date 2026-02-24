<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    // 
    public function agentOrderCount()
    {

        $agentId = Auth::id();

        $data = [
            'new_orders'       => Order::where('agent_id', $agentId)->where('status', 'placed')->count(),
            'picked_orders'    => Order::where('agent_id', $agentId)->where('status', 'picked')->count(),
            'completed_orders' => Order::where('agent_id', $agentId)->where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('agent_id', $agentId)->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * 
     * 
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
