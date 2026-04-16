<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDeletion extends Model
{
    protected $fillable = [
        'user_id',
        'reason',
        'requested_at',
        'scheduled_delete_at',
        'is_processed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
