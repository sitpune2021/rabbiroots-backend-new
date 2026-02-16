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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();

            /* =======================
         | Basic Information
         ======================= */
            $table->string('code_name')->unique(); // SAVE20
            $table->string('description')->nullable();

            /* =======================
         | Discount Configuration
         ======================= */
            $table->enum('discount_type', ['percentage', 'flat']);
            $table->decimal('discount_value', 10, 2);

            $table->decimal('min_order_value', 10, 2)->default(0);
            $table->decimal('max_discount_cap', 10, 2)->default(0);

            /* =======================
         | Validity & Scheduling
         ======================= */
            $table->date('start_date');
            $table->date('end_date');

            $table->time('active_start_time')->nullable();
            $table->time('active_end_time')->nullable();

            $table->enum('status', ['draft', 'active', 'scheduled', 'inactive'])
                ->default('draft');

            /* =======================
         | Applicability Rules
         ======================= */
            $table->enum('store_type', ['all_stores', 'specific_store'])
                ->default('all_stores');

            // 👇 REQUIRED BY YOUR CONTROLLER
            $table->boolean('new_users')->default(0);
            $table->boolean('all_users')->default(0);

            /* =======================
         | Device Configuration
         ======================= */
            $table->boolean('device_web')->default(0);
            $table->boolean('device_ios')->default(0);
            $table->boolean('device_android')->default(0);

            /* =======================
         | Usage Rules
         ======================= */
            $table->enum('usage_type', ['single_use', 'multi_use'])
                ->default('single_use');

            $table->integer('usage_limit_per_user')->nullable();
            $table->integer('total_redemptions_limit')->nullable();
            $table->integer('total_redemptions_used')->default(0);

            /* =======================
         | Dynamic Pricing Rules
         ======================= */
            $table->boolean('apply_rush_pricing')->default(0);

            $table->integer('morning_rush_adjustment')->default(0);
            $table->integer('lunch_rush_adjustment')->default(0);
            $table->integer('evening_rush_adjustment')->default(0);

            /* =======================
         | Metadata
         ======================= */
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
