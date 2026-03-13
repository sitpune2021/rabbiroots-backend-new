<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('orders')->insert([
            'order_number' => 'ORD-' . strtoupper(Str::random(6)),
            'vendor_id' => rand(1, 5),
            'customer_id' => rand(1, 20),
            'agent_id' => 14,

            'store_lat' => 10.520430,
            'store_lng' => 60.856743,
            'delivery_lat' => 10.531000,
            'delivery_lng' => 60.844000,

            'distance_km' => rand(1, 10),

            'status' => collect([
                'placed',
               
            ])->random(),

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
