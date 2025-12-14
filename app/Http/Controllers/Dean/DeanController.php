<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;
use App\Services\DeanChairNotificationService;
use App\Models\FacultyNotification;
use App\Helpers\LogActivity;
use Intervention\Image\Laravel\Facades\Image;
use App\Services\ZstdCompressionService; 
use App\Models\ActivityLog;
use App\Models\{
    Notification,
    User,
    Category,
    CategoryField,
    ArchiveFile
};

class DeanController extends Controller
{
    /* ==================== HELPER METHODS ==================== */
    protected $zstdService;
    protected $notificationService;
    
    public function __construct()
    {
        $this->zstdService = new ZstdCompressionService();
        $this->notificationService = new DeanChairNotificationService();
    }
    
    


    /**
     * Get decrypted program for a user
     */
    public function getDecryptedProgram($user)
    {
        if (!$user || !$user->program) {
            return "No Program";
        }

        try {
            // Get the raw encrypted value
            $rawProgram = $user->getRawOriginal('program');
            
            // Try to decrypt
            $decrypted = decrypt($rawProgram);
            return $decrypted ?: "No Program";
        } catch (\Exception $e) {
            // If decryption fails, return the raw value or fallback
            return $user->program ?: "No Program";
        }
    }
    
    /**
     * Get raw attribute value (bypass accessor)
     */
    private function raw($model, $attribute)
    {
        return $model->getRawOriginal($attribute) ?? $model->{$attribute};
    }

    /**
     * Enhanced decryption method
     */
    private function tryDecrypt($value)
    {
        if (blank($value)) {
            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        if (!$this->looksLikeEncrypted($value)) {
            return $value;
        }

        try {
            return decrypt($value);
        } catch (\Exception $e) {
            \Log::warning('Decryption failed for value: ' . substr($value, 0, 50));
            return null;
        }
    }

    /**
     * Better encrypted data detection
     */
    private function looksLikeEncrypted($value)
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

    /**
     * Generate avatar URL
     */
    private function avatarUrl($name, $profilePicture, $size = 150, $color = '0D8ABC')
    {
        if ($profilePicture && Storage::disk('public')->exists($profilePicture)) {
            return Storage::disk('public')->url($profilePicture);
        }

        $encodedName = urlencode($name ?: 'Faculty');
        return "https://ui-avatars.com/api/?name={$encodedName}&size={$size}&color=FFFFFF&background={$color}";
    }

    /**
     * Get faculty display name with fallback
     */
    private function getFacultyDisplayName($user)
    {
        $rawName = $this->raw($user, 'name');
        $decryptedName = $this->tryDecrypt($rawName);
        
        if ($decryptedName && $decryptedName !== $rawName) {
            return $decryptedName;
        }
        
        if ($this->looksLikeEncrypted($rawName)) {
            return "Faculty #" . $user->id;
        }
        
        return $rawName ?: "Faculty #" . $user->id;
    }

    /**
     * Get faculty display username with fallback
     */
    private function getFacultyDisplayUsername($user)
    {
        $rawUsername = $this->raw($user, 'username');
        $decryptedUsername = $this->tryDecrypt($rawUsername);
        
        if ($decryptedUsername && $decryptedUsername !== $rawUsername) {
            return $decryptedUsername;
        }
        
        if ($this->looksLikeEncrypted($rawUsername)) {
            return "user_" . $user->id;
        }
        
        return $rawUsername ?: "user_" . $user->id;
    }

    /**
     * Get faculty program with fallback
     */
    private function getFacultyProgram($user)
    {
        $rawProgram = $this->raw($user, 'program');
        $decryptedProgram = $this->tryDecrypt($rawProgram);
        
        if ($decryptedProgram && $decryptedProgram !== $rawProgram) {
            return $decryptedProgram;
        }
        
        if ($this->looksLikeEncrypted($rawProgram)) {
            return "Program";
        }
        
        return $rawProgram ?: "No Program";
    }

   
    /* ==================== DASHBOARD ==================== */
   public function dashboard()
{
    $user = Auth::user();
    
    // Get statistics
    $totalFaculty = User::where('role', 'faculty')->count();
    $totalChairperson = User::where('role', 'chairperson')->count();
    $totalPrograms = User::where('role', 'faculty')
        ->orWhere('role', 'chairperson')
        ->orWhere('role', 'dean')
        ->distinct('program')
        ->count('program');
    $totalArchivedFiles = ArchiveFile::count();
    
    // Get recent uploads (last 10 files)
    $recentUploads = ArchiveFile::with(['category', 'uploader'])
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
    
    // Get file type statistics
    $fileTypeStats = ArchiveFile::select('file_type', DB::raw('count(*) as count'))
        ->groupBy('file_type')
        ->get()
        ->map(function($item) {
            $colors = [
                'pdf' => '#FF6384',
                'doc' => '#36A2EB',
                'docx' => '#36A2EB',
                'xls' => '#4BC0C0',
                'xlsx' => '#4BC0C0',
                'jpg' => '#FFCE56',
                'jpeg' => '#FFCE56',
                'png' => '#9966FF',
                'default' => '#C9CBCF'
            ];
            
            return [
                'type' => $item->file_type ?: 'unknown',
                'count' => $item->count,
                'color' => $colors[$item->file_type] ?? $colors['default']
            ];
        });
    
    // Get recent activities
    $recentActivities = ActivityLog::with('user')
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();
    
    // Get notifications
    $unreadCount = Notification::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('receiver_role', $user->role);
        })
        ->where('is_read', false)
        ->count();
    
    $notifications = Notification::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('receiver_role', $user->role);
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    return view('dean.dashboard', compact(
        'totalFaculty',
        'totalChairperson',
        'totalPrograms',
        'totalArchivedFiles',
        'recentUploads',
        'fileTypeStats',
        'recentActivities',
        'unreadCount',
        'notifications'
    ));
}
    /**
     * Display upload files page
     */
     public function uploadFiles()
    {
        $categories = Category::with(['fields' => function($query) {
            $query->orderBy('order', 'asc');
        }])
        ->where('accessible_roles', 'like', '%dean%')
        ->orderBy('name')
        ->get();

        return view('dean.upload_files', compact('categories'));
    }

    /**
     * Display all notifications for dean - FIXED: Removed duplicate method
     */
   public function notifications()
{
    $dean = Auth::user();

    // Get notifications sent to this dean individually OR to the 'dean' role
    $notifications = Notification::where(function($query) use ($dean) {
            $query->where('user_id', $dean->id)
                  ->orWhere('receiver_role', 'dean');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    // Load possible recipients for the Dean (Admin, Chairperson, Faculty)
    $users = User::whereIn('role', ['admin', 'chairperson', 'faculty'])
        ->orderBy('name')
        ->get();

    // Count unread notifications using the same logic
    $unreadCount = Notification::where(function ($q) use ($dean) {
            $q->where('receiver_role', 'dean')
              ->orWhere('user_id', $dean->id);
        })
        ->where('is_read', false)
        ->count();

    return view('dean.notifications', [
        'notifications' => $notifications,
        'unreadCount' => $unreadCount,
        'users' => $users
    ]);
}
    
    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $user = Auth::user();
        $this->notificationService->markAsRead($id, $user);
        
        return back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user);
        
        return back()->with('success', 'All notifications marked as read.');
    }
    
    /**
     * Fetch notifications for AJAX (for real-time updates)
     */
    public function fetchNotifications()
    {
        $user = Auth::user();
        $unreadCount = $this->notificationService->getUnreadCount($user);
        $notifications = $this->notificationService->getNotifications($user, 10);
        
        return response()->json([
            'unreadCount' => $unreadCount,
            'notifications' => $notifications->map(function($n) {
                return [
                    'id' => $n->id,
                    'message' => $n->message,
                    'is_read' => $n->is_read,
                    'created_at' => $n->created_at->diffForHumans(),
                    'sender_name' => $n->sender->name ?? 'Unknown',
                    'file_name' => $n->relatedItem->original_name ?? 'Unknown File',
                    'file_id' => $n->related_item_id,
                ];
            })
        ]);
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
            // Check if category is accessible to dean
            $category = Category::where('id', $request->category_id)
                ->where('accessible_roles', 'like', '%dean%')
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
        // Dean can view ANY file (not just their own uploads)
        $file = ArchiveFile::findOrFail($id);
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
            // Dean can view any file
            $file = ArchiveFile::with(['category.fields', 'uploader'])
                ->findOrFail($id);
            $fieldData = json_decode($file->field_data, true) ?? [];

            return view('dean.view_file_details', compact('file', 'fieldData'));

        } catch (\Exception $e) {
            \Log::error('View file details error: ' . $e->getMessage());
            return redirect()->route('dean.archived_files')
                ->with('error', 'Unable to load file details.');
        }
    }

    /**
     * Display archived files page
     */
    public function archivedFiles()
    {
        try {
            // Dean can see ALL archived files
            $archivedFiles = ArchiveFile::with(['category', 'uploader'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('dean.archived_files', compact('archivedFiles'));
        } catch (\Exception $e) {
            \Log::error('Archived files error: ' . $e->getMessage());
            return back()->with('error', 'Error loading archived files: ' . $e->getMessage());
        }
    }

    /**
     * Delete archive file
     */
    public function deleteArchiveFile($id)
    {
        try {
            // Dean can delete any file
            $file = ArchiveFile::findOrFail($id);
            
            // Delete physical file
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            
            // Delete database record
            $file->delete();
            
            return redirect()->route('dean.archived_files')
                ->with('success', 'File deleted successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Delete file error: ' . $e->getMessage());
            return back()->with('error', 'Unable to delete file.');
        }
    }

    /**
     * Restore archive file 
     */
    public function restoreArchiveFile($id)
    {
        try {
            $file = ArchiveFile::findOrFail($id);
            
            // If your ArchiveFile model has an 'is_archived' field, uncomment this:
            // $file->update(['is_archived' => false]);
            
            // Or if you're moving between tables, implement that logic here
            
            return redirect()->route('dean.archived_files')
                ->with('success', 'File restored successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Restore file error: ' . $e->getMessage());
            return back()->with('error', 'Unable to restore file.');
        }
    }


    /* ---------- settings ---------- */
    public function settings()
    {
        $dean = Auth::user();
        return view('dean.settings', compact('dean'));
    }

    // Add the updateProfile method
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'program' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }
            
            $profilePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $profilePath;
        }

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->program = $request->program;
        $user->address = $request->address;
        $user->description = $request->description;

        // Update password if provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}