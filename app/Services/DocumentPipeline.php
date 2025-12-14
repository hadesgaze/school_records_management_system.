<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentPipeline
{
    public function __construct(
        protected PdfConvertService $pdf,
        protected FileEncryptService $enc
    ) {}

    /**
     * Full pipeline: validate (in controller), convert -> encrypt -> store
     * Returns metadata array to persist in the DB.
     */
    public function handleUpload(UploadedFile $file, string $bucketDir, ?string $desiredPdfName = null): array
    {
        $originalName = $file->getClientOriginalName();
        $originalMime = $file->getClientMimeType();
        $originalSize = $file->getSize();

        // 1) Convert to PDF (or normalize pdf)
        $pdfPath = $this->pdf->toPdf($file);

        // 2) Decide final filename (no spaces for safety)
        $cleanName = $desiredPdfName
            ? $desiredPdfName
            : pathinfo($originalName, PATHINFO_FILENAME);
        $pdfFileName = Str::slug($cleanName).'_'.Str::random(6).'.pdf';

        // 3) Encrypt & store in secure disk
        [$encryptedPath, $sha256, $storedName] = $this->enc->encryptAndStore($pdfPath, $bucketDir, pathinfo($pdfFileName, PATHINFO_FILENAME));

        // cleanup temp
        @unlink($pdfPath);

        return [
            'original_filename' => $originalName,
            'original_mime'     => $originalMime,
            'original_size'     => $originalSize,
            'pdf_filename'      => $pdfFileName,
            'encrypted_path'    => $encryptedPath,
            'is_encrypted'      => true,
            'sha256'            => $sha256,
            'converted_to_pdf_at' => now(),
        ];
    }
}
