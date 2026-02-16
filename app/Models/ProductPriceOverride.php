<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPriceOverride extends Model
{
    protected $fillable = [
        'store_id',
        'product_variant_id',
        'override_price',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'override_price' => 'decimal:2',
        'start_at'       => 'datetime',
        'end_at'         => 'datetime',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
