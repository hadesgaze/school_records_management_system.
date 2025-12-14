<?php

namespace App\Helpers; // <-- Must be App\Helpers

use App\Models\ActivityLog; // <-- It USES the model
use Illuminate\Support\Facades\Auth;

class LogActivity // <-- Must be LogActivity
{
    /**
     * Helper to log an activity.
     */
    public static function add($action, $module = null, $details = null)
    {
        ActivityLog::create([
            'user_id'   => Auth::check() ? Auth::id() : null,
            'action'    => $action,
            'module'    => $module,
            'details'   => $details,
            'ip_address'=> request()->ip(),
        ]);
    }
}