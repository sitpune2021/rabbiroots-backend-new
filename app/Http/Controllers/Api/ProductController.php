<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Product, Category};
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Display the specified product details
     */
    public function show(string $id)
    {
        $cacheKey = "product_details_{$id}";

        return Cache::remember($cacheKey, 300, function () use ($id) {
            
            // Fetch product with all relationships
            $product = Product::where('id', $id)
                ->where('is_active', true)
                ->with([
                    'category.parent.parent', // For breadcrumb
                    'brand',
                    'images' => function ($q) {
                        $q->orderBy('is_primary', 'desc')->orderBy('sort_order');
                    },
                    'variants' => function ($q) {
                        $q->where('is_active', true)
                          ->orderBy('is_default', 'desc')
                          ->orderBy('selling_price');
                    },
                    'variants.images' => function ($q) {
                        $q->orderBy('is_primary', 'desc')->orderBy('sort_order');
                    },
                    'attributes' => function ($q) {
                        $q->where('is_visible', true);
                    },
                    'compliance'
                ])
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found'
                ], 404);
            }

            // Build breadcrumb
            $breadcrumb = $this->buildBreadcrumb($product->category);

            // Format variants
            $variants = $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'unit' => $variant->unit,
                    'pack_size' => $variant->pack_size,
                    'mrp' => (float) $variant->mrp,
                    'selling_price' => (float) $variant->selling_price,
                    'discount_percent' => $variant->mrp > 0 
                        ? round((($variant->mrp - $variant->selling_price) / $variant->mrp) * 100, 2) 
                        : 0,
                    'is_default' => $variant->is_default,
                    'min_order_qty' => $variant->min_order_qty,
                    'max_order_qty' => $variant->max_order_qty,
                    'images' => $variant->images->map(fn($img) => [
                        'image' => asset('storage/' . $img->image),
                        'is_primary' => $img->is_primary
                    ])
                ];
            });

            // Format product images
            $images = $product->images->map(fn($img) => [
                'image' => asset('storage/' . $img->image),
                'is_primary' => $img->is_primary
            ]);

            // Product attributes (nutritional info, etc.)
            $attributes = $product->attributes->mapWithKeys(function ($attr) {
                return [$attr->attribute_key => $attr->attribute_value];
            });

            // Build product description as array of objects with label and value
            $descriptionArray = [];
            
            // Add description
            $descriptionArray[] = [
                'label' => 'Description',
                'value' => $product->description,
            ];

            // Add compliance data if exists
            if ($product->compliance) {
                $descriptionArray[] = ['label' => 'FSSAI License', 'value' => $product->compliance->fssai_license];
                $descriptionArray[] = ['label' => 'Manufacturer', 'value' => $product->compliance->manufacturer];
                $descriptionArray[] = ['label' => 'Country of Origin', 'value' => $product->compliance->country_of_origin];
                $descriptionArray[] = ['label' => 'Ingredients', 'value' => $product->compliance->ingredients];
                $descriptionArray[] = ['label' => 'Allergen Info', 'value' => $product->compliance->allergen_info];
                $descriptionArray[] = ['label' => 'Storage Instructions', 'value' => $product->compliance->storage_instructions];
                $descriptionArray[] = ['label' => 'Usage Instructions', 'value' => $product->compliance->usage_instructions];
                $descriptionArray[] = ['label' => 'Disclaimer', 'value' => $product->compliance->disclaimer];
            }

            // Add attributes
            if ($attributes->isNotEmpty()) {
                foreach ($attributes as $key => $value) {
                    $descriptionArray[] = ['label' => $key, 'value' => $value];
                }
            }

            // Similar Products (10 random from same category)
            $similarProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->with([
                    'images' => fn($q) => $q->where('is_primary', true),
                    'variants' => fn($q) => $q->where('is_default', true)
                ])
                ->inRandomOrder()
                ->take(10)
                ->get()
                ->map(function ($p) {
                    $variant = $p->variants->first();
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'slug' => $p->slug,
                        'short_description' => $p->short_description,
                        'image' => optional($p->images->first())->image,
                        'price' => $variant ? (float) $variant->selling_price : 0,
                        'mrp' => $variant ? (float) $variant->mrp : 0,
                    ];
                });

            // Top 10 Recent Products from Same Category
            $topProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->with([
                    'images' => fn($q) => $q->where('is_primary', true),
                    'variants' => fn($q) => $q->where('is_default', true)
                ])
                ->latest('created_at')
                ->take(10)
                ->get()
                ->map(function ($p) {
                    $variant = $p->variants->first();
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'slug' => $p->slug,
                        'short_description' => $p->short_description,
                        'image' => optional($p->images->first())->image,
                        'price' => $variant ? (float) $variant->selling_price : 0,
                        'mrp' => $variant ? (float) $variant->mrp : 0,
                    ];
                });

            // Footer data
            $footerData = [
                'links' => [
                    ['name' => 'About Us', 'url' => '/about-us'],
                    ['name' => 'Contact Us', 'url' => '/contact-us'],
                    ['name' => 'Privacy Policy', 'url' => '/privacy-policy'],
                    ['name' => 'Terms of Service', 'url' => '/terms-of-service'],
                ],
                'categories' => Category::active()
                    ->level('main')
                    ->orderBy('sort_order')
                    ->select('id', 'name', 'slug')
                    ->take(30)
                    ->get()
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'breadcrumb' => $breadcrumb,
                    'details' => [
                        // Basic Product Information
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        
                        // Variants
                        'variants' => $variants,
                        
                        // Description with Compliance and Attributes combined as array of objects
                        'description' => $descriptionArray,
                        
                        // Flags
                        'flag' => [
                            'is_veg' => $product->is_veg,
                            'is_perishable' => $product->is_perishable,
                            'requires_cold_storage' => $product->requires_cold_storage,
                        ],
                    ],
                    'feature_section' => [
                        'similar_products' => $similarProducts,
                        'top_products' => $topProducts
                    ],
                    'footer_sections' => $footerData,
                ]
            ]);
        });
    }

    /**
     * Build breadcrumb trail
     */
    private function buildBreadcrumb($category)
    {
        $breadcrumb = [['id' => 0, 'name' => 'Home', 'url' => '/', 'slug' => '']];

        // Collect category hierarchy
        $categories = [];
        $current = $category;
        
        while ($current) {
            array_unshift($categories, $current);
            $current = $current->parent;
        }

        foreach ($categories as $cat) {
            $breadcrumb[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'url' => '/category/' . $cat->slug
            ];
        }

        return $breadcrumb;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
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
