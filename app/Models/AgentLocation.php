<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentLocation extends Model
{
    protected $fillable = [
        'agent_id',
        'latitude',
        'longitude',
        'battery_percentage'
    ];

    // ===============================
    // Relationships
    // ===============================

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // ===============================
    // Helper
    // ===============================

    public static function latestLocation($agentId)
    {
        return self::where('agent_id', $agentId)
            ->latest()
            ->first();
    }
}
