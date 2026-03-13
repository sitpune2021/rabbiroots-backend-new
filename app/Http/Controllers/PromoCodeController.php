<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromoCodeRequest;
use Illuminate\Http\Request;
use App\Models\PromoCode;
use Illuminate\Support\Facades\Log;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $promoCodes = PromoCode::query()

            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            ->when($request->discount_type, function ($q) use ($request) {
                $q->where('discount_type', $request->discount_type);
            })

            ->when($request->store_type, function ($q) use ($request) {
                $q->where('store_type', $request->store_type);
            })

            ->when($request->device, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('device_web', $request->device === 'web')
                        ->orWhere('device_ios', $request->device === 'ios')
                        ->orWhere('device_android', $request->device === 'android');
                });
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.promo.index', compact('promoCodes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';
        return view('pages.promo.create', compact('mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PromoCodeRequest $request)
    {
        Log::info('PromoCode store request started', [
            'ip'      => $request->ip(),
            'user_id' => auth()->id(),
        ]);

        try {
            // Fetch request data
            $data = $request->all();

            Log::debug('PromoCode raw request data', $data);

            // Cast checkbox fields
            $data['new_users']          = $request->has('new_users') ? 1 : 0;
            $data['all_users']          = $request->has('all_users') ? 1 : 0;
            $data['device_web']         = $request->has('device_web') ? 1 : 0;
            $data['device_ios']         = $request->has('device_ios') ? 1 : 0;
            $data['device_android']     = $request->has('device_android') ? 1 : 0;
            $data['apply_rush_pricing'] = $request->has('apply_rush_pricing') ? 1 : 0;

            Log::debug('PromoCode checkbox-casted data', [
                'new_users'          => $data['new_users'],
                'all_users'          => $data['all_users'],
                'device_web'         => $data['device_web'],
                'device_ios'         => $data['device_ios'],
                'device_android'     => $data['device_android'],
                'apply_rush_pricing' => $data['apply_rush_pricing'],
            ]);

            // Handle stores
            if ($request->has('stores')) {
                $data['stores'] = $request->stores;

                Log::info('PromoCode store-wise mapping detected', [
                    'stores' => $data['stores'],
                ]);
            }

            // Create promo code
            $promo = PromoCode::create($data);

            Log::info('PromoCode created successfully', [
                'promo_id'   => $promo->id,
                'code_name'  => $promo->code_name ?? null,
                'status'     => $promo->status ?? null,
            ]);

            return redirect()
                ->route('promo.index')
                ->with('success', 'Promo created successfully');
        } catch (\Throwable $e) {

            Log::error('PromoCode creation failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'data'    => $request->all(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the promo code.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PromoCode $promo)
    {
        $mode = 'view';
        return view('pages.promo.create', compact('promo', 'mode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromoCode $promo)
    {
        $mode = 'edit';
        return view('pages.promo.create', compact('promo', 'mode'));
    }

    /**
     * Update a resource in storage.
     */
    public function update(PromoCodeRequest $request, PromoCode $promo)
    {
        Log::info('Promo update started', [
            'promo_id' => $promo->id,
            'request_data' => $request->except(['_token', '_method']),
            'updated_by' => auth()->id()
        ]);

        try {
            $data = $request->validated();

            // Handle checkbox fields
            foreach (
                [
                    'new_users',
                    'all_users',
                    'device_web',
                    'device_ios',
                    'device_android',
                    'apply_rush_pricing'
                ] as $field
            ) {
                $data[$field] = $request->has($field) ? 1 : 0;
            }

            Log::debug('Promo update data prepared', [
                'promo_id' => $promo->id,
                'data' => $data
            ]);

            $promo->update($data);

            Log::info('Promo updated successfully', [
                'promo_id' => $promo->id,
                'updated_by' => auth()->id()
            ]);

            return redirect()
                ->route('promo.index')
                ->with('success', 'Promo updated successfully');
        } catch (\Exception $e) {

            Log::error('Promo update failed', [
                'promo_id' => $promo->id ?? null,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'updated_by' => auth()->id()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the promo');
        }
    }
    /**
     * Remove a resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
