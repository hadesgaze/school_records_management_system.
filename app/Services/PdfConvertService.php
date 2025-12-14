<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PdfConvertService
{
    /**
     * Convert any office/image doc to PDF using LibreOffice.
     * Returns absolute path to the produced PDF file under storage/app/tmp.
     */
    public function toPdf(UploadedFile $file): string
    {
        // If already PDF, normalize to tmp and return path
        if (strtolower($file->getClientOriginalExtension()) === 'pdf' || $file->getMimeType() === 'application/pdf') {
            $tmp = storage_path('app/tmp');
            @mkdir($tmp, 0775, true);
            $target = $tmp.'/'.Str::uuid().'.pdf';
            $file->move($tmp, basename($target));
            return $target;
        }

        // 1) Put the source file in a unique temp dir
        $tmpDir = storage_path('app/tmp/'.Str::uuid());
        @mkdir($tmpDir, 0775, true);
        $srcPath = $tmpDir.'/'.$file->getClientOriginalName();
        $file->move($tmpDir, $file->getClientOriginalName());

        // 2) Resolve LibreOffice binary
        $lo = $this->loBinary();
        if (!is_file($lo)) {
            throw new \RuntimeException("LibreOffice binary not found at: {$lo}. Set LIBREOFFICE_BIN in .env to your soffice.exe path.");
        }

        // 3) Run conversion (spaces in paths are fine when using array args)
        $process = new Process([$lo, '--headless', '--convert-to', 'pdf', $srcPath, '--outdir', $tmpDir]);
        $process->setTimeout(120);

        try {
            $process->mustRun();
        } catch (\Throwable $e) {
            $out = $process->getOutput();
            $err = $process->getErrorOutput();
            throw new \RuntimeException("LibreOffice conversion failed. {$e->getMessage()}\nOUT: {$out}\nERR: {$err}");
        }

        $pdfPath = preg_replace('/\.[^.]+$/', '.pdf', $srcPath);
        if (!is_file($pdfPath)) {
            throw new \RuntimeException('PDF conversion failed (output file missing).');
        }
        return $pdfPath;
    }

    /**
     * Find soffice.exe. Prefer .env/config; fallback to common Windows paths; last resort 'soffice' on PATH.
     */
    protected function loBinary(): string
    {
        // Prefer config/env (works with config:cache)
        $fromEnv = (string) (config('services.libreoffice.bin') ?? env('LIBREOFFICE_BIN', ''));
        if ($fromEnv !== '') {
            return $fromEnv;
        }

        // Windows fallbacks
        if (stripos(PHP_OS, 'WIN') === 0) {
            $candidates = [
                'C:\Program Files\LibreOffice\program\soffice.exe',
                'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
            ];
            foreach ($candidates as $c) {
                if (is_file($c)) return $c;
            }
        }

        // POSIX fallback to PATH (Linux/macOS)
        return 'soffice';
    }
}
