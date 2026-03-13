<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GofrugalController extends Controller
{
    public function getItems()
    {
        try {

            $baseUrl = env('GOFRUGAL_BASE_URL');
            $apiKey = env('GOFRUGAL_API_KEY');
            $storeId = env('GOFRUGAL_STORE_ID');

            $url = $baseUrl . "/items?store_id=" . $storeId;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: $apiKey",
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {

                $error = curl_error($curl);

                curl_close($curl);

                return response()->json([
                    'status' => false,
                    'message' => $error
                ]);
            }

            curl_close($curl);

            $result = json_decode($response, true);

            Log::info('GOFRUGAL Get Items API', [
                'response' => $result
            ]);

            return response()->json([
                'status' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {

            Log::error('GOFRUGAL API ERROR', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
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
