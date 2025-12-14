<?php

namespace App;

// Make sure all these 'use' statements are here
use Illuminate;
use Illuminate\Contracts\Encryption\DecryptException; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    public $table = 'users';

    public $fillable = [
        'name', 'email', 'username', 'address', 'description', 
        'password', 'status', 'role', 'program', 'profile_picture'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'username' => 'string',
        'email' => 'string',
        'address' => 'string',
        'description' => 'string',
        'status' => 'string',
        'program' => 'string',
        'email_verified_at' => 'datetime',
    ];

    public $timestamps = true;
    
    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'email|nullable|unique:users,email',
        'username' => 'required|string|max:255|unique:users,username',
    ];

    //
    // -----------------------------------------------------------------
    //  FIXED ENCRYPTION / DECRYPTION
    // -----------------------------------------------------------------
    //

    /**
     * Safe decrypt method that handles both encrypted and plain text.
     */
    private function safeDecrypt($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }
        
        try {
            return Crypt::decryptString($value);
        } 
        catch (DecryptException $e) { 
            // If it fails (it's plain text), return the value as-is.
            return $value; 
        } 
        catch (\Throwable $e) { 
            \Log::warning('Decryption failed (Throwable): ' . substr($value, 0, 50));
            return $value;
        }
    }

    /**
     * Accessor for Name - Automatically decrypt when accessed
     */
    public function getNameAttribute($value)
    {
        return $this->safeDecrypt($value);
    }

    /**
     * Accessor for Email - Automatically decrypt when accessed
     */
    public function getEmailAttribute($value)
    {
        return $this->safeDecrypt($value);
    }

    /**
     * Accessor for Username - Automatically decrypt when accessed
     */
    public function getUsernameAttribute($value)
    {
        return $this->safeDecrypt($value);
    }

    /**
     * Accessor for Address - Automatically decrypt when accessed
     */
    public function getAddressAttribute($value)
    {
        return $this->safeDecrypt($value);
    }

    /**
     * Accessor for Program - Automatically decrypt when accessed
     */
    public function getProgramAttribute($value)
    {
        return $this->safeDecrypt($value);
    }

    /**
     * Accessor for Description - Automatically decrypt when accessed
     */
    public function getDescriptionAttribute($value)
    {
        return $this->safeDecrypt($value);
    }

    /**
     * Mutator for Name - Automatically encrypt when setting
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encryptString($value);
    }

    /**
     * Mutator for Email - Automatically encrypt when setting
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = Crypt::encryptString($value);
    }

    /**
     * Mutator for Username - Automatically encrypt when setting
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = Crypt::encryptString($value);
    }

    /**
     * Mutator for Address - Automatically encrypt when setting
     */
    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Mutator for Program - Automatically encrypt when setting
     */
    public function setProgramAttribute($value)
    {
        $this->attributes['program'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Mutator for Description - Automatically encrypt when setting
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = $value ? Crypt::encryptString($value) : null;
    }

    //
    // -----------------------------------------------------------------
    //  RAW ATTRIBUTES
    // -----------------------------------------------------------------
    //
    
    /**
     * Get the raw, un-mutated value of an attribute.
     * This signature MUST match the parent Model class.
     */
    public function getRawOriginal($key = null, $default = null)
    {
        if ($key === null) {
            return $this->getAttributes();
        }
        return $this->getAttributes()[$key] ?? $default;
    }

    public function getRawNameAttribute()
    {
        return $this->getRawOriginal('name');
    }
    public function getRawProgramAttribute()
    {
        return $this->getRawOriginal('program');
    }

    //
    // -----------------------------------------------------------------
    //  OTHER FUNCTIONS (Unchanged)
    // -----------------------------------------------------------------
    //

    public function getIsSuperAdminAttribute()
    {
        return $this->id === 1;
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($user) {
            if (!empty($user->role)) {
                try {
                    $user->syncRoles([$user->role]);
                } catch (\Exception $e) {
                    \Log::error('Failed to sync roles for user ' . $user->id . ': ' . $e->getMessage());
                }
            }
        });
    }

    public function scopeFaculty($query)
    {
        return $query->where('role', 'faculty');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function getDisplayNameAttribute()
    {
        return $this->name; // Uses the accessor
    }

    public function getDisplayEmailAttribute()
    {
        return $this->email; // Uses the accessor
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'ACTIVE';
    }

    public static function getFacultyPrograms()
    {
        return static::where('role', 'faculty')
            ->whereNotNull('program')
            ->where('program', '!=', '')
            ->get()
            ->pluck('program') // This will use the auto-decrypting accessor
            ->unique()
            ->sort()
            ->values();
    }
}