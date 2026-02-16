<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [

        'category_id',
        'brand_id',
        'name',
        'slug',
        'product_type',
        'is_perishable',
        'requires_cold_storage',
        'is_fragile',
        'short_description',
        'description',
        'is_veg',
        'contains_allergens',
        'shelf_life_days',
        'manufactured_at',
        'hsn_code',
        'gst_percent',
        'search_rank',
        'popularity_score',
        'search_keywords',
        'is_featured',
        'is_new',
        'is_bestseller',
        'show_out_of_stock',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_perishable'        => 'boolean',
        'requires_cold_storage'=> 'boolean',
        'is_fragile'           => 'boolean',
        'is_veg'               => 'boolean',
        'contains_allergens'   => 'boolean',
        'is_featured'          => 'boolean',
        'is_new'               => 'boolean',
        'is_bestseller'        => 'boolean',
        'show_out_of_stock'    => 'boolean',
        'is_active'            => 'boolean',
        'search_keywords'      => 'array',
        'manufactured_at'      => 'date',
        'published_at'         => 'datetime',
    ];

    /* ===================== RELATIONS ===================== */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function compliance()
    {
        return $this->hasOne(ProductCompliance::class);
    }

    /* ===================== SCOPES ===================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
