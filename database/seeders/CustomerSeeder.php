<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 3; $i++) {

            DB::table('users')->insert([
                'name'       => 'Customer ' . $i,
                'email'      => 'customer' . $i . '@gmail.com',
                'phone'      => '98765432' . rand(10,99),
                'password'   => Hash::make('password123'),
                'role'    => 4, // Customer Role
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Customers seeded successfully!');
    }
}
