<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
     protected $table = 'promo_codes';

    protected $fillable = [
        'code_name',
        'description',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_discount_cap',
        'start_date',
        'end_date',
        'active_start_time',
        'active_end_time',
        'status',
        'store_type',
        'new_users',
        'all_users',
        'device_web',
        'device_ios',
        'device_android',
        'usage_type',
        'usage_limit_per_user',
        'total_redemptions_limit',
        'apply_rush_pricing',
        'morning_rush_adjustment',
        'lunch_rush_adjustment',
        'evening_rush_adjustment',
    ];

    protected $casts = [
        'new_users' => 'boolean',
        'all_users' => 'boolean',
        'device_web' => 'boolean',
        'device_ios' => 'boolean',
        'device_android' => 'boolean',
        'apply_rush_pricing' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount_cap' => 'decimal:2',
        'usage_limit_per_user' => 'integer',
        'total_redemptions_limit' => 'integer',
        'morning_rush_adjustment' => 'integer',
        'lunch_rush_adjustment' => 'integer',
        'evening_rush_adjustment' => 'integer',
    ];
}
