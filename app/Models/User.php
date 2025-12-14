<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'status', 'profile_picture','address',  'email_verified_at','description', 'program'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // Do NOT cast program as encrypted since it's plain text in DB
    ];

    // Add these accessors for decrypted values
    public function getDecryptedNameAttribute()
    {
        try {
            return $this->name ? Crypt::decryptString($this->name) : null;
        } catch (\Exception $e) {
            return $this->name;
        }
    }

    public function getDecryptedUsernameAttribute()
    {
        try {
            return $this->username ? Crypt::decryptString($this->username) : null;
        } catch (\Exception $e) {
            return $this->username;
        }
    }

    public function getDecryptedEmailAttribute()
    {
        try {
            return $this->email ? Crypt::decryptString($this->email) : null;
        } catch (\Exception $e) {
            return $this->email;
        }
    }

    // Program is NOT encrypted, so just return as-is
    public function getDecryptedProgramAttribute()
    {
        return $this->program; // Plain text
    }

    // Add these mutators for automatic encryption
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? Crypt::encryptString($value) : null;
    }

    // Program is NOT encrypted, so set directly
    public function setProgramAttribute($value)
    {
        $this->attributes['program'] = $value; // Plain text
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }
    
    // Helper method to get all users with decrypted values
    public static function getAllWithDecrypted()
    {
        return self::all()->map(function ($user) {
            $user->name_decrypted = $user->decrypted_name;
            $user->username_decrypted = $user->decrypted_username;
            $user->email_decrypted = $user->decrypted_email;
            $user->program_decrypted = $user->decrypted_program;
            return $user;
        });
    }

    /**
     * Get the archive files uploaded by this user
     */
    public function uploadedFiles()
    {
        return $this->hasMany(ArchiveFile::class, 'uploaded_by');
    }
/**
     * Get the program associated with this user
     * Assuming you have a Program model
     */
    public function userProgram()
    {
        return $this->belongsTo(\App\Models\Program::class, 'program', 'id');
    }

}