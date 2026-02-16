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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('product_type', 20); // grocery, fresh, frozen, pharma
            $table->boolean('is_perishable')->default(false);
            $table->boolean('requires_cold_storage')->default(false);
            $table->boolean('is_fragile')->default(false);

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->boolean('is_veg')->nullable();
            $table->boolean('contains_allergens')->default(false);

            $table->integer('shelf_life_days')->nullable();
            $table->date('manufactured_at')->nullable();

            $table->string('hsn_code')->nullable();
            $table->decimal('gst_percent', 5, 2)->default(0);

            $table->integer('search_rank')->default(0);
            $table->integer('popularity_score')->default(0);
            $table->json('search_keywords')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('show_out_of_stock')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index(['is_featured', 'is_bestseller']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
