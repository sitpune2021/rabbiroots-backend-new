<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\ProductVariantImage;
use App\Models\ProductAttribute;
use App\Models\ProductCompliance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'variants']);

        // 🔍 Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        // 📌 Status Filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        $products = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.products.index', compact('products'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('level', 'sub')->orWhere('level', 'child')->with('parent')->latest()->get();
        $brands = Brand::all();

        return view('pages.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        Log::info('🚀 Product Store Request Started', ['data' => $request->all()]);

        // ============================
        // 1️⃣ VALIDATION
        // ============================

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'product_type' => ['required', Rule::in(['grocery', 'fresh', 'frozen', 'pharma', 'fashion', 'cosmetic'])],

            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|string|max:255',
            'variants.*.pack_size' => 'required|string|max:50',
            'variants.*.unit' => 'required|string|max:20',
            'variants.*.mrp' => 'required|numeric|min:0',
            'variants.*.selling_price' => 'required|numeric|min:0',
        ]);

        Log::info('✅ Validation Passed');

        DB::beginTransaction();

        try {

            // ============================
            // 3️⃣ CREATE PRODUCT
            // ============================

            $product = Product::create([
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'],
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'product_type' => $validated['product_type'],
                'is_active' => $request->boolean('is_active'),
                'status' => 'published',
            ]);

            Log::info('✅ Product Created', ['product_id' => $product->id]);

            // ============================
            // 4️⃣ PRODUCT IMAGES
            // ============================

            if ($request->hasFile('product_images')) {
                Log::info('📸 Uploading Product Images');

                foreach ($request->file('product_images') as $i => $image) {
                    $path = $image->store('products/images', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $path,
                        'is_primary' => $i === 0,
                        'sort_order' => $i,
                    ]);
                }

                Log::info('✅ Product Images Saved');
            }

            // ============================
            // 5️⃣ VARIANTS
            // ============================

            foreach ($validated['variants'] as $index => $variantData) {

                Log::info('🔄 Creating Variant', ['index' => $index]);

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'],
                    'pack_size' => $variantData['pack_size'],
                    'unit' => $variantData['unit'],
                    'mrp' => $variantData['mrp'],
                    'selling_price' => $variantData['selling_price'],
                    'is_active' => !empty($variantData['is_active']),
                ]);

                Log::info('✅ Variant Created', ['variant_id' => $variant->id]);

                // Variant Images
                if (!empty($variantData['images'])) {

                    Log::info('📸 Uploading Variant Images');

                    foreach ($variantData['images'] as $i => $image) {
                        $path = $image->store('products/variants', 'public');

                        ProductVariantImage::create([
                            'product_variant_id' => $variant->id,
                            'image' => $path,
                            'is_primary' => $i === 0,
                            'sort_order' => $i,
                        ]);
                    }

                    Log::info('✅ Variant Images Saved');
                }
            }

            // ============================
            // 6️⃣ ATTRIBUTES
            // ============================

            if (!empty($validated['attributes'])) {
                Log::info('🔄 Saving Attributes');

                foreach ($validated['attributes'] as $attr) {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'attribute_key' => $attr['attribute_key'],
                        'attribute_value' => $attr['attribute_value'],
                    ]);
                }

                Log::info('✅ Attributes Saved');
            }

            // ============================
            // 7️⃣ COMPLIANCE
            // ============================

            ProductCompliance::create([
                'product_id' => $product->id,
                'manufacturer' => $validated['manufacturer'] ?? null,
            ]);

            Log::info('✅ Compliance Saved');

            DB::commit();

            Log::info('🎉 Product Creation Completed Successfully');

            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully.');

            // return response()->json([
            //     'success' => true,
            //     'redirect' => route('products.index')
            // ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('❌ Product creation failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Something went wrong. Please check logs.']);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);

        $product->load([
            'category.parent',
            'brand',
            'images',
            'variants.images',
            'variants.storeInventories.store',
            'variants.inventoryBatches.store',
            'variants.priceOverrides.store',
            'attributes',
            'compliance',
        ]);

        return view('pages.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $product->load([
            'images',
            'variants.images',
            // 'variants',
            'attributes',
            'compliance'
        ]);

        $categories = Category::where('level', 'sub')->orWhere('level', 'child')->with('parent')->latest()->get();
        $brands = Brand::all();

        return view('pages.products.create', compact('categories', 'brands', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('🔄 Product Update Request', ['data' => $request->all()]);

        $product = Product::with('variants.images', 'attributes', 'images')->findOrFail($id);

        // ============================
        // 1️⃣ VALIDATION (FIXED)
        // ============================

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:products,slug,' . $id,
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',

            'variants' => 'required|array|min:1',

            'variants.*.sku' => 'required|string|max:255',
            'variants.*.pack_size' => 'sometimes|required|string|max:50',
            'variants.*.unit' => 'sometimes|required|string|max:20',
            'variants.*.mrp' => 'required|numeric|min:0',
            'variants.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            // ============================
            // 2️⃣ UPDATE PRODUCT
            // ============================

            $product->update([
                'name'        => $validated['name'],
                'slug'        => $validated['slug'],
                'category_id' => $validated['category_id'],
                'brand_id'    => $validated['brand_id'],
                'short_description' => $request->short_description,
                'description'       => $request->description,
                'is_active' => $request->boolean('is_active'),
                'is_featured'   => $request->boolean('is_featured'),
                'is_new'        => $request->boolean('is_new'),
                'is_bestseller' => $request->boolean('is_bestseller'),
                'show_out_of_stock' => $request->boolean('show_out_of_stock'),
            ]);

            // ============================
            // 3️⃣ PRODUCT IMAGES
            // ============================

            $existingImageIds = $request->input('existing_product_images', []);

            foreach ($product->images as $img) {
                if (!in_array($img->id, $existingImageIds)) {
                    Storage::disk('public')->delete($img->image);
                    $img->delete();
                }
            }

            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $i => $image) {
                    $path = $image->store('products/images', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image'      => $path,
                        'is_primary' => false,
                        'sort_order' => $i,
                    ]);
                }
            }

            // ============================
            // 4️⃣ VARIANTS (FIXED)
            // ============================

            $existingVariantIds = [];

            foreach ($request->variants as $index => $variantData) {

                Log::info('🔄 Processing Variant', ['index' => $index, 'data' => $variantData]);

                // Skip invalid variant safely
                if (empty($variantData['sku'])) {
                    Log::warning('⚠️ Skipping variant without SKU', $variantData);
                    continue;
                }

                $variantPayload = [
                    'sku'           => $variantData['sku'],
                    'barcode'       => $variantData['barcode'] ?? null,
                    'pack_size'     => $variantData['pack_size'] ?? null,
                    'unit'          => $variantData['unit'] ?? null,
                    'mrp'           => $variantData['mrp'] ?? 0,
                    'selling_price' => $variantData['selling_price'] ?? 0,
                    'cost_price'    => $variantData['cost_price'] ?? null,
                    'tax_percent'   => $variantData['tax_percent'] ?? null,
                    'is_default'    => !empty($variantData['is_default']),
                    'is_active'     => !empty($variantData['is_active']),
                ];

                // UPDATE
                if (!empty($variantData['id'])) {

                    $variant = ProductVariant::find($variantData['id']);

                    if ($variant) {
                        $variant->update($variantPayload);
                        $existingVariantIds[] = $variant->id;
                    }
                } else {

                    // CREATE
                    $variantPayload['product_id'] = $product->id;

                    $variant = ProductVariant::create($variantPayload);

                    $existingVariantIds[] = $variant->id;
                }

                // ============================
                // VARIANT IMAGES
                // ============================

                if (!empty($variantData['images'])) {

                    foreach ($variantData['images'] as $i => $image) {

                        if ($image instanceof \Illuminate\Http\UploadedFile) {

                            $path = $image->store('products/variants', 'public');

                            ProductVariantImage::create([
                                'product_variant_id' => $variant->id,
                                'image' => $path,
                                'is_primary' => $i === 0,
                            ]);
                        }
                    }
                }
            }

            // DELETE REMOVED VARIANTS
            $product->variants()
                ->whereNotIn('id', $existingVariantIds)
                ->delete();

            // ============================
            // 5️⃣ ATTRIBUTES
            // ============================

            $product->attributes()->delete();

            if (!empty($request->attributes)) {
                foreach ($request->attributes as $attr) {

                    if (empty($attr['attribute_key'])) continue;

                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'attribute_key' => $attr['attribute_key'],
                        'attribute_value' => $attr['attribute_value'] ?? null,
                        'is_filterable' => !empty($attr['is_filterable']),
                        'is_visible' => !empty($attr['is_visible']),
                    ]);
                }
            }

            // ============================
            // 6️⃣ COMPLIANCE
            // ============================

            $product->compliance()->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'fssai_license' => $request->fssai_license,
                    'manufacturer'  => $request->manufacturer,
                    'country_of_origin' => $request->country_of_origin,
                    'ingredients'   => $request->ingredients,
                    'storage_instructions' => $request->storage_instructions,
                    'usage_instructions'   => $request->usage_instructions,
                    'disclaimer'    => $request->disclaimer,
                ]
            );

            DB::commit();

            Log::info('✅ Product updated successfully', ['product_id' => $product->id]);

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully')->with('debug', 'reached');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('❌ Product update failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return back()->withInput()->withErrors([
                'general' => 'Something went wrong while updating'
            ]);
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
