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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            
            /* =====================================================
             | Store Identity
             |=====================================================*/
            $table->string('name');                         // Display name
            $table->string('code')->unique();               // SAT-01, PUN-02
            $table->string('contact_phone', 15)->nullable();
            $table->string('contact_email')->nullable();

            /* =====================================================
             | Physical Location & Delivery Logic
             |=====================================================*/
            $table->text('address');
            $table->decimal('latitude', 10, 7);             // agent distance calc
            $table->decimal('longitude', 10, 7);
            $table->decimal('delivery_radius_km', 5, 2)->default(5.00);                           // order eligibility

            /* =====================================================
             | Operational State (CRITICAL)
             |=====================================================*/
            $table->boolean('is_active')->default(true);    // admin disable
            $table->boolean('is_open')->default(false);     // Start / Close Store
            $table->boolean('accepting_orders')->default(true);
            // auto false when store closed or force locked

            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();

            /* =====================================================
             | Order & Inventory Control
             |=====================================================*/
            $table->unsignedInteger('max_orders_per_slot')
                  ->nullable();                              // peak hour throttling

            $table->unsignedInteger('order_cutoff_minutes')
                  ->default(5);                              // cancel window buffer

            /* =====================================================
             | Cash & Finance Control (COD)
             |=====================================================*/
            $table->decimal('daily_cash_limit', 10, 2)
                  ->nullable();                              // risk control

            $table->decimal('pending_cash_amount', 10, 2)
                  ->default(0);                              // reconciliation

            /* =====================================================
             | Store Management (RBAC Anchor)
             |=====================================================*/
            $table->unsignedBigInteger('manager_id')->nullable();
            // primary store manager (users.id)

            /* =====================================================
             | Audit & Compliance
             |=====================================================*/
            $table->timestamp('last_opened_at')->nullable();
            $table->timestamp('last_closed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* =====================================================
             | Indexes
             |=====================================================*/
            $table->index(['is_active', 'is_open']);
            $table->index('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};

