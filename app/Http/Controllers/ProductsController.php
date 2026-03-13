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
        $categories = Category::where('level', 'sub') ->orWhere('level', 'child')->with('parent')->latest()->get();
        $brands = Brand::all();

        return view('pages.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        // ============================
        // 1️⃣ VALIDATION
        // ============================

        $validated = $request->validate([
            // Product
            'name'              => 'required|string|max:255',
            'slug'              => 'required|string|max:255|unique:products,slug',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'required|exists:brands,id',
            'product_type'      => ['required', Rule::in(['grocery','fresh','frozen','pharma'])],

            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',

            'is_perishable'     => 'boolean',
            'requires_cold_storage' => 'boolean',
            'is_fragile'        => 'boolean',
            'is_veg'            => 'boolean',
            'contains_allergens'=> 'boolean',

            'shelf_life_days'   => 'nullable|integer|min:0',
            'manufactured_at'   => 'nullable|date',

            'hsn_code'          => 'nullable|string|max:50',
            'gst_percent'       => 'nullable|numeric|min:0|max:100',

            'search_rank'       => 'nullable|min:0',
            'popularity_score'  => 'nullable|min:0',
            'search_keywords'   => 'nullable|string',

            'is_featured'       => 'boolean',
            'is_new'            => 'boolean',
            'is_bestseller'     => 'boolean',
            'show_out_of_stock' => 'boolean',
            'is_active'         => 'boolean',

            'published_at'      => 'nullable|date',

            // Product Images
            'product_images.*'  => 'max:2048',

            // Variants
            'variants'                          => 'required|array|min:1',
            'variants.*.sku'                    => 'required|string|max:255',
            'variants.*.barcode'                => 'nullable|string|max:255',
            'variants.*.pack_size'              => 'required|string|max:50',
            'variants.*.unit'                   => 'required|string|max:20',
            'variants.*.mrp'                    => 'required|numeric|min:0',
            'variants.*.selling_price'          => 'required|numeric|min:0',
            'variants.*.cost_price'             => 'nullable|numeric|min:0',
            'variants.*.tax_percent'            => 'nullable|numeric|min:0|max:100',
            'variants.*.min_order_qty'           => 'nullable|integer|min:1',
            'variants.*.max_order_qty'           => 'nullable|integer|min:1',
            'variants.*.is_default'              => 'boolean',
            'variants.*.is_active'               => 'boolean',
            'variants.*.images.*'                => 'max:2048',

            // Attributes
            'attributes'                        => 'nullable|array',
            'attributes.*.attribute_key'        => 'required|string|max:255',
            'attributes.*.attribute_value'      => 'required|string|max:255',
            'attributes.*.is_filterable'         => 'boolean',
            'attributes.*.is_visible'            => 'boolean',

            // Compliance
            'fssai_license'         => 'nullable|string|max:255',
            'manufacturer'          => 'nullable|string|max:255',
            'country_of_origin'     => 'nullable|string|max:255',
            'ingredients'           => 'nullable|string',
            'allergen_info'         => 'nullable|string',
            'storage_instructions'  => 'nullable|string',
            'usage_instructions'    => 'nullable|string',
            'disclaimer'            => 'nullable|string',
        ]);

        // ============================
        // 2️⃣ TRANSACTION START
        // ============================

        DB::beginTransaction();

        try {

            // ============================
            // 3️⃣ CREATE PRODUCT
            // ============================

            $product = Product::create([
                'category_id'       => $validated['category_id'],
                'brand_id'          => $validated['brand_id'],
                'name'              => $validated['name'],
                'slug'              => $validated['slug'],
                'product_type'      => $validated['product_type'],

                'short_description' => $validated['short_description'] ?? null,
                'description'       => $validated['description'] ?? null,

                'is_perishable'     => $request->boolean('is_perishable'),
                'requires_cold_storage' => $request->boolean('requires_cold_storage'),
                'is_fragile'        => $request->boolean('is_fragile'),
                'is_veg'            => $request->boolean('is_veg'),
                'contains_allergens'=> $request->boolean('contains_allergens'),

                'shelf_life_days'   => $validated['shelf_life_days'] ?? null,
                'manufactured_at'   => $validated['manufactured_at'] ?? null,

                'hsn_code'          => $validated['hsn_code'] ?? null,
                'gst_percent'       => $validated['gst_percent'] ?? null,

                'search_rank'       => $validated['search_rank'] ?? 0,
                'popularity_score'  => $validated['popularity_score'] ?? 0,
                'search_keywords'   => isset($validated['search_keywords'])
                    ? array_map('trim', explode(',', $validated['search_keywords']))
                    : [],

                'is_featured'       => $request->boolean('is_featured'),
                'is_new'            => $request->boolean('is_new'),
                'is_bestseller'     => $request->boolean('is_bestseller'),
                'show_out_of_stock' => $request->boolean('show_out_of_stock'),
                'is_active'         => $request->boolean('is_active'),

                'published_at'      => $validated['published_at'] ?? null,
                'status'            => 'published',
            ]);

            // ============================
            // 4️⃣ PRODUCT IMAGES
            // ============================

            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $i => $image) {
                    $path = $image->store('products/images', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image'      => $path,
                        'is_primary' => $i === 0,
                        'sort_order' => $i,
                    ]);
                }
            }

            // ============================
            // 5️⃣ VARIANTS
            // ============================

            foreach ($validated['variants'] as $index => $variantData) {

                $variant = ProductVariant::create([
                    'product_id'     => $product->id,
                    'sku'            => $variantData['sku'],
                    'barcode'        => $variantData['barcode'] ?? null,
                    'pack_size'      => $variantData['pack_size'],
                    'unit'           => $variantData['unit'],
                    'mrp'            => $variantData['mrp'],
                    'selling_price'  => $variantData['selling_price'],
                    'cost_price'     => $variantData['cost_price'] ?? null,
                    'tax_percent'    => $variantData['tax_percent'] ?? null,
                    'min_order_qty'  => $variantData['min_order_qty'] ?? 1,
                    'max_order_qty'  => $variantData['max_order_qty'] ?? null,
                    'is_default'     => !empty($variantData['is_default']),
                    'is_active'      => !empty($variantData['is_active']),
                ]);

                // Variant Images
                if (isset($variantData['images'])) {
                    foreach ($variantData['images'] as $i => $image) {
                        $path = $image->store('products/variants', 'public');

                        ProductVariantImage::create([
                            'product_variant_id' => $variant->id,
                            'image'              => $path,
                            'is_primary'         => $i === 0,
                            'sort_order'         => $i,
                        ]);
                    }
                }
            }

            // ============================
            // 6️⃣ ATTRIBUTES
            // ============================

            if (!empty($validated['attributes'])) {
                foreach ($validated['attributes'] as $attr) {
                    ProductAttribute::create([
                        'product_id'    => $product->id,
                        'attribute_key' => $attr['attribute_key'],
                        'attribute_value'=> $attr['attribute_value'],
                        'is_filterable' => !empty($attr['is_filterable']),
                        'is_visible'    => !empty($attr['is_visible']),
                    ]);
                }
            }

            // ============================
            // 7️⃣ COMPLIANCE
            // ============================

            ProductCompliance::create([
                'product_id'           => $product->id,
                'fssai_license'         => $validated['fssai_license'] ?? null,
                'manufacturer'          => $validated['manufacturer'] ?? null,
                'country_of_origin'     => $validated['country_of_origin'] ?? null,
                'ingredients'           => $validated['ingredients'] ?? null,
                'allergen_info'         => $validated['allergen_info'] ?? null,
                'storage_instructions'  => $validated['storage_instructions'] ?? null,
                'usage_instructions'    => $validated['usage_instructions'] ?? null,
                'disclaimer'            => $validated['disclaimer'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Something went wrong. Please try again.']);
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

        $categories = Category::where('level', 'sub') ->orWhere('level', 'child')->with('parent')->latest()->get();
        $brands = Brand::all();

        return view('pages.products.create', compact('categories', 'brands', 'product'));

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
