<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
// use App\Models\User; // Replace with your model if named differently (e.g., Account)

class AdminNotificationController extends Controller
{
    /* =========================================================
       📬 Show Notifications (Admin Side)
    ========================================================= */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orWhere('receiver_role', 'all')
            ->orderBy('created_at', 'desc')
            ->get();

        // Load all possible recipients (Dean, Chairperson, Faculty)
        $users = \App\Models\User::whereIn('role', ['dean', 'chairperson', 'faculty'])
            ->orderBy('name')
            ->get();

        return view('admin.notifications', compact('notifications', 'users'));
    }

    /* =========================================================
       ✉️ Send Notification
    ========================================================= */
    public function store(Request $request)
    {
        $request->validate([
            'send_type'     => 'required|string',
            'message'       => 'required|string|max:1000',
            'receiver_role' => 'nullable|string',
            'user_id'       => 'nullable|integer',
        ]);

        $sender = Auth::user();

        // 👤 Send to individual user
        if ($request->send_type === 'user' && $request->user_id) {
            $user = \App\Models\User::findOrFail($request->user_id);

            Notification::create([
                'sender_id'     => $sender->id,
                'sender_role'   => $sender->role ?? 'admin',
                'receiver_role' => $user->role,
                'user_id'       => $user->id,
                'message'       => $request->message,
                'is_read'       => false,
            ]);
        } 
        // 🧩 Send to role or all
        elseif ($request->send_type === 'role' && $request->receiver_role) {
            if ($request->receiver_role === 'all') {
                $recipients = \App\Models\User::whereIn('role', ['dean', 'chairperson', 'faculty'])->get();
            } else {
                $recipients = \App\Models\User::where('role', $request->receiver_role)->get();
            }

            foreach ($recipients as $user) {
                Notification::create([
                    'sender_id'     => $sender->id,
                    'sender_role'   => $sender->role ?? 'admin',
                    'receiver_role' => $user->role,
                    'user_id'       => $user->id,
                    'message'       => $request->message,
                    'is_read'       => false,
                ]);
            }
        }

        return back()->with('success', 'Notification sent successfully!');
    }

    /* =========================================================
       ✅ Mark Notification as Read
    ========================================================= */
    public function markRead($id)
    {
        $notif = Notification::findOrFail($id);
        $notif->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }
//  /* =========================================================
      // 📲 Fetch Latest Notifications (for AJAX polling)
    public function fetch()
    {
        $notifications = Notification::where('receiver_role', 'admin')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'message', 'is_read', 'created_at']);

        $unreadCount = Notification::where('receiver_role', 'admin')
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
