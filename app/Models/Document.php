<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'title', 'file_path', 'file_size', 'file_type', 'uploaded_by'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function archive()
    {
        return $this->hasOne(Archive::class);
    }
}