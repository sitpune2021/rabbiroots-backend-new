<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DeliveryAgentRoleSeeder extends Seeder
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

            // Agent availability
            'agent.go_online',
            'agent.go_offline',
            'agent.view_profile',

            // Orders (agent scope)
            'orders.view_assigned',
            'orders.accept',
            'orders.reject',
            'orders.pickup',
            'orders.mark_out_for_delivery',
            'orders.complete_delivery',
            'orders.mark_attempted',

            // Proof of delivery
            'delivery.upload_proof',
            'delivery.add_notes',

            // OTP
            'delivery.verify_otp',
            'delivery.use_fallback_verification',

            // Location & scanning
            'location.share_live',
            'location.scan_store_qr',

            // Item scan
            'items.scan_barcode',
            'items.manual_entry',

            // Communication
            'communication.call_customer',
            'communication.chat_customer',

            // Earnings
            'earnings.view_today',
            'earnings.view_history',
            'wallet.request_payout',
            'wallet.emergency_withdraw',
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
            'name' => 'delivery_agent',
            'guard_name' => 'web',
        ]);

        // Assign permissions
        $role->syncPermissions($permissions);
    
    }
}
