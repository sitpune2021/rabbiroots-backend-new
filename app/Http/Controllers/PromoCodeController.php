<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.promo.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.promo.create');
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
        return view('pages.promo.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('pages.promo.edit');
    }

    /**
     * Update a resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove a resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
