<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FileEncryptService
{
    /**
     * Encrypt a file (string $absolutePath) and store it on the "secure" disk.
     * Returns [encryptedRelativePath, sha256, storedFilename]
     */
    public function encryptAndStore(string $absolutePath, string $dir, string $storeFilename): array
    {
        $contents = file_get_contents($absolutePath);
        // AES-256-CBC via Laravel Crypt (key from APP_KEY)
        $encrypted = Crypt::encryptString($contents);

        $relPath = trim($dir, '/').'/'.$storeFilename.'.enc';
        Storage::disk('secure')->put($relPath, $encrypted);

        $sha256 = hash('sha256', $encrypted);

        return [$relPath, $sha256, $storeFilename];
    }

    /**
     * Stream a previously encrypted file to the browser as a PDF.
     */
    public function streamDecryptedPdf(string $encryptedRelativePath, string $downloadAs = 'document.pdf')
    {
        if (!Storage::disk('secure')->exists($encryptedRelativePath)) {
            abort(404);
        }
        $encrypted = Storage::disk('secure')->get($encryptedRelativePath);
        $plain = Crypt::decryptString($encrypted);

        return response($plain, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$downloadAs.'"',
            'Content-Length' => strlen($plain),
        ]);
    }
}
