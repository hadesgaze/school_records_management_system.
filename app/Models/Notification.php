<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Notification extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_role',
        'user_id',
        'message',
        'is_read',
        'related_item_id',   // <--- ADD THIS
        'related_item_type'  // <--- ADD THIS
    ];

    protected $casts = ['is_read' => 'boolean'];

    // ... your existing safe getters/setters for message ...
    public function getMessageAttribute($value)
    {
        if ($value === null) return null;
        try { return Crypt::decryptString($value); }
        catch (\Throwable $e) { return $value; }
    }

    public function setMessageAttribute($value)
    {
        if ($value === null) { $this->attributes['message'] = null; return; }
        try {
            Crypt::decryptString($value);
            $this->attributes['message'] = $value;
        } catch (\Throwable $e) {
            $this->attributes['message'] = Crypt::encryptString($value);
        }
    }

    public function sender() { return $this->belongsTo(\App\Models\User::class, 'sender_id'); }
    public function recipient() { return $this->belongsTo(\App\Models\User::class, 'user_id'); }
}