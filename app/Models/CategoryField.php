<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryField extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'type',
        'slug',
        'description',
        'options',
        'order',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    // Automatically generate slug when creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($field) {
            if (empty($field->slug)) {
                $field->slug = Str::slug($field->name);
            }
        });

        static::updating(function ($field) {
            if ($field->isDirty('name') && empty($field->slug)) {
                $field->slug = Str::slug($field->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Auto-format comma-separated options to JSON array
    public function setOptionsAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['options'] = json_encode(
                array_map('trim', explode(',', $value))
            );
        } else {
            $this->attributes['options'] = json_encode($value);
        }
    }
}