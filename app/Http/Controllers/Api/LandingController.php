<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Category, Product, Store};
use Illuminate\Support\Facades\Cache;

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
           $topCategories = Category::active()->level('main')->select('id','name','slug','icon','image')
                ->take(20)
                ->select('id','name','slug','icon','image')
                ->get();

            /* ============================================
               2️⃣ TOP 3 FEATURED SECTIONS
            ============================================ */

            $featuredMainCategories = Category::active()
                ->level('main')
                ->orderBy('sort_order')
                ->take(3)
                ->with('children.children') // eager load level2 & level3
                ->get();

            $sections = [];

            foreach ($featuredMainCategories as $main) {

                $subData = [];
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

                // foreach ($main->children as $level2) {

                //     // 🔥 Get level2 + level3 ids
                //     $categoryIds = collect([$level2->id])
                //         ->merge($level2->children->pluck('id'));

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
                                ->select('id','product_id','selling_price','mrp');
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
                                'image' => optional($product->images->first())->image,
                                'price' => $override?->override_price ?? $variant->selling_price,
                                'mrp' => $variant->mrp,
                                'stock' => $inventory?->available_qty ?? 0,
                                'in_stock' => ($inventory?->available_qty ?? 0) > 0
                            ];
                        })->filter()->values();

                    // ✅ Only push if products exist
                    if ($products->isNotEmpty()) {
                        // $sections[] = [
                        //     'id' => $level2->id,
                        //     'name' => $level2->name,
                        //     'slug' => $level2->slug,
                        //     'image' => $level2->image,
                        //     'products' => $products
                        // ];
                          $sections[] = [
                            'id' => $main->id,
                            'name' => $main->name,
                            'slug' => $main->slug,
                            'image' => $main->image,
                            'products' => $products
                        ];
                    }
                // }

                // ✅ Only push main if subcategories exist
                // if (!empty($subData)) {
                //     $sections[] = [
                //         'id' => $main->id,
                //         'name' => $main->name,
                //         'slug' => $main->slug,
                //         'image' => $main->image,
                //         'sub_categories' => $subData
                //     ];
                // }
            }


            return response()->json([
                'logo' => asset('images/logo.png'),
                'store' => Store::active()->select('id','name','code', 'latitude', 'longitude', 'delivery_radius_km')->first(),
                'main_banner' => ['id' => 1, 'image' => asset('images/main-banner.jpg'), 'url' => '/category/fruits'],
                'slider_banners' => [
                    ['id' => 1, 'image' => asset('images/slider1.jpg'), 'url' => '/category/fruits'],
                    ['id' => 2, 'image' => asset('images/slider2.jpg'), 'url' => '/category/vegetables'],
                    ['id' => 3, 'image' => asset('images/slider3.jpg'), 'url' => '/category/dairy'],
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
