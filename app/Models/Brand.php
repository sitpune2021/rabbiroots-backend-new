<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
        'description',
        'is_active',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'is_active' => 'boolean',
    ];
}
