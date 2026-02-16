<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_key',
        'attribute_value',
        'is_filterable',
        'is_visible',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
        'is_visible'    => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
