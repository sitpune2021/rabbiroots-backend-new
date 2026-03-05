<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CustomerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * IMPORTANT:
         * ❌ Do NOT truncate roles or permissions
         * This seeder is additive only
         */

        $permissions = [

            // Profile
            'customer.view_profile',
            'customer.update_profile',

            // Address
            'address.add',
            'address.update',
            'address.delete',
            'address.view',

            // Orders
            'orders.create',
            'orders.view_own',
            'orders.cancel',
            'orders.track',

            // Payments
            'payments.make',
            'payments.view_history',

            // Reviews
            'reviews.give',
            'reviews.edit',
            'reviews.delete',

            // Communication
            'communication.call_agent',
            'communication.chat_agent',

            // Notifications
            'notifications.view',
            'notifications.mark_read',

            // Wallet
            'wallet.view_balance',
            'wallet.add_money',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create role
        $role = Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);

        // Assign permissions
        $role->syncPermissions($permissions);
    }
}
