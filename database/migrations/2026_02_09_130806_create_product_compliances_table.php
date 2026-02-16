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
        Schema::create('product_compliances', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('fssai_license')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('country_of_origin')->nullable();

            $table->text('ingredients')->nullable();
            $table->text('allergen_info')->nullable();
            $table->text('storage_instructions')->nullable();
            $table->text('usage_instructions')->nullable();
            $table->text('disclaimer')->nullable();

            // Key Features , Return Policy, Sugar Profile, 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_compliances');
    }
};
