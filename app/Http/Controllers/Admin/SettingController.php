<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    /* ============================================================
       ⚙️ SYSTEM SETTINGS VIEW
    ============================================================ */
    public function index()
    {
        // Fetch all settings (name => value)
        $settings = Setting::pluck('value', 'name')->toArray();
        return view('admin.settings', compact('settings'));
    }

    /* ============================================================
       💾 UPDATE SYSTEM CONFIGURATION (Title, Department, Logo)
    ============================================================ */
    public function update(Request $request)
    {
        $request->validate([
            'system_title' => 'required|string|max:255',
            'department_name' => 'required|string|max:255',
            'system_logo' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);

        $settings = [
            'system_title' => $request->system_title,
            'department_name' => $request->department_name,
        ];

        // 🖼️ Handle Logo Upload
        if ($request->hasFile('system_logo')) {
            $path = $request->file('system_logo')->store('logos', 'public');

            // Optional: Delete old logo if exists
            $oldLogo = Setting::where('name', 'system_logo')->value('value');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $settings['system_logo'] = $path;
        }

        // ✅ Save each setting
        foreach ($settings as $name => $value) {
            Setting::updateOrCreate(
                ['name' => $name],
                ['value' => $value]
            );
        }

        return back()->with('success', 'System settings updated successfully!');
    }

    /* ============================================================
       👤 UPDATE ADMIN PROFILE
    ============================================================ */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
            'current_password' => 'required_with:password',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // ✅ Password change only if new one provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }
            $user->password = Hash::make($request->password);
        }

        // 🖼️ Upload / Replace Profile Picture
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Save new file
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        // ✅ Update name & email
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    
}
