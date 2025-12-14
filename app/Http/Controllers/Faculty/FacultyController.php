<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\FacultyNotification;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\ArchiveFile;
use App\Services\DeanChairNotificationService;
use Illuminate\Support\Str;
use App\Models\ActivityLog;
use App\Services\ZstdCompressionService; 

class FacultyController extends Controller
{

    protected $zstdService;
    
    public function __construct()
    {
        $this->zstdService = new ZstdCompressionService();
    }

    /**
     * Display dashboard
     */
  public function dashboard()
{
    $user = Auth::user();

    $totalFiles = ArchiveFile::where('uploaded_by', $user->id)->count();

    $totalCategories = Category::where('accessible_roles', 'like', '%faculty%')->count();

    $recentActivities = ActivityLog::where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get();

    $recentFiles = ArchiveFile::where('uploaded_by', $user->id)
        ->with('category')
        ->latest()
        ->take(5)
        ->get();

    return view('faculty.dashboard', compact(
        'totalFiles', 
        'totalCategories', 
        'recentActivities',
        'recentFiles'
    ));
}

    /**
     * Display upload files + category management page
     */
    public function uploadFiles()
    {
        $categories = Category::with(['fields' => function($query) {
            $query->orderBy('order', 'asc');
        }])
        ->where('accessible_roles', 'like', '%faculty%')
        ->orderBy('name')
        ->get();

        return view('faculty.upload_files', compact('categories'));
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
            // Check if category is accessible to faculty
            $category = Category::where('id', $request->category_id)
                ->where('accessible_roles', 'like', '%faculty%')
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
     * View file details
     */
    public function viewFileDetails($id)
    {
        try {
            $file = ArchiveFile::with(['category.fields', 'uploader'])
                ->where('uploaded_by', Auth::id())
                ->findOrFail($id);
            $fieldData = json_decode($file->field_data, true) ?? [];

            return view('faculty.view_file_details', compact('file', 'fieldData'));

        } catch (\Exception $e) {
            \Log::error('View file details error: ' . $e->getMessage());
            return redirect()->route('faculty.archive_files')
                ->with('error', 'Unable to load file details.');
        }
    }

   // In your Faculty Controller
public function archivedFiles()
{
    // Get the logged-in faculty user
    $faculty = auth()->user();
    
    // Get archived files uploaded by this faculty
    $archivedFiles = ArchiveFile::where('uploaded_by', $faculty->id)
        ->with(['category', 'uploader'])
        ->latest()
        ->paginate(10);
    
    return view('faculty.archived_files', compact('archivedFiles'));
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
     * Delete archive file
     */
    public function deleteArchiveFile($id)
    {
        try {
            $file = ArchiveFile::where('uploaded_by', Auth::id())->findOrFail($id);
            
            // Delete physical file
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            
            // Delete database record
            $file->delete();
            
            return redirect()->route('faculty.archive_files')
                ->with('success', 'File deleted successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Delete file error: ' . $e->getMessage());
            return back()->with('error', 'Unable to delete file.');
        }
    }

    /**
     * Display settings page
     */
    public function settings()
    {
        return view('faculty.settings');
    }

    /**
     * Display notifications page
     */
    public function notifications()
    {
        $uid = Auth::id();

        $notifications = FacultyNotification::query()
            ->where(function ($q) use ($uid) {
                $q->where('receiver_role', 'faculty')
                  ->orWhere('user_id', $uid);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('faculty.notifications', compact('notifications'));
    }

    /**
     * Fetch notifications for AJAX requests
     */
    public function fetchNotifications()
    {
        $uid = Auth::id();

        $unreadCount = FacultyNotification::query()
            ->where(function ($q) use ($uid) {
                $q->where('receiver_role', 'faculty')
                  ->orWhere('user_id', $uid);
            })
            ->where('is_read', false)
            ->count();

        $list = FacultyNotification::query()
            ->where(function ($q) use ($uid) {
                $q->where('receiver_role', 'faculty')
                  ->orWhere('user_id', $uid);
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['id','message','is_read','created_at']);

        return response()->json([
            'unreadCount'   => $unreadCount,
            'notifications' => $list->map(fn ($n) => [
                'id'         => $n->id,
                'message'    => (string) $n->message,
                'is_read'    => (bool) $n->is_read,
                'created_at' => $n->created_at,
            ]),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $uid = Auth::id();

        $n = FacultyNotification::where('id', $id)
            ->where(function ($q) use ($uid) {
                $q->where('receiver_role', 'faculty')
                  ->orWhere('user_id', $uid);
            })
            ->firstOrFail();

        if (!$n->is_read) {
            $n->is_read = true;
            $n->save();
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark notification as unread
     */
    public function markNotificationUnread($id)
    {
        $uid = Auth::id();

        $n = FacultyNotification::where('id', $id)
            ->where(function ($q) use ($uid) {
                $q->where('receiver_role', 'faculty')
                  ->orWhere('user_id', $uid);
            })
            ->firstOrFail();

        if ($n->is_read) {
            $n->is_read = false;
            $n->save();
        }

        return back()->with('success', 'Notification marked as unread.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $uid = Auth::id();

        FacultyNotification::query()
            ->where(function ($q) use ($uid) {
                $q->where('receiver_role', 'faculty')
                  ->orWhere('user_id', $uid);
            })
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete notification
     */
    public function deleteNotification($id)
    {
        $uid = Auth::id();

        $n = FacultyNotification::where('id', $id)
            ->where(function ($q) use ($uid) {
                $q->where('receiver_role', 'faculty')
                  ->orWhere('user_id', $uid);
            })
            ->firstOrFail();

        $n->delete();

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Update faculty profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'program' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'nullable|confirmed|min:8',
            'current_password' => 'required_with:password',
        ]);

        try {
            // Handle password change
            if ($request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->with('error', 'Current password is incorrect.');
                }
                $user->password = Hash::make($request->password);
            }

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old photo if exists
                if ($user->profile_picture && Storage::exists('public/' . $user->profile_picture)) {
                    Storage::delete('public/' . $user->profile_picture);
                }

                $path = $request->file('profile_picture')->store('profiles', 'public');
                $user->profile_picture = $path;
            }

            // Update other fields
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->program = $request->program;
            $user->address = $request->address;
            $user->description = $request->description;

            $user->save();

            return back()->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }
}