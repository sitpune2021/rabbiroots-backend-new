<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'address_type',
        'house_no',
        'floor',
        'area',
        'landmark',
        'city',
        'state',
        'pincode',
        'name',
        'phone',
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
