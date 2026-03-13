<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'order_number',
        'vendor_id',
        'customer_id',
        'agent_id',
        'store_lat',
        'store_lng',
        'delivery_lat',
        'delivery_lng',
        'distance_km',
        'status',
        'secondary_phone',
        'primary_attempt_count',
        'secondary_attempt_count',
        'delivery_attempt_started_at',
        'sms_sent'
    ];

    // ===============================
    // Relationships
    // ===============================


    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    // ===============================
    // Scopes
    // ===============================

    public function scopePending($query)
    {
        return $query->where('status', 'placed');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    // ===============================
    // Helper
    // ===============================

    // public function markStatus($status, $userId)
    // {
    //     $this->update(['status' => $status]);

    //     OrderStatusLog::create([
    //         'order_id' => $this->id,
    //         'status' => $status,
    //         'updated_by' => $userId
    //     ]);
    // }
}
