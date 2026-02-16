<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
     use HasFactory, SoftDeletes;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'code',
        'contact_phone',
        'contact_email',

        'address',
        'latitude',
        'longitude',
        'delivery_radius_km',

        'is_active',
        'is_open',
        'accepting_orders',

        'opening_time',
        'closing_time',

        'max_orders_per_slot',
        'order_cutoff_minutes',

        'daily_cash_limit',
        'pending_cash_amount',

        'manager_id',

        'last_opened_at',
        'last_closed_at',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'is_active'           => 'boolean',
        'is_open'             => 'boolean',
        'accepting_orders'    => 'boolean',

        'delivery_radius_km'  => 'decimal:2',
        'daily_cash_limit'    => 'decimal:2',
        'pending_cash_amount' => 'decimal:2',

        'opening_time'        => 'datetime:H:i',
        'closing_time'        => 'datetime:H:i',

        'last_opened_at'      => 'datetime',
        'last_closed_at'      => 'datetime',
    ];

    /* =====================================================
     | Relationships (MINIMAL & SAFE)
     |=====================================================*/

    /**
     * Primary store manager
     * (store-wise login & RBAC anchor)
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /* =====================================================
     | Query Scopes (Store-wise Control)
     |=====================================================*/

    /**
     * Only active stores
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Stores that are currently open & accepting orders
     */
    public function scopeAcceptingOrders($query)
    {
        return $query->where('is_open', true)
                     ->where('accepting_orders', true);
    }

    /* =====================================================
     | Business Logic Helpers (CRITICAL)
     |=====================================================*/

    /**
     * Can this store accept new orders right now?
     */
    public function canAcceptOrders(): bool
    {
        return $this->is_active
            && $this->is_open
            && $this->accepting_orders;
    }

    /**
     * Mark store as opened
     */
    public function open(): void
    {
        $this->update([
            'is_open'        => true,
            'accepting_orders' => true,
            'last_opened_at' => now(),
        ]);
    }

    /**
     * Mark store as closed (locks new orders)
     */
    public function close(): void
    {
        $this->update([
            'is_open'          => false,
            'accepting_orders' => false,
            'last_closed_at'   => now(),
        ]);
    }

    // public function getOpeningTimeFormattedAttribute()
    // {
    //     return $this->opening_time
    //         ? \Carbon\Carbon::createFromFormat('H:i:s', $this->opening_time)->format('h:i A')
    //         : null;
    // }

    // public function getClosingTimeFormattedAttribute()
    // {
    //     return $this->closing_time
    //         ? \Carbon\Carbon::createFromFormat('H:i:s', $this->closing_time)->format('h:i A')
    //         : null;
    // }

    public function getOpeningTimeForInputAttribute()
    {
        return $this->opening_time
            ? \Carbon\Carbon::parse($this->opening_time)->format('H:i')
            : null;
    }

    public function getClosingTimeForInputAttribute()
    {
        return $this->closing_time
            ? \Carbon\Carbon::parse($this->closing_time)->format('H:i')
            : null;
    }


}
