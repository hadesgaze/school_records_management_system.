<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Share notifications with dean layout
        View::composer('layouts.dean', function ($view) {
            if (Auth::check() && Auth::user()->role === 'dean') {
                $user = Auth::user();
                $unreadCount = Notification::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere('receiver_role', 'dean');
                })
                ->where('is_read', false)
                ->count();
                
                $notifications = Notification::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere('receiver_role', 'dean');
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
                
                $view->with(compact('unreadCount', 'notifications'));
            }
        });

        // Same for chairperson layout
        View::composer('layouts.chairperson', function ($view) {
            if (Auth::check() && Auth::user()->role === 'chairperson') {
                // Same logic as above but for chairperson
            }
        });
    }
}