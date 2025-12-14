<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_role',
        'user_id',
        'message',
        'related_item_id',
        'related_item_type',
        'is_read'
    ];

    // Relationship to sender (faculty)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relationship to receiver (user - if specific user)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Polymorphic relationship to the related item (file)
    public function relatedItem()
    {
        return $this->morphTo();
    }
}