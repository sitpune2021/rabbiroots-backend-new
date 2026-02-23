<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::latest()->paginate(20);
        return view('pages.brand.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';
        return view('pages.brand.create', compact('mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        Log::info('Brand store request started', [
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
        ]);

        $data = $request->validated();

        Log::debug('Brand validated data', $data);
        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            Log::info('Brand image upload detected', [
                'original_name' => $request->file('logo')->getClientOriginalName(),
                'size' => $request->file('logo')->getSize(),
            ]);
            $file = $request->file('logo');

            $data['logo'] = $file->storeAs(
                'brands',
                $file->getClientOriginalName(),
                'public'
            );

            Log::info('Brand image stored successfully', [
                'path' => $data['logo'],
            ]);
        } else {
            Log::warning('Brand store request without image');
        }
  
        $brand = Brand::create($data);

        Log::info('Brand created successfully', [
            'brand_id' => $brand->id,
            'name' => $brand->name ?? null,
        ]);

        return redirect()
            ->route('brand.index')
            ->with('success', 'Brand created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $mode = 'view';
        return view('pages.brand.create', compact('mode', 'brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand,)
    {
        $mode = 'edit';
        return view('pages.brand.create', compact('mode', 'brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        Log::info('Brand update request started', [
            'brand_id' => $brand->id,
            'ip'       => $request->ip(),
            'user_id'  => auth()->id(),
        ]);

        $data = $request->validated();

        Log::debug('Brand update validated data', $data);

        if ($request->hasFile('logo')) {
            Log::info('Brand image upload detected', [
                'original_name' => $request->file('logo')->getClientOriginalName(),
                'size' => $request->file('logo')->getSize(),
            ]);
            $file = $request->file('logo');

            $data['logo'] = $file->storeAs(
                'brands',
                $file->getClientOriginalName(),
                'public'
            );

            Log::info('Brand image stored successfully', [
                'path' => $data['logo'],
            ]);
        } else {
            Log::warning('Brand store request without image');
        }

        $brand->update($data);

        Log::info('Brand updated successfully', [
            'brand_id' => $brand->id,
            'name'     => $brand->name ?? null,
        ]);

        return redirect()
            ->route('brand.index')
            ->with('success', 'Brand updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
            Log::info('Brand delete request started', [
                'brand_id' => $brand->id,
                'user_id'  => auth()->id(),
            ]);

            $brand->delete(); // Soft delete

            Log::info('Brand deleted successfully', [
                'brand_id' => $brand->id,
            ]);

            return redirect()
                ->route('brand.index')
                ->with('success', 'Brand deleted successfully');
        } catch (\Exception $e) {

            Log::error('Brand delete failed', [
                'brand_id' => $brand->id ?? null,
                'error'    => $e->getMessage(),
            ]);

            return redirect()
                ->route('brand.index')
                ->with('error', 'Something went wrong while deleting the brand');
        }
    }
}
