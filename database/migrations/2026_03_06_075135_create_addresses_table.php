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
        Schema::create('addresses', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->enum('address_type', ['home', 'work', 'hotel', 'other']);
            $table->string('house_no');
            $table->string('floor')->nullable();
            $table->string('area');
            $table->string('landmark')->nullable();

            $table->string('city');
            $table->string('state');
            $table->string('pincode', 6);

            $table->string('name');
            $table->string('phone')->nullable();

            $table->boolean('is_default')->default(0);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
