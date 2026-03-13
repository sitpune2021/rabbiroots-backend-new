<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerAddressController extends Controller
{
    // add address api
    public function addAddress(Request $request)
    {
        try {

            Log::info('Add address request', [
                'user_id' => auth()->id()
            ]);

            $validator = Validator::make($request->all(), [
                'house_no' => 'required|string|max:255',
                'area' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'pincode' => 'required|digits:6',
                'latitude' => 'nullable',
                'longitude' => 'nullable'
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $address = Address::create([
                'user_id' => auth()->id(),
                'house_no' => $request->house_no,
                'area' => $request->area,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_default' => $request->is_default ?? 0
            ]);

            Log::info('Customer address added', [
                'address_id' => $address->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Address added successfully',
                'data' => $address
            ]);
        } catch (\Exception $e) {

            Log::error('Add address failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    //  // add address api list api
    public function addressList()
    {
        try {

            Log::info('Fetching customer address list', [
                'user_id' => auth()->id()
            ]);

            $addresses = Address::where('user_id', auth()->id())
                ->orderBy('is_default', 'desc')
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Address list fetched successfully',
                'data' => $addresses
            ]);
        } catch (\Exception $e) {

            Log::error('Address list fetch failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    // Update Addrdess
    public function updateAddress(Request $request)
    {
        try {

            Log::info('Update address request', [
                'user_id' => auth()->id(),
                'address_id' => $request->address_id
            ]);

            $validator = Validator::make($request->all(), [
                'address_id' => 'required|exists:addresses,id',
                'house_no' => 'required|string|max:255',
                'area' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'pincode' => 'required|digits:6',
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $address = Address::where('id', $request->address_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$address) {
                return response()->json([
                    'status' => false,
                    'message' => 'Address not found'
                ]);
            }

            $address->update([
                'house_no' => $request->house_no,
                'area' => $request->area,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            Log::info('Address updated successfully', [
                'address_id' => $address->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Address updated successfully',
                'data' => $address
            ]);
        } catch (\Exception $e) {

            Log::error('Update address failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function deleteAddress($id)
    {

        try {

            Log::info('Delete address request', [
                'user_id' => auth()->id(),
                'address_id' => $id
            ]);

            $address = Address::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$address) {

                return response()->json([
                    'status' => false,
                    'message' => 'Address not found'
                ]);
            }

            $address->delete();

            Log::info('Address deleted successfully', [
                'address_id' => $id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Address deleted successfully'
            ]);
        } catch (\Exception $e) {

            Log::error('Delete address failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
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
