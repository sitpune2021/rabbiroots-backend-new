<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreInventory extends Model
{
        protected $fillable = [
        'store_id',
        'product_variant_id',
        'available_qty',
        'reserved_qty',
        'committed_qty',
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
