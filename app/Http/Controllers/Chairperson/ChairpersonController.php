<?php

namespace App\Http\Controllers\Chairperson;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use Carbon\Carbon; 
use ZipArchive;
use App\Providers\ViewServiceProvider;
use App\Models\User;
use App\Services\DeanChairNotificationService;
use App\Models\FacultyNotification;
use App\Models\Notification;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\ArchiveFile;
use App\Models\Document;
use App\Helpers\LogActivity;
use App\Models\ActivityLog;
use App\Services\ZstdCompressionService; 



class ChairpersonController extends Controller
{
    /* ---------- helpers ---------- */


    protected $zstdService;
    protected $notificationService;
    
    public function __construct()
    {
        $this->zstdService = new ZstdCompressionService();
        $this->notificationService = new DeanChairNotificationService();
    }
    
    private function raw($model, string $attr)
    {
        return method_exists($model, 'getRawOriginal')
            ? $model->getRawOriginal($attr)
            : data_get($model, $attr); 
    }

    private function tryDecrypt($value)
    {
        if ($value === null || $value === '' || !is_string($value)) {
            return $value;
        }
        
        // More lenient base64 check - handle BSIS and other program names
        if (strlen($value) % 4 !== 0 || !preg_match('/^[a-zA-Z0-9\/+=]+$/', $value)) {
            // If it doesn't look like base64 encrypted data, return as-is
            // This handles cases where program names might not be encrypted
            return $value;
        }
        
        try {
            $decrypted = Crypt::decryptString($value);
            // Special handling for BSIS - check if decryption resulted in garbage
            if (strlen($decrypted) > 100 || preg_match('/[^\x20-\x7E]/', $decrypted)) {
                 \Log::warning('Suspicious decryption result for value: ' . substr($value, 0, 50));
                 return $value; // Return original if decryption looks wrong
            }
            return $decrypted;
        } catch (DecryptException $e) {
            \Log::warning('Decryption failed for value: ' . substr($value, 0, 50) . ' - ' . $e->getMessage());
            return $value; // Return original value if decryption fails
        } catch (\Throwable $e) {
            \Log::warning('Decryption error: ' . $e->getMessage());
            return $value;
        }
    }

    private function avatarUrl($nameRaw, $profilePicture, int $size = 128, string $bg = 'ff7f50')
    {
        if ($profilePicture && Storage::disk('public')->exists($profilePicture)) {
            return asset('storage/'.$profilePicture);
        }
        
        $name = $this->tryDecrypt($nameRaw);
        if (!$name || !is_string($name)) {
            $name = 'User';
        }
        
        return 'https://ui-avatars.com/api/?name='.urlencode($name)."&background={$bg}&color=fff&size={$size}";
    }

   
     
    /* ---------- dashboard ---------- */

/**
 * Display chairperson dashboard with statistics
 */
public function dashboard()
{
    $user = Auth::user();
    
    // Get total faculty members in the same program
    $totalFaculty = User::where('role', 'faculty')
        ->where('program', $user->program)
        ->count();
    
    // Get files uploaded by the chairperson
    $yourFiles = ArchiveFile::where('uploaded_by', $user->id)->count();
    
    // Get all faculty members in the same program (for program files count)
    $facultyInSameProgram = User::where('role', 'faculty')
        ->where('program', $user->program)
        ->pluck('id')
        ->toArray();
    
    // Include the chairperson's own ID
    $allowedUserIds = array_merge([$user->id], $facultyInSameProgram);
    
    // Get total files in the program (chairperson + faculty)
    $programFiles = ArchiveFile::whereIn('uploaded_by', $allowedUserIds)->count();
    
    // Get unread notifications count
    $unreadCount = Notification::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('receiver_role', $user->role);
        })
        ->where('is_read', false)
        ->count();
    
    // Get recent notifications
    $notifications = Notification::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('receiver_role', $user->role);
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    return view('chairperson.dashboard', compact(
        'totalFaculty',
        'yourFiles',
        'programFiles',
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
        ->where('accessible_roles', 'like', '%chairperson%')
        ->orderBy('name')
        ->get();

        return view('chairperson.upload_files', compact('categories'));
    }

    /**
     * Display all notifications for program chair
     */
    public function notifications()
{
    $user = Auth::user();
    
    // Get users for sending notifications (only faculty in same program for chairperson)
    $users = User::where('role', 'faculty')
        ->where('program', $user->program)
        ->where('id', '!=', $user->id)
        ->get();
    
    // Get notifications using the Notification model
    $notifications = Notification::with(['sender'])
        ->where(function ($q) use ($user) {
            $q->where('receiver_role', 'chairperson')
              ->orWhere('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(20);
    
    // Count unread notifications using the same logic
    $unreadCount = Notification::where(function ($q) use ($user) {
            $q->where('receiver_role', 'chairperson')
              ->orWhere('user_id', $user->id);
        })
        ->where('is_read', false)
        ->count();
    
    return view('chairperson.notifications', [
        'notifications' => $notifications,
        'unreadCount' => $unreadCount,
        'users' => $users
    ]);
}
    
    /**
     * Mark notification as read - FIXED: This should use Notification model
     */
    public function markNotificationRead($id)
    {
        $user = Auth::user();
        
        // Using Notification model instead of service
        $notification = Notification::where('id', $id)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('receiver_role', 'chairperson');
            })
            ->firstOrFail();
            
        $notification->update(['is_read' => true]);
        
        return back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read - FIXED
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        
        Notification::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('receiver_role', 'chairperson');
            })
            ->update(['is_read' => true]);
        
        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Store notification
     */
    public function storeNotification(Request $request)
    {
        $request->validate([
            'send_type'     => 'required|string',
            'message'       => 'required|string|max:1000',
            'receiver_role' => 'nullable|string',
            'user_id'       => 'nullable|integer',
        ]);

        $sender = Auth::user(); // This is the logged-in Chairperson

        // 👤 Send to individual user
        if ($request->send_type === 'user' && $request->user_id) {
            $user = User::findOrFail($request->user_id);

            Notification::create([
                'sender_id'     => $sender->id,
                'sender_role'   => $sender->role, // Will be 'chairperson'
                'receiver_role' => $user->role,
                'user_id'       => $user->id,
                'message'       => $request->message,
                'is_read'       => false,
            ]);
        } 
        // 🧩 Send to role or all
        elseif ($request->send_type === 'role' && $request->receiver_role) {
            if ($request->receiver_role === 'all') {
                // 'all' for a Chairperson means all Faculty in their program
                $recipients = User::where('role', 'faculty')
                    ->where('program', $sender->program)
                    ->get();
            } else {
                // Send to a specific role
                $recipients = User::where('role', $request->receiver_role)
                    ->where('program', $sender->program)
                    ->get();
            }

            foreach ($recipients as $user) {
                Notification::create([
                    'sender_id'     => $sender->id,
                    'sender_role'   => $sender->role, // Will be 'chairperson'
                    'receiver_role' => $user->role,
                    'user_id'       => $user->id,
                    'message'       => $request->message,
                    'is_read'       => false,
                ]);
            }
        }

        return back()->with('success', 'Notification sent successfully!');
    }

    /**
     * Fetch Latest Notifications (for AJAX polling)
     */
    public function fetchNotification()
    {
        $chairperson = Auth::user();

        // Base query for notifications belonging to this Chairperson
        $baseQuery = Notification::where(function($query) use ($chairperson) {
            $query->where('user_id', $chairperson->id)
                  ->orWhere('receiver_role', 'chairperson');
        });

        // Clone the query to use it for two different counts
        $notificationsQuery = clone $baseQuery;
        $unreadCountQuery = clone $baseQuery;

        $notifications = $notificationsQuery->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'message', 'is_read', 'created_at']);

        $unreadCount = $unreadCountQuery->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
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
            // Check if category is accessible to chairperson
            $category = Category::where('id', $request->category_id)
                ->where('accessible_roles', 'like', '%chairperson%')
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
            $chairperson = Auth::user();
            
            // Chairperson can only view files from their program
            $file = ArchiveFile::with(['category.fields', 'uploader'])
                ->findOrFail($id);
                
            // Check if the uploader is in the same program
            if ($file->uploader->program !== $chairperson->program && $file->uploaded_by !== $chairperson->id) {
                return redirect()->route('chairperson.archive_files')
                    ->with('error', 'You are not authorized to view this file.');
            }
                
            $fieldData = json_decode($file->field_data, true) ?? [];

            return view('chairperson.view_file_details', compact('file', 'fieldData'));

        } catch (\Exception $e) {
            \Log::error('View file details error: ' . $e->getMessage());
            return redirect()->route('chairperson.archive_files')
                ->with('error', 'Unable to load file details.');
        }
    }

    /**
     * Display archived files page - Only show chairperson's files and files from faculty in their program
     */
    public function archivedFiles()
    {
        try {
            $chairperson = Auth::user();
            
            // Get all faculty members in the same program as the chairperson
            $facultyInSameProgram = User::where('role', 'faculty')
                ->where('program', $chairperson->program)
                ->pluck('id')
                ->toArray();
            
            // Include the chairperson's own ID
            $allowedUserIds = array_merge([$chairperson->id], $facultyInSameProgram);
            
            // Get archived files only from allowed users
            $archivedFiles = ArchiveFile::with(['category', 'uploader'])
                ->whereIn('uploaded_by', $allowedUserIds)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('chairperson.archived_files', compact('archivedFiles'));
        } catch (\Exception $e) {
            \Log::error('Archived files error: ' . $e->getMessage());
            return back()->with('error', 'Error loading archived files: ' . $e->getMessage());
        }
    }


    /**
     * Delete archive file with authorization check
     */
    public function deleteArchiveFile($id)
    {
        try {
            $chairperson = Auth::user();
            
            // Get the file
            $file = ArchiveFile::with('uploader')->findOrFail($id);
            
            // Check if the chairperson is authorized to delete this file
            $facultyInSameProgram = User::where('role', 'faculty')
                ->where('program', $chairperson->program)
                ->pluck('id')
                ->toArray();
            
            $allowedUserIds = array_merge([$chairperson->id], $facultyInSameProgram);
            
            if (!in_array($file->uploaded_by, $allowedUserIds)) {
                return back()->with('error', 'You are not authorized to delete this file.');
            }
            
            // Delete physical file
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            
            // Delete database record
            $file->delete();
            
            return redirect()->route('chairperson.archive_files')
                ->with('success', 'File deleted successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Delete file error: ' . $e->getMessage());
            return back()->with('error', 'Unable to delete file.');
        }
    }

    /**
     * Restore archive file (if you have an 'is_archived' field)
     */
    public function restoreArchiveFile($id)
    {
        try {
            $chairperson = Auth::user();
            
            // Get the file
            $file = ArchiveFile::with('uploader')->findOrFail($id);
            
            // Check if the chairperson is authorized to restore this file
            $facultyInSameProgram = User::where('role', 'faculty')
                ->where('program', $chairperson->program)
                ->pluck('id')
                ->toArray();
            
            $allowedUserIds = array_merge([$chairperson->id], $facultyInSameProgram);
            
            if (!in_array($file->uploaded_by, $allowedUserIds)) {
                return back()->with('error', 'You are not authorized to restore this file.');
            }
            
            // If your ArchiveFile model has an 'is_archived' field, uncomment this:
            // $file->update(['is_archived' => false]);
            
            return redirect()->route('chairperson.archive_files')
                ->with('success', 'File restored successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Restore file error: ' . $e->getMessage());
            return back()->with('error', 'Unable to restore file.');
        }
    }

   
    /* ---------- settings ---------- */
    public function settings()
    {
        $chairperson = Auth::user();
        return view('chairperson.settings', compact('chairperson'));
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