<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Amul',
                'description' => 'India’s leading dairy brand',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Nestle',
                'description' => 'Global food and beverage brand',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Britannia',
                'description' => 'Biscuits and dairy products',
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Patanjali',
                'description' => 'Ayurvedic and FMCG products',
                'is_featured' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Lays',
                'description' => 'Popular snack and chips brand',
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Loose',
                'description' => 'Unbranded / loose grocery items',
                'is_featured' => false,
                'sort_order' => 6,
            ],

        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => Str::slug($brand['name']),
                'logo' => null,
                'banner' => null,
                'description' => $brand['description'],
                'meta_title' => $brand['name'],
                'meta_description' => $brand['description'],
                'meta_keywords' => $brand['name'],
                'is_active' => true,
                'is_featured' => $brand['is_featured'],
                'sort_order' => $brand['sort_order'],
                
            ]);
        }

    }
}
