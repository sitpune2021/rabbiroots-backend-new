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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();
            $table->unsignedBigInteger('vendor_id')->default(1);
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->decimal('store_lat', 10, 7);
            $table->decimal('store_lng', 10, 7);
            $table->decimal('delivery_lat', 10, 7);
            $table->decimal('delivery_lng', 10, 7);
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->enum('status', [
                'placed',
                'assigned',
                'accepted',
                'picked',
                'out_for_delivery',
                'delivered',
                'delivery_attempted',
                'cancelled',
                'reassigned'
            ])->default('placed');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
