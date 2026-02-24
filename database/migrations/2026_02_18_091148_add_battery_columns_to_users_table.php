<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('battery_level')->nullable()->after('remember_token');
            $table->timestamp('battery_updated_at')->nullable()->after('battery_level');
            $table->boolean('can_accept_orders')->default(true)->after('battery_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'battery_level',
                'battery_updated_at',
                'can_accept_orders'
            ]);
        });
    }
};
