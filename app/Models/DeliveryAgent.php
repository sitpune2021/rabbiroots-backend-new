<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    protected $fillable = [
        'user_id',
        'rating_avg',
        'dob',
        'dead_phone_incidents',
        'is_available',
        'aadhar_number',
        'pan_number',
        'permanent_address',
        'temporary_address',
        'license_number',
        'license_type',
        'license_issue_date',
        'license_expiry_date',
        'vehicle_name',
        'vehicle_model',
        'license_plate',
        'vehicle_capacity',
        'registration_number',
        'insurance_policy_number',
        'driving_license_doc',
        'vehicle_registration_doc',
        'insurance_doc',
        'aadhar_doc',
        'pan_doc',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
