<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
        'description',
        'is_active',

        //    'logo',
        // 'banner',
        // 'meta_title',
        // 'meta_description',
        // 'meta_keywords',
        // 'is_featured',
        // 'sort_order',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
}
