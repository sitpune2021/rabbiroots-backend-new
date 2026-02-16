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
        Schema::create('delivery_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('dob')->nullable();

            $table->string('aadhar_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('temporary_address')->nullable();

            // License Details
            $table->string('license_number')->nullable();
            $table->string('license_type')->nullable();
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiry_date')->nullable();

            // Vehicle Details
            $table->string('vehicle_name')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('license_plate')->nullable();
            $table->integer('vehicle_capacity')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('insurance_policy_number')->nullable();

            // Documents (store file path)
            $table->string('driving_license_doc')->nullable();
            $table->string('vehicle_registration_doc')->nullable();
            $table->string('insurance_doc')->nullable();
            $table->string('aadhar_doc')->nullable();
            $table->string('pan_doc')->nullable();

            // Status
            $table->enum('status', ['pending', 'active', 'inactive', 'on-leave', 'suspended'])
                ->default('pending');
            $table->decimal('rating_avg', 3, 2)->default(5.0);
            $table->integer('dead_phone_incidents')->default(0);
            $table->boolean('is_available')->default(true);         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_agents');
    }
};
