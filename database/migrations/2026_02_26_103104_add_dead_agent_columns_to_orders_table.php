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
            $table->unsignedBigInteger('reassigned_from')
                ->nullable()
                ->after('agent_id');

            $table->boolean('penalty_applied')
                ->default(false)
                ->after('status');

            $table->integer('reassign_count')
                ->default(0)
                ->after('penalty_applied');

            $table->timestamp('dead_detected_at')
                ->nullable()
                ->after('reassign_count');

            // Optional foreign key
            $table->foreign('reassigned_from')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['reassigned_from']);
            $table->dropColumn([
                'reassigned_from',
                'penalty_applied',
                'reassign_count',
                'dead_detected_at'
            ]);
        });
    }
};
