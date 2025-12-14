<?php
// No namespace for global helper functions
// File: app/Helpers/helpers.php

use Illuminate\Support\Str;

if (!function_exists('safeDecrypt')) {
    /**
     * Safely decrypt a value, returning a fallback if decryption fails
     */
    function safeDecrypt($value, $fallback = '')
    {
        if (blank($value)) {
            return $fallback;
        }

        if (!is_string($value)) {
            return $value;
        }

        // Check if it looks encrypted
        if (!looksLikeEncrypted($value)) {
            return $value;
        }

        try {
            $decrypted = decrypt($value);
            return $decrypted ?: $fallback;
        } catch (\Exception $e) {
            \Log::warning('Decryption failed: ' . substr($value, 0, 50));
            return $fallback;
        }
    }
}

if (!function_exists('looksLikeEncrypted')) {
    /**
     * Check if a value looks like encrypted data
     */
    function looksLikeEncrypted($value)
    {
        if (!is_string($value) || trim($value) === '') {
            return false;
        }
        
        if (base64_decode($value, true) === false) {
            return false;
        }
        
        $decoded = base64_decode($value);
        return strlen($decoded) > 16 && strlen($value) > 20;
    }
}