<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Schema;

class SuperAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        Schema::disableForeignKeyConstraints();
        Permission::truncate();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        $permissions = [

            // System & Audit
            'system.manage','audit.view','audit.export',

            // Users & Roles
            'users.create','users.view','users.update','users.disable','roles.assign',

            // Orders
            'orders.view','orders.create','orders.assign','orders.update_status',
            'orders.cancel_before_dispatch','orders.cancel_after_dispatch',
            'orders.refund_initiate','orders.refund_approve','orders.override',

            // Inventory
            'inventory.view','inventory.update','inventory.adjust',
            'inventory.transfer','inventory.expiry_manage',

            // Delivery & Agents
            'delivery.assign','delivery.track','delivery.override',
            'agent.track','agent.penalize','agent.bonus',
            'agent.payout_view','agent.payout_adjust',

            // Finance
            'finance.view','finance.payout','finance.adjust',
            'finance.reports','finance.cash_reconcile',

            // Promotions
            'promo.create','promo.update','promo.schedule',
            'promo.view_reports','promo.approve',

            // Analytics
            'analytics.view','analytics.export',

            // Support & Incidents
            'support.view_tickets','support.resolve_tickets',
            'support.raise_refund_request',
            'incident.view','incident.resolve',

            // Store Ops
            'store.open','store.close','store.lock_orders',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo(Permission::all());
    }
}
