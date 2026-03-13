<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
        protected $fillable = [
        'store_id',
        'product_variant_id',
        'expiry_date',
        'quantity_received',
        'quantity_available',
        'received_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_at' => 'datetime',
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
