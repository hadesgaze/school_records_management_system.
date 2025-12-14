<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Models\ArchiveFile;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    /**
     * Helper method to safely decrypt data
     */
    private function safeDecrypt($value, $default = null)
    {
        if (empty($value)) {
            return $default;
        }
        
        // If it's already decrypted (not a serialized/encrypted string), return as is
        if (is_string($value) && !preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) {
            return $value;
        }
        
        try {
            // Try to decrypt using Laravel's Crypt
            return Crypt::decrypt($value);
        } catch (\Exception $e) {
            // If decryption fails, return the original value
            // It might already be decrypted or using different encryption
            return $value;
        }
    }

    /**
     * Display system reports
     */
    public function index(Request $request)
    {
        // Get filters from request
        $roleFilter = $request->get('role', '');
        $categoryFilter = $request->get('category', '');
        $programFilter = $request->get('program', '');
        $dateFrom = $request->get('from', '');
        $dateTo = $request->get('to', '');

        // ===== Users =====
        $usersQuery = User::query();
        
        if ($roleFilter) {
            $usersQuery->where('role', $roleFilter);
        }
        
        if ($programFilter) {
            $usersQuery->where('program', $programFilter);
        }
        
        if ($dateFrom && $dateTo) {
            $usersQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        $users = $usersQuery->get();
        
        // Try to decrypt user names and emails
        $users = $users->map(function($user) {
            $user->decrypted_name = $this->tryMultipleDecryptionMethods($user->name, $user->name);
            $user->decrypted_email = $this->tryMultipleDecryptionMethods($user->email, $user->email);
            return $user;
        });

        // ===== Archive Files =====
        $archiveFilesQuery = ArchiveFile::with(['uploader', 'category']);
        
        if ($roleFilter) {
            $archiveFilesQuery->whereHas('uploader', function($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            });
        }
        
        if ($categoryFilter) {
            $archiveFilesQuery->where('category_id', $categoryFilter);
        }
        
        if ($programFilter) {
            $archiveFilesQuery->whereHas('uploader', function($query) use ($programFilter) {
                $query->where('program', $programFilter);
            });
        }
        
        if ($dateFrom && $dateTo) {
            $archiveFilesQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        $archiveFiles = $archiveFilesQuery->get();
        
        // Try to decrypt uploader names if they exist
        $archiveFiles = $archiveFiles->map(function($file) {
            if ($file->uploader) {
                $file->uploader->decrypted_name = $this->tryMultipleDecryptionMethods($file->uploader->name, $file->uploader->name);
                $file->uploader->decrypted_email = $this->tryMultipleDecryptionMethods($file->uploader->email, $file->uploader->email);
            }
            return $file;
        });

        // ===== Statistics =====
        // User statistics by role
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->when($roleFilter, function($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->when($programFilter, function($query) use ($programFilter) {
                $query->where('program', $programFilter);
            })
            ->when($dateFrom && $dateTo, function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->groupBy('role')
            ->pluck('total', 'role');

        // User statistics by status
        $usersByStatus = User::select('status', DB::raw('count(*) as total'))
            ->when($roleFilter, function($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->when($programFilter, function($query) use ($programFilter) {
                $query->where('program', $programFilter);
            })
            ->when($dateFrom && $dateTo, function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->groupBy('status')
            ->pluck('total', 'status');

        // Archive statistics by category
        $archiveByCategory = ArchiveFile::with('category')
            ->select('category_id', DB::raw('count(*) as total'))
            ->when($roleFilter, function($query) use ($roleFilter) {
                $query->whereHas('uploader', function($q) use ($roleFilter) {
                    $q->where('role', $roleFilter);
                });
            })
            ->when($categoryFilter, function($query) use ($categoryFilter) {
                $query->where('category_id', $categoryFilter);
            })
            ->when($programFilter, function($query) use ($programFilter) {
                $query->whereHas('uploader', function($q) use ($programFilter) {
                    $q->where('program', $programFilter);
                });
            })
            ->when($dateFrom && $dateTo, function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->groupBy('category_id')
            ->get()
            ->mapWithKeys(function($item) {
                $categoryName = $item->category ? $item->category->name : 'Uncategorized';
                return [$categoryName => $item->total];
            });

        // Archive statistics by file type
        $archiveByFileType = ArchiveFile::select('file_type', DB::raw('count(*) as total'))
            ->when($roleFilter, function($query) use ($roleFilter) {
                $query->whereHas('uploader', function($q) use ($roleFilter) {
                    $q->where('role', $roleFilter);
                });
            })
            ->when($categoryFilter, function($query) use ($categoryFilter) {
                $query->where('category_id', $categoryFilter);
            })
            ->when($programFilter, function($query) use ($programFilter) {
                $query->whereHas('uploader', function($q) use ($programFilter) {
                    $q->where('program', $programFilter);
                });
            })
            ->when($dateFrom && $dateTo, function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->groupBy('file_type')
            ->pluck('total', 'file_type');

        // Total file size
        $totalFileSize = ArchiveFile::when($roleFilter, function($query) use ($roleFilter) {
                $query->whereHas('uploader', function($q) use ($roleFilter) {
                    $q->where('role', $roleFilter);
                });
            })
            ->when($categoryFilter, function($query) use ($categoryFilter) {
                $query->where('category_id', $categoryFilter);
            })
            ->when($programFilter, function($query) use ($programFilter) {
                $query->whereHas('uploader', function($q) use ($programFilter) {
                    $q->where('program', $programFilter);
                });
            })
            ->when($dateFrom && $dateTo, function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->sum('file_size');

        // Get all categories for filter dropdown
        $categories = Category::all();
        
        // Get unique programs from users for filter dropdown
        $programs = User::select('program')
            ->whereNotNull('program')
            ->where('program', '!=', '')
            ->distinct()
            ->orderBy('program')
            ->pluck('program');

        return view('admin.reports', compact(
            'users',
            'archiveFiles',
            'usersByRole',
            'usersByStatus',
            'archiveByCategory',
            'archiveByFileType',
            'totalFileSize',
            'categories',
            'programs',
            'roleFilter',
            'categoryFilter',
            'programFilter',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Try multiple decryption methods
     */
    private function tryMultipleDecryptionMethods($value, $default = null)
    {
        if (empty($value)) {
            return $default;
        }
        
        // Method 1: Check if it's already plain text (not base64 encoded)
        if (is_string($value) && !$this->isBase64Encoded($value)) {
            return $value;
        }
        
        // Method 2: Try Laravel's Crypt::decrypt
        try {
            return Crypt::decrypt($value);
        } catch (\Exception $e) {
            // Continue to other methods
        }
        
        // Method 3: Try base64 decode first, then decrypt
        if ($this->isBase64Encoded($value)) {
            $decoded = base64_decode($value);
            if ($decoded !== false) {
                // Try to see if it's JSON after decoding
                $json = json_decode($decoded, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($json['iv'], $json['value'], $json['mac'])) {
                    // It's Laravel encrypted format
                    try {
                        return Crypt::decrypt($value);
                    } catch (\Exception $e) {
                        // Couldn't decrypt, return decoded value
                        return $decoded;
                    }
                }
                return $decoded;
            }
        }
        
        // Method 4: Try simple base64 decode
        $decoded = base64_decode($value, true);
        if ($decoded !== false) {
            return $decoded;
        }
        
        // Method 5: Return original value
        return $value;
    }
    
    /**
     * Check if string is base64 encoded
     */
    private function isBase64Encoded($string)
    {
        if (!is_string($string)) {
            return false;
        }
        
        // Check if it's valid base64
        if (base64_decode($string, true) === false) {
            return false;
        }
        
        // Additional check: base64 strings usually have specific character set
        return preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string) === 1;
    }

    /**
     * Export reports in PDF or Excel format
     */
    public function export(Request $request, $type)
    {
        // Get filters from request
        $roleFilter = $request->get('role', '');
        $categoryFilter = $request->get('category', '');
        $programFilter = $request->get('program', '');
        $dateFrom = $request->get('from', '');
        $dateTo = $request->get('to', '');

        // Users data for export
        $usersQuery = User::query();
        
        if ($roleFilter) {
            $usersQuery->where('role', $roleFilter);
        }
        
        if ($programFilter) {
            $usersQuery->where('program', $programFilter);
        }
        
        if ($dateFrom && $dateTo) {
            $usersQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        $users = $usersQuery->get();
        
        // Prepare user data for export
        $users = $users->map(function($user) {
            return [
                'name' => $this->tryMultipleDecryptionMethods($user->name, $user->name),
                'email' => $this->tryMultipleDecryptionMethods($user->email, $user->email),
                'role' => $user->role,
                'status' => $user->status,
                'program' => $user->program,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        // Archive files data for export
        $archiveFilesQuery = ArchiveFile::with(['uploader', 'category']);
        
        if ($roleFilter) {
            $archiveFilesQuery->whereHas('uploader', function($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            });
        }
        
        if ($categoryFilter) {
            $archiveFilesQuery->where('category_id', $categoryFilter);
        }
        
        if ($programFilter) {
            $archiveFilesQuery->whereHas('uploader', function($query) use ($programFilter) {
                $query->where('program', $programFilter);
            });
        }
        
        if ($dateFrom && $dateTo) {
            $archiveFilesQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        $archiveFiles = $archiveFilesQuery->get();
        
        // Prepare archive data for export
        $archiveFiles = $archiveFiles->map(function($file) {
            $uploaderName = 'Unknown';
            $uploaderEmail = '';
            $uploaderProgram = 'N/A';
            
            if ($file->uploader) {
                $uploaderName = $this->tryMultipleDecryptionMethods($file->uploader->name, $file->uploader->name);
                $uploaderEmail = $this->tryMultipleDecryptionMethods($file->uploader->email, $file->uploader->email);
                $uploaderProgram = $file->uploader->program ?? 'N/A';
            }
            
            return [
                'original_name' => $file->original_name,
                'file_type' => $file->file_type,
                'file_size' => $this->formatBytes($file->file_size),
                'category' => $file->category ? $file->category->name : 'Uncategorized',
                'program' => $uploaderProgram,
                'uploader_name' => $uploaderName,
                'uploader_email' => $uploaderEmail,
                'uploader_role' => $file->uploader ? $file->uploader->role : 'Unknown',
                'created_at' => $file->created_at->format('Y-m-d H:i:s'),
                'description' => $file->description ?? '',
            ];
        });

        // Get statistics for export
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->when($roleFilter, function($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->when($programFilter, function($query) use ($programFilter) {
                $query->where('program', $programFilter);
            })
            ->when($dateFrom && $dateTo, function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->groupBy('role')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->role => $item->total];
            });

        $totalFileSize = ArchiveFile::when($roleFilter, function($query) use ($roleFilter) {
                $query->whereHas('uploader', function($q) use ($roleFilter) {
                    $q->where('role', $roleFilter);
                });
            })
            ->when($categoryFilter, function($query) use ($categoryFilter) {
                $query->where('category_id', $categoryFilter);
            })
            ->when($programFilter, function($query) use ($programFilter) {
                $query->whereHas('uploader', function($q) use ($programFilter) {
                    $q->where('program', $programFilter);
                });
            })
            ->when($dateFrom && $dateTo, function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->sum('file_size');

        if ($type === 'pdf') {
            $pdf = PDF::loadView('admin.reports-pdf', [
                'users' => $users,
                'archiveFiles' => $archiveFiles,
                'usersByRole' => $usersByRole,
                'totalFileSize' => $this->formatBytes($totalFileSize),
                'roleFilter' => $roleFilter,
                'categoryFilter' => $categoryFilter,
                'programFilter' => $programFilter,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'exportDate' => now()->format('F j, Y H:i:s'),
            ])->setPaper('a4', 'landscape');
            
            return $pdf->download('system_report_' . now()->format('Y-m-d_H-i-s') . '.pdf');
        }

        if ($type === 'excel') {
            return Excel::download(
                new ReportsExport($users, $archiveFiles, $usersByRole, $this->formatBytes($totalFileSize)), 
                'system_report_' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        return back()->with('error', 'Invalid export type');
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
}