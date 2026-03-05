<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $orders = Order::where('status', 'delivered')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'orders.customer_id');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'orders.agent_id');
            })
            ->get();

        foreach ($orders as $order) {

            if (Review::where('order_id', $order->id)->exists()) {
                continue;
            }

            Review::create([
                'order_id'    => $order->id,
                'customer_id' => $order->customer_id,
                'agent_id'    => $order->agent_id,
                'rating'      => rand(3, 5),
                'review'      => 'Good delivery service.',
            ]);
        }

        $this->command->info('Reviews seeded successfully!');
    }
}
