<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCompliance extends Model
{
        protected $fillable = [
        'product_id',
        'fssai_license',
        'manufacturer',
        'country_of_origin',
        'ingredients',
        'allergen_info',
        'storage_instructions',
        'usage_instructions',
        'disclaimer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
