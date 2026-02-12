<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            [
                'email' => 'superadmin@rabbiroots.com',
            ],
            [
                'name'              => 'RabbiRoots Super Admin',
                'phone'             => '9999999999',
                'password'          => Hash::make('SuperAdmin@123'),
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign role safely (idempotent)
        if (! $superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }
    }
}
