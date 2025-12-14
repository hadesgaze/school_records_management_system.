<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- 1. Import this

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'details',
        'ip_address'
    ];

    /**
     * Get the user that performed the action. (THIS IS THE FIX)
     */
    public function user(): BelongsTo // <-- 2. Add this method
    {
        return $this->belongsTo(User::class);
    }
}