<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Schema;

class StoreManagerRoleSeeder extends Seeder
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
         * ❌ Do NOT truncate permissions or roles here
         * This seeder is additive, not destructive
         */

        $permissions = [

            /* ===============================
             | Store Operations
             |===============================*/
            'store.open',
            'store.close',
            'store.lock_orders',

            /* ===============================
             | Orders (Store-level only)
             |===============================*/
            'orders.view',
            'orders.assign',
            'orders.update_status',
            'orders.cancel_before_dispatch',

            /* ===============================
             | Inventory (Store-level)
             |===============================*/
            'inventory.view',
            'inventory.update',
            'inventory.adjust',
            'inventory.expiry_manage',

            /* ===============================
             | Delivery & Agents (Store-level)
             |===============================*/
            'delivery.assign',
            'delivery.track',
            'agent.track',

            /* ===============================
             | Analytics (Store-level)
             |===============================*/
            'analytics.view',

            /* ===============================
             | Support (Limited)
             |===============================*/
            'support.view_tickets',
            'support.resolve_tickets',
        ];

        // Create permissions if they don’t exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Create Store Manager role
        $role = Role::firstOrCreate([
            'name' => 'store_manager',
            'guard_name' => 'web',
        ]);

        // Assign permissions
        $role->syncPermissions($permissions);
    }
}
