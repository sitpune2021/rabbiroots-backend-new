<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function allOrders()
    {
        $orders = Order::with(['agent', 'customer'])
            ->latest()
            ->paginate(10);

        return view('pages.order.all', compact('orders'));
    }

    public function assignedOrders()
    {
        $orders = Order::with(['agent', 'customer'])
        ->where('status','assigned')
            ->whereNotNull('agent_id')
            ->latest()
            ->paginate(10);

        return view('pages.order.assigned', compact('orders'));
    }

    public function deliveredOrders()
    {
        $orders = Order::with(['agent', 'customer'])
            ->where('status', 'delivered')
            ->latest()
            ->paginate(10);

        return view('pages.order.delivered', compact('orders'));
    }

    // Agents assigned order list
    public function ordersByAgent($agentId)
    {
        $orders = Order::where('agent_id', $agentId)->get();

        return view('pages.driver.order-assigned.index', compact('orders'));
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
}
