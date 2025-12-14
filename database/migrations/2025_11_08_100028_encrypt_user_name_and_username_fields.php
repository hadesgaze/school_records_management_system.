<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use App\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Process in chunks to avoid memory issues with large datasets
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->encryptUserData($user);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Process in chunks for rollback
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->decryptUserData($user);
            }
        });
    }

    private function encryptUserData(User $user): void
    {
        $originalName = $user->getRawOriginal('name');
        $originalUsername = $user->getRawOriginal('username');
        
        $nameIsEncrypted = $this->isEncrypted($originalName);
        $usernameIsEncrypted = $this->isEncrypted($originalUsername);
        
        $needsUpdate = false;
        
        // Encrypt name if not already encrypted
        if (!$nameIsEncrypted && !empty($originalName)) {
            $user->name = $originalName; // This will trigger the mutator to encrypt
            $needsUpdate = true;
        }
        
        // Encrypt username if not already encrypted
        if (!$usernameIsEncrypted && !empty($originalUsername)) {
            $user->username = $originalUsername; // This will trigger the mutator to encrypt
            $needsUpdate = true;
        }
        
        // Save only if changes were made
        if ($needsUpdate) {
            User::withoutEvents(function () use ($user) {
                $user->save();
            });
        }
    }

    private function decryptUserData(User $user): void
    {
        $originalName = $user->getRawOriginal('name');
        $originalUsername = $user->getRawOriginal('username');
        
        try {
            // Try to decrypt both fields
            $decryptedName = Crypt::decryptString($originalName);
            $decryptedUsername = Crypt::decryptString($originalUsername);
            
            // If decryption succeeds, set as plain text
            $user->name = $decryptedName;
            $user->username = $decryptedUsername;
            
            User::withoutEvents(function () use ($user) {
                $user->save();
            });
        } catch (\Exception $e) {
            // If decryption fails, data is probably already plain text - skip
        }
    }

    private function isEncrypted($value): bool
    {
        if (empty($value)) {
            return false;
        }
        
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
};