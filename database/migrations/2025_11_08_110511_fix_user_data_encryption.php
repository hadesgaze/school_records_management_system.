<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class FixUserDataEncryption extends Migration
{
    public function up()
    {
        // Get all users
        $users = User::all();
        
        foreach ($users as $user) {
            try {
                // Get the raw attributes
                $attributes = $user->getAttributes();
                
                // Process each attribute that should be encrypted
                $encryptable = ['name', 'email', 'username', 'address', 'program', 'description'];
                
                foreach ($encryptable as $field) {
                    if (isset($attributes[$field]) && $attributes[$field] !== null) {
                        $value = $attributes[$field];
                        
                        // Check if it's already properly encrypted
                        $isEncrypted = $this->isProperlyEncrypted($value);
                        
                        if (!$isEncrypted && !empty(trim($value))) {
                            // Encrypt the plain text value
                            $user->$field = $value; // This will trigger the mutator to encrypt
                        }
                    }
                }
                
                // Save without triggering events to avoid loops
                User::withoutEvents(function () use ($user) {
                    $user->save();
                });
                
            } catch (\Exception $e) {
                \Log::error("Failed to process user {$user->id}: " . $e->getMessage());
                continue;
            }
        }
    }
    
    private function isProperlyEncrypted($value)
    {
        if (!is_string($value) || empty(trim($value))) {
            return false;
        }
        
        try {
            // Try to decrypt - if it works, it's properly encrypted
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function down()
    {
        // This migration cannot be safely reversed
    }
}