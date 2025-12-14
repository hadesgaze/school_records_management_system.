<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveFile extends Model
{
    use HasFactory;

    protected $table = 'archive_files';

    protected $fillable = [
        'category_id',
        'file_path',
        'original_name',
        'file_size',
        'compressed_size',
        'is_compressed',
        'compression_ratio',
        'file_type',
        'uploaded_by',
        'field_data',
        'semester',        
        'school_year',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'field_data' => 'array',
        'is_compressed' => 'boolean',

    ];

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get the category this archive file belongs to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who uploaded this file
     */
    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    /**
     * Get the program through the uploader
     */
    public function program()
    {
        return $this->hasOneThrough(
            \App\Models\Program::class, // Target model
            \App\Models\User::class,    // Intermediate model
            'id',                       // Foreign key on intermediate model (User)
            'id',                       // Foreign key on target model (Program)  
            'uploaded_by',              // Local key on this model (ArchiveFile)
            'program'                   // Local key on intermediate model (User)
        );
    }

    /**
     * Get program name directly from uploader
     */
    public function getProgramNameAttribute()
    {
        return $this->uploader ? $this->uploader->program : null;
    }

     // Calculate compression ratio when saving
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            if ($model->is_compressed && $model->file_size && $model->compressed_size) {
                $model->compression_ratio = round((1 - $model->compressed_size / $model->file_size) * 100, 2);
            }
        });
    }
    
    // Get storage space saved
    public function getSpaceSavedAttribute()
    {
        if ($this->is_compressed && $this->file_size && $this->compressed_size) {
            return $this->file_size - $this->compressed_size;
        }
        return 0;
    }
}
