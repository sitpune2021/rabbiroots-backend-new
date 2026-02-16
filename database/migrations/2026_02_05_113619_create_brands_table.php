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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            
            /* ================= BASIC ================= */
            $table->string('name');
            $table->string('slug')->unique();

            /* ================= MEDIA ================= */
            $table->string('logo')->nullable();        // brand logo
            $table->string('banner')->nullable();      // optional brand banner

            /* ================= CONTENT ================= */
            $table->text('description')->nullable();

            /* ================= SEO ================= */
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            /* ================= FLAGS ================= */
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            /* ================= SORTING ================= */
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
