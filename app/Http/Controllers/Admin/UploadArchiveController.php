<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\ArchiveFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ActivityLog;
use App\Services\ZstdCompressionService; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Services\DeanChairNotificationService;


class UploadArchiveController extends Controller
{


    protected $zstdService;
    
    public function __construct()
    {
        $this->zstdService = new ZstdCompressionService();
    }


     /**
     * Display admin dashboard with statistics
     */
    public function dashboard()
    {
        // === USER STATISTICS ===
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'ACTIVE')->count();
        $inactiveUsers = User::where('status', 'INACTIVE')->count();
        $suspendedUsers = User::where('status', 'SUSPENDED')->count();
        
        // Users by role
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();
        
        // Users by program
        $usersByProgram = User::select('program', DB::raw('count(*) as total'))
            ->whereNotNull('program')
            ->where('program', '!=', '')
            ->groupBy('program')
            ->pluck('total', 'program')
            ->toArray();

        // === ARCHIVE FILE STATISTICS ===
        $totalFiles = ArchiveFile::count();
        $totalFileSize = ArchiveFile::sum('file_size');
        $filesThisMonth = ArchiveFile::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Files by category
        $filesByCategory = ArchiveFile::with('category')
            ->select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->get()
            ->mapWithKeys(function($item) {
                $categoryName = $item->category ? $item->category->name : 'Uncategorized';
                return [$categoryName => $item->total];
            })
            ->toArray();
        
        // Files by type
        $filesByType = ArchiveFile::select('file_type', DB::raw('count(*) as total'))
            ->groupBy('file_type')
            ->pluck('total', 'file_type')
            ->toArray();

        // === CATEGORY STATISTICS ===
        $totalCategories = Category::count();
        $categoriesWithFiles = ArchiveFile::distinct('category_id')->count('category_id');

        // === RECENT ACTIVITY ===
        $recentActivities = ActivityLog::with(['user:id,name,username,profile_picture'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        // Decrypt activity logs
        $recentActivities->transform(function ($log) {
            if ($log->user) {
                $rawName = $log->user->getRawOriginal('name');
                $log->user->name = $this->tryAesDecrypt($rawName);
            }
            $log->action = $this->tryAesDecrypt($log->getRawOriginal('action'));
            $log->module = $this->tryAesDecrypt($log->getRawOriginal('module'));
            return $log;
        });

        // === RECENT UPLOADS ===
        $recentUploads = ArchiveFile::with(['uploader:id,name,username', 'category:id,name'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        // Decrypt uploader names
        $recentUploads->transform(function ($file) {
            if ($file->uploader) {
                $rawName = $file->uploader->getRawOriginal('name');
                $file->uploader->name = $this->tryAesDecrypt($rawName);
            }
            return $file;
        });

        // === CHART DATA - Last 7 Days ===
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days->push([
                'date' => $date->format('M d'),
                'users' => User::whereDate('created_at', $date)->count(),
                'files' => ArchiveFile::whereDate('created_at', $date)->count(),
            ]);
        }

        // === MONTHLY TRENDS ===
        $monthlyData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyData->push([
                'month' => $date->format('M Y'),
                'users' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'files' => ArchiveFile::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ]);
        }

        // === STORAGE STATISTICS ===
        $storageStats = [
            'total_size' => $totalFileSize,
            'formatted_size' => $this->formatBytes($totalFileSize),
            'average_file_size' => $totalFiles > 0 ? $totalFileSize / $totalFiles : 0,
            'formatted_average' => $this->formatBytes($totalFiles > 0 ? $totalFileSize / $totalFiles : 0),
        ];

        return view('admin.dashboard', compact(
    'totalUsers',
    'activeUsers',
    'inactiveUsers',
    'suspendedUsers',
    'usersByRole',
    'usersByProgram',
    'totalFiles',
    'filesThisMonth',
    'filesByCategory',
    'filesByType',
    'totalCategories',
    'categoriesWithFiles',
    'recentActivities',
    'recentUploads',
    'last7Days',
    'monthlyData',
    'storageStats'
));

    }

    /**
     * Safely decrypt AES-encrypted values
     */
    private function tryAesDecrypt($value)
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }



    /**
     * Display upload files page
     */
    public function uploadFiles()
    {
        $categories = Category::with(['fields' => function($query) {
            $query->orderBy('order', 'asc');
        }])
        ->where('accessible_roles', 'like', '%admin%')
        ->orderBy('name')
        ->get();

        return view('admin.upload_files', compact('categories'));
    }

    /**
     * Upload document & store metadata with compression
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'document_file' => 'required|file|max:10240',
            'semester'      => 'required|in:1st Semester,2nd Semester,Summer',
            'school_year'   => 'required|regex:/^\d{4}-\d{4}$/',
        ]);

        try {
            // Check if category is accessible to admin
            $category = Category::where('id', $request->category_id)
                ->where('accessible_roles', 'like', '%admin%')
                ->first();

            if (!$category) {
                return back()->with('error', 'Category not accessible.');
            }

            $file = $request->file('document_file');
            $filename = time().'_'.Str::random(8).'_'.$file->getClientOriginalName();
            
            // Store file temporarily
            $tempPath = $file->storeAs('temp', $filename, 'local');
            $fullTempPath = storage_path('app/' . $tempPath);
            
            // Compress the file
            $compressedPath = $this->zstdService->compressFile($fullTempPath);
            
            if (!$compressedPath) {
                // If compression fails, use original file
                Storage::disk('local')->delete($tempPath);
                $path = $file->storeAs('documents', $filename, 'public');
                $isCompressed = false;
            } else {
                // Move compressed file to public storage
                $compressedFilename = pathinfo($filename, PATHINFO_FILENAME) . '.zst';
                $path = 'documents/' . $compressedFilename;
                Storage::disk('public')->put($path, file_get_contents($compressedPath));
                
                // Clean up temp compressed file
                unlink($compressedPath);
                $isCompressed = true;
            }
            
            $fields = CategoryField::where('category_id', $request->category_id)
                ->orderBy('order', 'asc')
                ->get();
                
            $fieldData = [];

            foreach ($fields as $field) {
                $value = $request->input('fields.' . $field->slug);
                if ($field->is_required && !$value) {
                    // Clean up uploaded file if validation fails
                    Storage::disk('public')->delete($path);
                    return back()->with('error', $field->name . ' is required!');
                }
                $fieldData[$field->slug] = $value;
            }

            $archiveFile = ArchiveFile::create([
                'category_id'   => $request->category_id,
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size'     => $file->getSize(),
                'compressed_size' => $isCompressed ? Storage::disk('public')->size($path) : null,
                'is_compressed' => $isCompressed,
                'file_type'     => strtolower($file->getClientOriginalExtension()),
                'uploaded_by'   => Auth::id(),
                'field_data'    => json_encode($fieldData),
                'semester'      => $request->semester,     
                'school_year'   => $request->school_year, 
            ]);

            // ✨ Add Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Uploaded a document' . ($isCompressed ? ' (compressed)' : ''),
                'data' => json_encode([
                    'file' => $archiveFile->original_name,
                    'category' => $category->name,
                    'compressed' => $isCompressed,
                    'original_size' => $file->getSize(),
                    'compressed_size' => $isCompressed ? Storage::disk('public')->size($path) : null,
                    'semester'      => $request->semester,     
                    'school_year'   => $request->school_year,
                ]),
            ]);

            (new DeanChairNotificationService())->notifyUpload($archiveFile);

            return back()->with('success', 'File successfully archived' . ($isCompressed ? ' & compressed' : '') . '!');
        } 
        catch (\Exception $e) {
            \Log::error($e);
            return back()->with('error', 'Upload failed.');
        }
    }

    /**
     * Download archive file with decompression
     */
    public function downloadArchiveFile($id)
    {
        try {
            $file = ArchiveFile::where('uploaded_by', Auth::id())->findOrFail($id);  
            $filePath = storage_path('app/public/' . $file->file_path);
            
            if (!file_exists($filePath)) {
                return back()->with('error', 'File not found on server.');
            }
            
            if ($file->is_compressed) {
                // Stream decompressed content
                $stream = $this->zstdService->streamDecompressed($filePath);
                
                if (!$stream) {
                    return back()->with('error', 'Failed to decompress file.');
                }
                
                return response()->stream(
                    function () use ($stream) {
                        fpassthru($stream);
                        fclose($stream);
                    },
                    200,
                    [
                        'Content-Type' => Storage::mimeType($file->original_name),
                        'Content-Disposition' => 'attachment; filename="' . $file->original_name . '"',
                        'Content-Length' => $file->file_size,
                    ]
                );
            } else {
                // Regular file download
                return response()->download($filePath, $file->original_name);
            }
            
        } catch (\Exception $e) {
            \Log::error('Download file error: ' . $e->getMessage());
            return back()->with('error', 'Unable to download file.');
        }
    }

    
/**
 * View archive file (handles both compressed and uncompressed)
 */
public function viewFile($id)
{
    try {
        $file = ArchiveFile::where('uploaded_by', Auth::id())->findOrFail($id);
        $filePath = storage_path('app/public/' . $file->file_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found on server.');
        }
        
        // Only allow viewable file types
        $viewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'txt'];
        if (!in_array($file->file_type, $viewableTypes)) {
            return back()->with('error', 'This file type cannot be viewed in browser.');
        }
        
        if ($file->is_compressed) {
            // Decompress and stream for viewing
            $stream = $this->zstdService->streamDecompressed($filePath);
            
            if (!$stream) {
                return back()->with('error', 'Failed to decompress file.');
            }
            
            // Get the appropriate content type
            $mimeTypes = [
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'txt' => 'text/plain',
            ];
            
            $contentType = $mimeTypes[$file->file_type] ?? 'application/octet-stream';
            
            return response()->stream(
                function () use ($stream) {
                    fpassthru($stream);
                    fclose($stream);
                },
                200,
                [
                    'Content-Type' => $contentType,
                    'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
                    'Content-Length' => $file->file_size,
                ]
            );
        } else {
            // Regular file - serve directly for viewing
            $mimeTypes = [
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'txt' => 'text/plain',
            ];
            
            $contentType = $mimeTypes[$file->file_type] ?? 'application/octet-stream';
            
            return response()->file($filePath, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            ]);
        }
        
    } catch (\Exception $e) {
        \Log::error('View file error: ' . $e->getMessage());
        return back()->with('error', 'Unable to view file.');
    }
}


    /**
     * View file details
     */
    public function viewFileDetails($id)
    {
        try {
            $admin = Auth::user();
            
            // Admin can view all files from dean, chairperson, and faculty
            $file = ArchiveFile::with(['category.fields', 'uploader'])
                ->findOrFail($id);
                
            // Since admin can view all files, no authorization check needed
            
            $fieldData = json_decode($file->field_data, true) ?? [];

            return view('admin.view_file_details', compact('file', 'fieldData'));

        } catch (\Exception $e) {
            \Log::error('View file details error: ' . $e->getMessage());
            return redirect()->route('admin.archive_files')
                ->with('error', 'Unable to load file details.');
        }
    }

    /**
     * Display archived files page - Admin can see all files
     */
    public function archivedFiles()
    {
        try {
            // Admin can see all files, so no filtering needed
            $archivedFiles = ArchiveFile::with(['category', 'uploader'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('admin.archived_files', compact('archivedFiles'));
        } catch (\Exception $e) {
            \Log::error('Archived files error: ' . $e->getMessage());
            return back()->with('error', 'Error loading archived files: ' . $e->getMessage());
        }
    }


    /**
     * Delete archive file - Admin can delete any file
     */
    public function deleteArchiveFile($id)
    {
        try {
            $admin = Auth::user();
            
            // Get the file
            $file = ArchiveFile::with('uploader')->findOrFail($id);
            
            // Admin can delete any file, no authorization check needed
            
            // Delete physical file
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            
            // Delete database record
            $file->delete();
            
            return redirect()->route('admin.archive_files')
                ->with('success', 'File deleted successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Delete file error: ' . $e->getMessage());
            return back()->with('error', 'Unable to delete file.');
        }
    }

    /**
     * Restore archive file (if you have an 'is_archived' field) - Admin can restore any file
     */
    public function restoreArchiveFile($id)
    {
        try {
            $admin = Auth::user();
            
            // Get the file
            $file = ArchiveFile::with('uploader')->findOrFail($id);
            
            // Admin can restore any file, no authorization check needed
            
            // If your ArchiveFile model has an 'is_archived' field, uncomment this:
            // $file->update(['is_archived' => false]);
            
            return redirect()->route('admin.archive_files')
                ->with('success', 'File restored successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Restore file error: ' . $e->getMessage());
            return back()->with('error', 'Unable to restore file.');
        }
    }
}