<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'accessible_roles',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'accessible_roles' => 'array',
    ];

    // Fix the relationship
     public function fields()
    {
        return $this->hasMany(CategoryField::class)->orderBy('order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getAccessibleRoles()
    {
        return is_array($this->accessible_roles) 
            ? $this->accessible_roles 
            : json_decode($this->accessible_roles, true) ?? [];
    }

    // Add this method to your Category model
public function getAccessibleRolesArrayAttribute()
{
    if (empty($this->accessible_roles)) {
        return [];
    }
    
    if (is_array($this->accessible_roles)) {
        return $this->accessible_roles;
    }
    
    return json_decode($this->accessible_roles, true) ?? [];
}
}