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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('secondary_phone')->nullable();

            $table->integer('primary_attempt_count')->default(0);
            $table->integer('secondary_attempt_count')->default(0);

            $table->timestamp('delivery_attempt_started_at')->nullable();
            $table->boolean('sms_sent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'secondary_phone',
                'primary_attempt_count',
                'secondary_attempt_count',
                'delivery_attempt_started_at',
                'sms_sent'
            ]);
        });
    }
};
