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
        Schema::table('delivery_agents', function (Blueprint $table) {
            Schema::table('delivery_agents', function (Blueprint $table) {

                $table->boolean('is_online')
                    ->default(0)
                    ->after('user_id');

                $table->unsignedBigInteger('current_order_id')
                    ->nullable()
                    ->after('is_online');

                // Optional foreign key to orders table
                $table->foreign('current_order_id')
                    ->references('id')
                    ->on('orders')
                    ->onDelete('set null');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['current_order_id']);

            // Then drop columns
            $table->dropColumn([
                'is_online',
                'current_order_id'
            ]);
        });
    }
};
