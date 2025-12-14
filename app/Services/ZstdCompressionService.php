<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ZstdCompressionService
{
    /**
     * Compress file using zstd
     *
     * @param string $filePath
     * @param int $compressionLevel (1-19, 3 is default)
     * @return string|null Path to compressed file
     */
    public function compressFile(string $filePath, int $compressionLevel = 3): ?string
    {
        try {
            if (!extension_loaded('zstd')) {
                Log::warning('Zstd extension not loaded, skipping compression');
                return null;
            }

            if (!file_exists($filePath)) {
                throw new Exception("File not found: {$filePath}");
            }

            // Read original file
            $originalContent = file_get_contents($filePath);
            if ($originalContent === false) {
                throw new Exception("Failed to read file: {$filePath}");
            }

            // Compress content
            $compressedContent = zstd_compress($originalContent, $compressionLevel);
            if ($compressedContent === false) {
                throw new Exception("Zstd compression failed for: {$filePath}");
            }

            // Save compressed file
            $compressedPath = $filePath . '.zst';
            if (file_put_contents($compressedPath, $compressedContent) === false) {
                throw new Exception("Failed to write compressed file: {$compressedPath}");
            }

            // Clean up original file
            unlink($filePath);

            Log::info("File compressed successfully", [
                'original_size' => strlen($originalContent),
                'compressed_size' => strlen($compressedContent),
                'compression_ratio' => round((1 - strlen($compressedContent) / strlen($originalContent)) * 100, 2),
                'original_path' => $filePath,
                'compressed_path' => $compressedPath
            ]);

            return $compressedPath;

        } catch (Exception $e) {
            Log::error('Zstd compression failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Decompress zstd file
     *
     * @param string $filePath
     * @return string|null Decompressed content
     */
    public function decompressFile(string $filePath): ?string
    {
        try {
            if (!extension_loaded('zstd')) {
                Log::warning('Zstd extension not loaded');
                return file_get_contents($filePath);
            }

            if (!file_exists($filePath)) {
                throw new Exception("File not found: {$filePath}");
            }

            $compressedContent = file_get_contents($filePath);
            if ($compressedContent === false) {
                throw new Exception("Failed to read compressed file: {$filePath}");
            }

            $decompressedContent = zstd_uncompress($compressedContent);
            if ($decompressedContent === false) {
                throw new Exception("Zstd decompression failed for: {$filePath}");
            }

            return $decompressedContent;

        } catch (Exception $e) {
            Log::error('Zstd decompression failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get compression information
     *
     * @param string $filePath
     * @return array|null
     */
    public function getCompressionInfo(string $filePath): ?array
    {
        try {
            if (!file_exists($filePath)) {
                return null;
            }

            $isCompressed = pathinfo($filePath, PATHINFO_EXTENSION) === 'zst';
            $size = filesize($filePath);
            $originalSize = null;

            if ($isCompressed) {
                $decompressed = $this->decompressFile($filePath);
                $originalSize = $decompressed ? strlen($decompressed) : null;
            }

            return [
                'is_compressed' => $isCompressed,
                'compressed_size' => $size,
                'original_size' => $originalSize,
                'compression_ratio' => $originalSize ? round((1 - $size / $originalSize) * 100, 2) : null,
                'extension' => pathinfo($filePath, PATHINFO_EXTENSION)
            ];

        } catch (Exception $e) {
            Log::error('Failed to get compression info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Stream decompressed file for download
     *
     * @param string $compressedPath
     * @return resource|null
     */
    public function streamDecompressed(string $compressedPath)
    {
        try {
            $decompressedContent = $this->decompressFile($compressedPath);
            if (!$decompressedContent) {
                return null;
            }

            // Create temporary file for streaming
            $tempFile = tempnam(sys_get_temp_dir(), 'decompressed_');
            file_put_contents($tempFile, $decompressedContent);

            // Return file stream
            return fopen($tempFile, 'r');

        } catch (Exception $e) {
            Log::error('Failed to stream decompressed file: ' . $e->getMessage());
            return null;
        }
    }
}