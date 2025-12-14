<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $base = ActivityLog::with(['user:id,profile_picture,name,username'])
            ->orderByDesc('created_at');

        // ⛔ If NOT searching -> normal DB pagination but decrypt AFTER fetching
        if (!$request->filled('search')) {
            $logs = $base->paginate($perPage)->withQueryString();

            $logs->getCollection()->transform(function ($log) {
                // 🔓 Decrypt user name if exists
                if ($log->user) {
                    $rawName = $log->user->getRawOriginal('name');
                    $log->user->name = $this->tryAesDecrypt($rawName);
                }

                // 🔓 Decrypt module + action
                $log->action = $this->tryAesDecrypt($log->getRawOriginal('action'));
                $log->module = $this->tryAesDecrypt($log->getRawOriginal('module'));

                return $log;
            });

            return view('admin.audit-logs', compact('logs'));
        }

        // 🔍 SEARCH MODE
        $term = mb_strtolower(trim((string) $request->input('search','')));
        $window = 5000; // Limit for search efficiency

        // 1️⃣ Find users whose decrypted name or username matches search
        $matchUserIds = User::get()->filter(function ($user) use ($term) {
            $name = mb_strtolower($this->tryAesDecrypt($user->getRawOriginal('name')));
            $username = mb_strtolower($user->username ?? '');
            return Str::contains($name, $term) ||
                   Str::contains($username, $term);
        })->pluck('id');

        // 2️⃣ Decrypt logs in-memory for smart filtering
        $candidates = $base->take($window)->get();

        $filtered = $candidates->filter(function ($log) use ($term, $matchUserIds) {

            // 🔓 Decrypt + update object for display
            if ($log->user) {
                $rawName = $log->user->getRawOriginal('name');
                $log->user->name = $this->tryAesDecrypt($rawName);
            }

            $action = mb_strtolower($this->tryAesDecrypt($log->getRawOriginal('action') ?? ''));
            $module = mb_strtolower($this->tryAesDecrypt($log->getRawOriginal('module') ?? ''));
            $ip = mb_strtolower($log->ip_address ?? '');

            $log->action = $action;
            $log->module = $module;

            $matchAction = Str::contains($action, $term);
            $matchModule = Str::contains($module, $term);
            $matchIp = Str::contains($ip, $term);
            $matchUser = $matchUserIds->contains($log->user_id);

            return $matchAction || $matchModule || $matchIp || $matchUser;
        })->values();

        // 3️⃣ Manual pagination for filtered results
        $page = Paginator::resolveCurrentPage() ?: 1;
        $items = $filtered->forPage($page, $perPage)->values();

        $logs = new LengthAwarePaginator(
            $items,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.audit-logs', compact('logs'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        if ($log->user) {
            $rawName = $log->user->getRawOriginal('name');
            $log->user->name = $this->tryAesDecrypt($rawName);
        }

        $log->action = $this->tryAesDecrypt($log->getRawOriginal('action'));
        $log->module = $this->tryAesDecrypt($log->getRawOriginal('module'));

        return view('admin.audit-log-show', compact('log'));
    }

    /**
     * Safely decrypts AES-encrypted values.
     * Returns original if value is not encrypted.
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
     * Delete an audit log entry
     */
    public function destroy($id)
    {
        try {
            $log = ActivityLog::findOrFail($id);
            $log->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Audit log deleted successfully.'
                ]);
            }
            
            return redirect()->route('admin.audit-logs')
                ->with('success', 'Audit log deleted successfully.');
                
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete audit log.'
                ], 500);
            }
            
            return redirect()->route('admin.audit-logs')
                ->with('error', 'Failed to delete audit log.');
        }
    }
}