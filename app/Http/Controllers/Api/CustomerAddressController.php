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
                'address_type' => 'required|in:home,work,hotel,other',
                'house_no' => 'required|string|max:255',
                'floor' => 'nullable|string|max:100',
                'area' => 'required|string|max:255',
                'landmark' => 'nullable|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'pincode' => 'required|digits:6',
                'name' => 'required|string|max:255',
                'phone' => 'nullable|digits:10',
                'is_default' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // ✅ Normalize input (avoid duplicate variations)
            $houseNo = strtolower(trim($request->house_no));
            $area = strtolower(trim($request->area));
            $city = strtolower(trim($request->city));
            $pincode = $request->pincode;

            // ✅ Duplicate Check (Soft)
            $exists = Address::where('user_id', auth()->id())
                ->whereRaw('LOWER(TRIM(house_no)) = ?', [$houseNo])
                ->whereRaw('LOWER(TRIM(area)) = ?', [$area])
                ->whereRaw('LOWER(TRIM(city)) = ?', [$city])
                ->where('pincode', $pincode)
                ->exists();

            if ($exists) {

                Log::warning('Duplicate address attempt blocked', [
                    'user_id' => auth()->id(),
                    'house_no' => $houseNo,
                    'area' => $area
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'This address already exists'
                ], 409);
            }

            // ✅ Only one default address per user
            if ($request->is_default == 1) {
                Address::where('user_id', auth()->id())
                    ->update(['is_default' => 0]);
            }

            $address = Address::create([
                'user_id' => auth()->id(),
                'address_type' => $request->address_type,
                'house_no' => $request->house_no,
                'floor' => $request->floor,
                'area' => $request->area,
                'landmark' => $request->landmark,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'name' => $request->name,
                'phone' => $request->phone,
                'is_default' => $request->is_default ?? 0
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

    /**
     * GET USER ADDRESSES
     */
    public function getAddresses()
    {
        $addresses = Address::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $addresses
        ]);
    }

    // Update Addrdess
    public function updateAddress(Request $request, $id)
    {
        try {

            Log::info('Update address request received', [
                'user_id' => auth()->id(),
                'address_id' => $id,
                'request_data' => $request->all()
            ]);

            $address = Address::where('user_id', auth()->id())->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'address_type' => 'required|in:home,work,hotel,other',
                'house_no' => 'required|string|max:255',
                'floor' => 'nullable|string|max:100',
                'area' => 'required|string|max:255',
                'landmark' => 'nullable|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'pincode' => 'required|digits:6',
                'name' => 'required|string|max:255',
                'phone' => 'nullable|digits:10',
                'is_default' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {

                Log::warning('Update address validation failed', [
                    'user_id' => auth()->id(),
                    'address_id' => $id,
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // ✅ Handle default address
            if ($request->is_default == 1) {

                Log::info('Updating default address for user', [
                    'user_id' => auth()->id()
                ]);

                Address::where('user_id', auth()->id())
                    ->update(['is_default' => 0]);
            }

            $address->update($request->all());

            Log::info('Address updated successfully', [
                'user_id' => auth()->id(),
                'address_id' => $address->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Address updated successfully',
                'data' => $address
            ]);
        } catch (\Exception $e) {

            Log::error('Update address failed', [
                'user_id' => auth()->id(),
                'address_id' => $id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
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


    public function setDefault($id)
    {
        Address::where('user_id', auth()->id())
            ->update(['is_default' => 0]);

        $address = Address::where('user_id', auth()->id())->findOrFail($id);
        $address->update(['is_default' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Default address set successfully'
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
}
