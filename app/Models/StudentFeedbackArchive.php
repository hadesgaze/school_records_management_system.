<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeedbackArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'original_name',
        'semester',
        'school_year',
        'year_level',
        'section',
        'mime_type',
        'size',
        'path',              // original (public)
        'compressed_path',   // zip (public)
        'is_compressed',
        'encrypted_path',    // AES (secure)
        'is_encrypted',
        'sha256',
        'status',            // Pending | Submitted to Chair | Submitted to Dean | Approved | Rejected
        'user_id',
    ];

    protected $casts = [
        'is_compressed' => 'boolean',
        'is_encrypted'  => 'boolean',
        'size'          => 'integer',
    ];

       
public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}

public function getFacultyNameAttribute()
{
    return $this->user->name ?? 'N/A';
}

public function getProgramAttribute()
{
    return $this->user->program ?? 'N/A';
}
}
