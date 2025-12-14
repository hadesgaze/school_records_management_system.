<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Archive;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\ArchiveFile;
use Illuminate\Support\Facades\Crypt;

class UserManagementController extends Controller
{
    /* ============================================================
       🧭 USER LIST
    ============================================================ */
  public function index(Request $request)
{
    $search = $request->search;
    
    // Start with base query
    $usersQuery = User::query();
    
    // Apply search if provided
    if ($search) {
        $usersQuery->where(function($q) use ($search) {
            $q->where('program', 'LIKE', "%{$search}%")
              ->orWhere('role', 'LIKE', "%{$search}%")
              ->orWhere('status', 'LIKE', "%{$search}%");
        });
        
        // Also search in encrypted fields if needed
        $encryptedSearch = User::getAllWithDecrypted()
            ->filter(function ($user) use ($search) {
                return stripos($user->name_decrypted, $search) !== false || 
                       stripos($user->username_decrypted, $search) !== false ||
                       stripos($user->email_decrypted, $search) !== false;
            })
            ->pluck('id')
            ->toArray();
        
        if (!empty($encryptedSearch)) {
            $usersQuery->orWhereIn('id', $encryptedSearch);
        }
    }
    
    // Apply role filter
    if ($request->role) {
        $usersQuery->where('role', $request->role);
    }
    
    // Apply program filter
    if ($request->program) {
        $usersQuery->where('program', $request->program);
    }
    
    // Apply sorting
    $sort = $request->sort ?? 'created_at';
    $direction = $request->direction ?? 'desc';
    
    $validSortColumns = ['name', 'username', 'email', 'program', 'role', 'status', 'created_at'];
    $sortColumn = in_array($sort, $validSortColumns) ? $sort : 'created_at';
    
    $usersQuery->orderBy($sortColumn, $direction);
    
    // Get paginated results
    $users = $usersQuery->paginate(10);
    
    //  Decrypted attributes for display
    $users->getCollection()->transform(function ($user) {
        $user->name_decrypted = $user->decrypted_name;
        $user->username_decrypted = $user->decrypted_username;
        $user->email_decrypted = $user->decrypted_email;
        $user->program_decrypted = $user->decrypted_program;
        return $user;
    });

    return view('admin.users', compact('users'));
}
    /* ============================================================
       ➕ CREATE USER FORM
    ============================================================ */
    public function create()
    {
        $roles = ['admin', 'dean', 'chairperson', 'faculty'];
        $statuses = ['ACTIVE', 'INACTIVE', 'SUSPENDED'];
        $programs = ['BSCS', 'BSIS', 'BINDTECH'];
        return view('admin.users-create', compact('roles', 'statuses', 'programs'));
    }

    /* ============================================================
       💾 STORE NEW USER
    ============================================================ */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'nullable|email',
            'role' => ['required', Rule::in(['admin','dean','chairperson','faculty'])],
            'status' => ['required', Rule::in(['ACTIVE','INACTIVE','SUSPENDED'])],
            'password' => 'required|min:6|confirmed',
            'program' => 'nullable|string|max:255|in:BSCS,BSIS,BINDTECH',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for uniqueness using decrypted values
        $allUsers = User::getAllWithDecrypted();
        
        $existingUsername = $allUsers->first(function ($user) use ($request) {
            return $user->username_decrypted === $request->username;
        });
        
        if ($existingUsername) {
            return redirect()->back()
                ->withErrors(['username' => 'The username already exists.'])
                ->withInput();
        }
        
        if ($request->email) {
            $existingEmail = $allUsers->first(function ($user) use ($request) {
                return $user->email_decrypted === $request->email;
            });
            
            if ($existingEmail) {
                return redirect()->back()
                    ->withErrors(['email' => 'The email already exists.'])
                    ->withInput();
            }
        }

        try {
            
            // Create user - encryption happens automatically.
            $user = User::create([
                'name' => $request->name, // Will be encrypted.
                'username' => $request->username, // Will be encrypted.
                'email' => $request->email, // Will be encrypted.
                'role' => $request->role, //Plain text
                'status' => $request->status, //Plain text
                'password' => Hash::make($request->password),
                'program' => $request->program, // Plain text
            ]);

            $user->syncRoles([$request->role]);

            return redirect()->route('admin.users')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /* ============================================================
       ✏️ EDIT USER FORM
    ============================================================ */
    public function edit(User $user)
    {
        // Get decrypted values
        $user->name_decrypted = $user->decrypted_name;
        $user->username_decrypted = $user->decrypted_username;
        $user->email_decrypted = $user->decrypted_email;
        $user->program_decrypted = $user->program; // Plain text

        $roles = ['admin', 'dean', 'chairperson', 'faculty'];
        $statuses = ['ACTIVE', 'INACTIVE', 'SUSPENDED'];
        $programs = ['BSCS', 'BSIS', 'BINDTECH'];

        return view('admin.users-edit', compact('user', 'roles', 'statuses', 'programs'));
    }

    /* ============================================================
       🧩 UPDATE USER
    ============================================================ */
    public function update(Request $request, User $user)
    {
        // Validate with unique rule ignoring current user
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => ['required','string','max:255'],
            'email' => ['nullable','email'],
            'role' => ['required', Rule::in(['admin','dean','chairperson','faculty'])],
            'status' => ['required', Rule::in(['ACTIVE','INACTIVE','SUSPENDED'])],
            'password' => 'nullable|min:6|confirmed',
            'program' => 'nullable|string|max:255|in:BSCS,BSIS,BINDTECH',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Manual uniqueness check for encrypted fields
        $allUsers = User::where('id', '!=', $user->id)->get()
            ->map(function ($user) {
                $user->username_decrypted = $user->decrypted_username;
                $user->email_decrypted = $user->decrypted_email;
                return $user;
            });
        
        // Check username uniqueness
        $existingUsername = $allUsers->first(function ($existingUser) use ($request) {
            return $existingUser->username_decrypted === $request->username;
        });
        
        if ($existingUsername) {
            return redirect()->back()
                ->withErrors(['username' => 'The username already exists.'])
                ->withInput();
        }
        
        // Check email uniqueness if provided
        if ($request->email) {
            $existingEmail = $allUsers->first(function ($existingUser) use ($request) {
                return $existingUser->email && $existingUser->email_decrypted === $request->email;
            });
            
            if ($existingEmail) {
                return redirect()->back()
                    ->withErrors(['email' => 'The email already exists.'])
                    ->withInput();
            }
        }

        try {
            // Update user - encryption happens automatically via mutators
            $user->update([
                'name' => $request->name, // Will be encrypted by mutator
                'username' => $request->username, // Will be encrypted by mutator
                'email' => $request->email, // Will be encrypted by mutator
                'role' => $request->role,
                'status' => $request->status,
                'program' => $request->program, // Plain text
                'address' => $request->address, // Plain text
                'description' => $request->description, // Plain text
            ]);

             // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture) {
                Storage::delete($user->profile_picture);
            }

              // Store new picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        } elseif ($request->remove_profile_picture == '1') {
            // Remove profile picture
            if ($user->profile_picture) {
                Storage::delete($user->profile_picture);
            }
            $data['profile_picture'] = null;
        }

         // Handle email verification
        if ($request->email_verified_at == '1' && !$user->email_verified_at) {
            $data['email_verified_at'] = now();
        }

        // Update user
        $user->update($data);

            // Update password if provided
            if (!empty($request->password)) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $user->syncRoles([$request->role]);

            return redirect()->route('admin.users')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /* ============================================================
       🔍 VIEW USER & ARCHIVED DOCUMENTS
    ============================================================ */
    public function show(User $user, Request $request)
    {
        $search = $request->get('doc_search');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $category = $request->get('category', 'all');

        // Get decrypted user information for display
        $user->name_decrypted = $user->decrypted_name;
        $user->username_decrypted = $user->decrypted_username;
        $user->email_decrypted = $user->decrypted_email;
        $user->program_decrypted = $user->program; // Plain text

        // Get user's archived documents
        $archivesQuery = ArchiveFile::where('uploaded_by', $user->id);

        // Filter by category
        if ($category !== 'all') {
            $archivesQuery->where('category_id', $category);
        }

        // Apply search
        if ($search) {
            $archivesQuery->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('field_data', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $validSortColumns = ['original_name', 'file_size', 'file_type', 'created_at'];
        $sortColumn = in_array($sort, $validSortColumns) ? $sort : 'created_at';
        $archivesQuery->orderBy($sortColumn, $direction);

        $documents = $archivesQuery->paginate(10)->withQueryString();

        // Get unique categories for filter dropdown
        $categories = ArchiveFile::where('uploaded_by', $user->id)
            ->distinct('category_id')
            ->pluck('category_id');

        // Get activity logs
        $logs = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('admin.users-show', compact(
            'user', 'documents', 'logs', 'sort', 'direction', 
            'search', 'category', 'categories'
        ));
    }

    /* ============================================================
       🗑️ DELETE USER
    ============================================================ */
    public function destroy(User $user)
    {
        try {
            // Check if user has any archives before deleting
            $archiveCount = ArchiveFile::where('uploaded_by', $user->id)->count();
            
            if ($archiveCount > 0) {
                return redirect()->route('admin.users')
                    ->with('error', 'Cannot delete user. User has ' . $archiveCount . ' archived documents.');
            }

            $user->delete();
            
            return redirect()->route('admin.users')
                ->with('success', 'User deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->route('admin.users')
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}