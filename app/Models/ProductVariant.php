<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'pack_size',
        'unit',
        'mrp',
        'selling_price',
        'cost_price',
        'tax_percent',
        'min_order_qty',
        'max_order_qty',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'mrp'          => 'decimal:2',
        'selling_price'=> 'decimal:2',
        'cost_price'   => 'decimal:2',
        'tax_percent'  => 'decimal:2',
        'is_default'   => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }

    public function storeInventories()
    {
        return $this->hasMany(StoreInventory::class);
    }

    public function priceOverrides()
    {
        return $this->hasMany(ProductPriceOverride::class);
    }

    public function inventoryBatches()
    {
        return $this->hasMany(InventoryBatch::class);
    }
}
