<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use HasFactory;

    protected $table = 'research';

    protected $fillable = [
        'title','date_published','authors',
        'file_name','original_name','mime_type','size',
        'compressed_path',
        // AES + workflow
        'encrypted_path','is_encrypted','sha256','status',
        'user_id',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'size'         => 'integer',
        'date_published' => 'date',
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
