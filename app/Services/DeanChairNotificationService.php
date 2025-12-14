<?php

namespace App\Services;

use App\Models\User;
use App\Models\ArchiveFile;
use App\Models\FacultyNotification;
use Illuminate\Support\Facades\Log;

class DeanChairNotificationService
{
    /**
     * Send notifications to dean and program chair when faculty uploads a file
     */
    public function notifyUpload(ArchiveFile $file)
    {
        try {
            $faculty = $file->uploader;
            
            // 1. Notify Dean (all deans)
            $this->notifyDean($file, $faculty);
            
            // 2. Notify Program Chair (based on faculty's program)
            $this->notifyProgramChair($file, $faculty);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Dean/Chair Notification error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notify all deans
     */
    private function notifyDean(ArchiveFile $file, User $faculty)
    {
        // Find all users with dean role
        $deans = User::whereHas('role', function($q) {
            $q->where('name', 'dean');
        })->get();
        
        foreach ($deans as $dean) {
            FacultyNotification::create([
                'sender_id' => $faculty->id,
                'receiver_role' => 'dean',
                'user_id' => $dean->id, // Specific dean user
                'message' => "Faculty {$faculty->name} uploaded a new file: {$file->original_name}",
                'related_item_id' => $file->id,
                'related_item_type' => ArchiveFile::class,
                'is_read' => false,
            ]);
        }
    }
    
    /**
     * Notify the program chair
     */
    private function notifyProgramChair(ArchiveFile $file, User $faculty)
    {
        // Find program chair for the faculty's program
        $chairs = User::whereHas('role', function($q) {
                $q->where('name', 'program_chair');
            })
            ->where('program', $faculty->program) // Assuming both have 'program' field
            ->get();
        
        foreach ($chairs as $chair) {
            FacultyNotification::create([
                'sender_id' => $faculty->id,
                'receiver_role' => 'program_chair',
                'user_id' => $chair->id, // Specific chair user
                'message' => "Faculty {$faculty->name} from your program ({$faculty->program}) uploaded a new file: {$file->original_name}",
                'related_item_id' => $file->id,
                'related_item_type' => ArchiveFile::class,
                'is_read' => false,
            ]);
        }
    }
    
    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount(User $user)
    {
        return FacultyNotification::query()
            ->where(function ($q) use ($user) {
                // Either general role-based notifications
                $q->where('receiver_role', $user->role->name)
                  // OR specific user notifications
                  ->orWhere('user_id', $user->id);
            })
            ->where('is_read', false)
            ->count();
    }
    
    /**
     * Get notifications for a user
     */
    public function getNotifications(User $user, $limit = 20)
    {
        return FacultyNotification::with(['sender', 'relatedItem'])
            ->where(function ($q) use ($user) {
                // General role-based notifications for this role
                $q->where('receiver_role', $user->role->name)
                  // OR specific user notifications
                  ->orWhere('user_id', $user->id);
            })
            ->when($user->role->name === 'program_chair', function ($q) use ($user) {
                // For program chairs, only show notifications from their program
                // We'll need to join with users table to check faculty's program
                $q->whereHas('sender', function ($query) use ($user) {
                    $query->where('program', $user->program);
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, User $user)
    {
        $notification = FacultyNotification::where('id', $notificationId)
            ->where(function ($q) use ($user) {
                $q->where('receiver_role', $user->role->name)
                  ->orWhere('user_id', $user->id);
            })
            ->first();
            
        if ($notification && !$notification->is_read) {
            $notification->update(['is_read' => true]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(User $user)
    {
        return FacultyNotification::where(function ($q) use ($user) {
                $q->where('receiver_role', $user->role->name)
                  ->orWhere('user_id', $user->id);
            })
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}