<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Category, Product, Store};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LandingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Cache::remember('landing_page_v1', 300, function () {
        // $storeId= request()->header('X-Store-Id') ?? 1; // Default to 1 if header not present


            // 1️⃣ Top 20 categories
            $topCategories = Category::active()->level('main')->select('id','name','slug','icon','icon_alt','image', 'image_alt')->orderBy('sort_order')
                ->take(20)
                ->get();

            $topCategories->transform(function ($category) {
                $category->icon = $category->icon ? asset(Storage::url($category->icon)) : null;
                $category->icon_alt = $category->icon_alt ?? $category->name;
                $category->image = $category->image ? asset(Storage::url($category->image)) : null;
                $category->image_alt = $category->image_alt ?? $category->name;
                $category->sub_id = $category->children->first() ? $category->children->first()->id : null;
                $category->url = url("api/listing?01_cat_id={$category->id}&02_cat_id={$category->sub_id}");
                unset($category->children);
                return $category;
            });

            /* ============================================
               2️⃣ TOP 3 FEATURED SECTIONS
            ============================================ */

            $featuredMainCategories = Category::active()
                ->level('main')
                ->orderBy('sort_order')
                ->take(6)
                ->with('children.children') // eager load level2 & level3
                ->get();

            $sections = [];

            foreach ($featuredMainCategories as $main) {

                    // 🔥 Collect ALL level2 + level3 ids
                $level2Ids = $main->children->pluck('id');

                $level3Ids = $main->children
                    ->flatMap(function ($child) {
                        return $child->children->pluck('id');
                    });

                $allCategoryIds = collect()
                    ->merge($level2Ids)
                    ->merge($level3Ids)
                    ->unique()
                    ->values();

                $products = Product::whereIn('category_id', $allCategoryIds)
                    ->where('is_active', true)
                    // ->whereHas('variants.storeInventories', function ($q) use ($storeId) {
                    //     $q->where('store_id', $storeId)
                    //     ->where('available_qty', '>', 0);
                    // })
                    ->with([
                        'images' => function ($q) {
                            $q->where('is_primary', true)
                            ->select('id','product_id','image');
                        },
                        'variants' => function ($q) {
                            $q->where('is_default', true)
                            // ->with([
                            //     'storeInventories' => function ($q2) use ($storeId) {
                            //         $q2->where('store_id', $storeId)
                            //             ->select('id','variant_id','available_qty');
                            //     },
                            //     'priceOverrides' => function ($q3) use ($storeId) {
                            //         $q3->where('store_id', $storeId)
                            //             ->select('id','variant_id','override_price');
                            //     }
                            // ])
                            ->select('id','product_id','pack_size','unit','cost_price','tax_percent','selling_price','mrp');
                        }
                    ])
                    ->inRandomOrder()
                    ->take(12)
                    ->select('id','category_id','name','slug','short_description')
                    ->get()
                    ->map(function ($product) {

                        $variant = $product->variants->first();

                        if (!$variant) return null;

                        $inventory = $variant->storeInventories->first();
                        $override = $variant->priceOverrides->first();

                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'slug' => $product->slug,
                            'short_description' => $product->short_description,
                            'image' => $product->images->first()
                                        ? asset(Storage::url($product->images->first()->image))
                                        : null,
                            'image_alt' => $product->images->first() ? $product->images->first()->image_alt ?? $product->name : null,

                            'price' => $override?->override_price ?? $variant->selling_price,
                            'mrp' => $variant->mrp,
                            'stock' => $inventory?->available_qty ?? 0,
                            'in_stock' => ($inventory?->available_qty ?? 0) > 0,
                            'variants' => $product->variants->map(function ($v) {
                                return [
                                    'id' => $v->id,
                                    'pack_size' => $v->pack_size,
                                    'unit' => $v->unit,
                                    'cost_price' => $v->cost_price,
                                    'tax_percent' => $v->tax_percent,
                                    'selling_price' => $v->selling_price,
                                    'mrp' => $v->mrp,
                                    'discount_percent' => $v->mrp > 0
                                        ? round((($v->mrp - $v->selling_price) / $v->mrp) * 100)
                                        : 0,
                                ];
                            })
                        ];
                    })->filter()->values();

                // ✅ Only push if products exist
                if ($products->isNotEmpty()) {
                        $sections[] = [
                        'id' => $main->id,
                        'name' => $main->name,
                        'slug' => $main->slug,
                        'icon' => $main->icon ? asset(Storage::url($main->icon)) : null,
                        'icon_alt' => $main->icon_alt ?? $main->name,
                        'image' => $main->image ? asset(Storage::url($main->image)) : null,
                        'image_alt' => $main->image_alt ?? $main->name,
                        'products' => $products
                    ];
                }
               
            }


            return response()->json([
                'logo' => asset('temp/logo/logo.png'),
                'store' => Store::active()->select('id','name','code', 'latitude', 'longitude', 'delivery_radius_km')->first(),
                'main_banner' => ['id' => 1, 'image' => asset('temp/banner/main-banner.webp'), 'url' => '/category/fruits'],
                'slider_banners' => [
                    ['id' => 1, 'image' => asset('temp/slider/slider1.avif'), 'url' => '/category/fruits'],
                    ['id' => 2, 'image' => asset('temp/slider/slider2.avif'), 'url' => '/category/vegetables'],
                    ['id' => 3, 'image' => asset('temp/slider/slider3.avif'), 'url' => '/category/dairy'],
                ],
                'top_categories' => $topCategories,
                'featured_sections' => $sections,
                'footer_sections' => [
                    'links' => [
                        ['name' => 'About Us', 'url' => '/about-us'],
                        ['name' => 'Contact Us', 'url' => '/contact-us'],
                        ['name' => 'Privacy Policy', 'url' => '/privacy-policy'],
                        ['name' => 'Terms of Service', 'url' => '/terms-of-service'],
                    ],
                   'categories' => Category::active()->level('main')->orderBy('sort_order')->select('id','name','slug')->inRandomOrder()->take(30)->get()
                ]
            ]);
        });
    }

    public function listing(Request $request)
    {
        $cat1 = $request->query('01_cat_id');
        $cat2 = $request->query('02_cat_id');

        $categoryMain = Category::active()
            ->where('id', $cat1)
            ->with([
                'children' => function ($q) {
                    $q->active()->with(['children' => function ($q2) {
                        $q2->active();
                    }]);
                }
            ])
            ->select(
                'id','name','slug','icon','image',
                'meta_title','meta_description','meta_keywords'
            )
            ->first();

        if (!$categoryMain) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // 🔹 Main Category
        $mainCategory = [
            'id' => $categoryMain->id,
            'name' => $categoryMain->name,
            'slug' => $categoryMain->slug,
            'icon' => $categoryMain->icon ? Storage::url($categoryMain->icon) : null,
            'image' => $categoryMain->image ? Storage::url($categoryMain->image) : null,
        ];

        // 🔹 SEO
        $seo = [
            'meta_title' => $categoryMain->meta_title ?? $categoryMain->name,
            'meta_description' => $categoryMain->meta_description,
            'meta_keywords' => $categoryMain->meta_keywords,
        ];

        // 🔥 Collect all category IDs once
        $allCategoryIds = collect();

        foreach ($categoryMain->children as $subCategory) {
            $allCategoryIds->push($subCategory->id);

            foreach ($subCategory->children as $level3) {
                $allCategoryIds->push($level3->id);
            }
        }

        $allCategoryIds = $allCategoryIds->unique()->values();

        // 🔥 Fetch all products in ONE query
        $allProducts = Product::whereIn('category_id', $allCategoryIds)
            ->where('is_active', true)
            ->with([
                'images' => fn($q) => $q->where('is_primary', true)
                    ->select('id','product_id','image'),
                'variants' => fn($q) => $q->where('is_default', true)
                    ->select('id','product_id','pack_size','unit','cost_price','tax_percent','selling_price','mrp'),
            ])
            ->select('id','category_id','name','slug','short_description')
            ->get();

        // 🔥 Group products by subcategory
        $sections = [];

        foreach ($categoryMain->children as $subCategory) {

            $level3Ids = $subCategory->children->pluck('id');

            $categoryIds = collect([$subCategory->id])
                ->merge($level3Ids);

            $products = $allProducts
                ->whereIn('category_id', $categoryIds)
                ->map(function ($product) {

                    $variant = $product->variants->first();
                    if (!$variant) return null;

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'description' => $product->short_description,
                        'image' => $product->images->first()
                            ? Storage::url($product->images->first()->image)
                            : null,
                        // 'price' => $variant->selling_price,
                        // 'mrp' => $variant->mrp,
                        // 'discount_percent' => $variant->mrp > 0
                        //     ? round((($variant->mrp - $variant->selling_price) / $variant->mrp) * 100)
                        //     : 0,
                        // 'in_stock' => true,
                        'variants' => $product->variants->map(function ($v) {
                            return [
                                'id' => $v->id,
                                'pack_size' => $v->pack_size,
                                'unit' => $v->unit,
                                'cost_price' => $v->cost_price,
                                'tax_percent' => $v->tax_percent,
                                'selling_price' => $v->selling_price,
                                'mrp' => $v->mrp,
                                'discount_percent' => $v->mrp > 0
                                    ? round((($v->mrp - $v->selling_price) / $v->mrp) * 100)
                                    : 0,
                            ];
                         })
                    ];
                })
                ->filter()
                ->values();

            $sections[] = [
                'sub_category' => [
                    'id' => $subCategory->id,
                    'name' => $subCategory->name,
                    'slug' => $subCategory->slug,
                    'icon' => $subCategory->icon
                        ? Storage::url($subCategory->icon)
                        : null,
                    'image' => $subCategory->image
                        ? Storage::url($subCategory->image)
                        : null,
                    'active' => $subCategory->id == $cat2
                ],
                'products' => $products->values()
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'category' => $mainCategory,
                'seo' => $seo,
                'sections' => $sections,
                'breadcrumb' => [
                    [
                        'id' => $categoryMain->id,
                        'name' => $categoryMain->name,
                        'slug' => $categoryMain->slug
                    ]
                ]
            ]
        ]);
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
