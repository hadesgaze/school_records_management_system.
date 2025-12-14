<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Add this import
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    /**
     * Show login form for a specific role
     */
    public function showRoleLogin($role)
    {
        return view('auth.role-login', compact('role'));
    }

    /**
     * Handle login attempt for a specific role
     */
    public function roleLogin(Request $request, $role)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by manually checking encrypted usernames
        $user = $this->findUserByEncryptedUsername($request->username, $role);

        if ($user && Auth::attempt(['id' => $user->id, 'password' => $request->password], $request->filled('remember'))) {
            // Ensure account is active (if you use status field)
            if (isset($user->status) && $user->status !== 'ACTIVE') {
                Auth::logout();
                return back()->withErrors(['account' => 'Your account is inactive. Please contact the administrator.']);
            }

            // Redirect user based on their role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'dean':
                    return redirect()->route('dean.dashboard');
                case 'chairperson':
                    return redirect()->route('chair.dashboard');
                case 'faculty':
                    return redirect()->route('faculty.dashboard');
                default:
                    Auth::logout();
                    return back()->withErrors(['role' => 'Unauthorized role access.']);
            }
        }

        // Invalid login attempt
        return back()->withErrors(['username' => 'Invalid username or password.']);
    }

    /**
     * Find user by manually decrypting usernames
     */
    private function findUserByEncryptedUsername($username, $role)
    {
        // Get all users with the specified role
        $users = User::where('role', $role)->get();
        
        foreach ($users as $user) {
            try {
                // Get the raw encrypted username from database
                $encryptedUsername = $user->getRawOriginal('username');
                
                // Try to decrypt and compare
                $decryptedUsername = Crypt::decryptString($encryptedUsername);
                
                if ($decryptedUsername === $username) {
                    return $user;
                }
            } catch (\Exception $e) {
                // If decryption fails, try comparing as plain text (for backward compatibility)
                if ($encryptedUsername === $username) {
                    return $user;
                }
            }
        }
        
        return null;
    }

    /**
     * Handle logout for any role
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }

    /**
     * Tell Laravel to use the username field for login
     */
    public function username()
    {
        return 'username';
    }
}