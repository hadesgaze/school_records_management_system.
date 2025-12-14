@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 mb-1 text-gradient text-primary">Edit User Profile</h1>
            <p class="text-muted">Update user information, permissions, and account settings</p>
        </div>
        <div>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-eye me-2"></i>View Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - User Preview (Fixed) -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 20px;">
                <!-- User Profile Card -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-gradient-primary text-white py-4">
                        <div class="d-flex align-items-center">
                            <!-- Profile Picture -->
                            <div class="position-relative me-3">
                                <div class="avatar avatar-xl bg-white rounded-circle overflow-hidden border-3 border-white" 
                                     id="profilePreviewContainer">
                                    @if($user->profile_picture)
                                        <img src="{{ Storage::url($user->profile_picture) }}" 
                                             alt="Profile" 
                                             class="w-100 h-100 object-fit-cover"
                                             id="profilePreview">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                            <span class="text-primary fs-2 fw-bold" id="avatarInitial">
                                                {{ strtoupper(substr($user->name_decrypted ?? $user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="position-absolute bottom-0 end-0">
                                    <label for="profile_picture" class="btn btn-sm btn-light rounded-circle p-2 cursor-pointer" 
                                           title="Change Photo">
                                        <i class="fas fa-camera text-primary"></i>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-0" id="previewName">{{ $user->name_decrypted ?? $user->name }}</h4>
                                <p class="mb-0 opacity-75" id="previewUsername">{{ $user->username_decrypted ?? $user->username }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- User Stats -->
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="stat-card p-3 bg-light-primary rounded text-center">
                                    <h5 class="mb-1 text-primary">{{ $documents_count ?? 0 }}</h5>
                                    <small class="text-muted">Documents</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card p-3 bg-light-info rounded text-center">
                                    <h5 class="mb-1 text-info">{{ $activities_count ?? 0 }}</h5>
                                    <small class="text-muted">Activities</small>
                                </div>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Account Status</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-{{ [
                                    'admin' => 'danger',
                                    'dean' => 'info',
                                    'chairperson' => 'warning',
                                    'faculty' => 'success'
                                ][$user->role] ?? 'secondary' }} px-3 py-2 rounded-pill" id="previewRole">
                                    <i class="fas fa-{{ [
                                        'admin' => 'user-shield',
                                        'dean' => 'user-graduate',
                                        'chairperson' => 'user-tie',
                                        'faculty' => 'chalkboard-teacher'
                                    ][$user->role] ?? 'user' }} me-2"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                                <span class="badge bg-{{ [
                                    'ACTIVE' => 'success',
                                    'INACTIVE' => 'secondary',
                                    'SUSPENDED' => 'danger'
                                ][$user->status] ?? 'secondary' }} px-3 py-2 rounded-pill" id="previewStatus">
                                    {{ $user->status }}
                                </span>
                            </div>
                        </div>

                        <!-- User Details -->
                        <div class="user-details">
                            <div class="detail-item mb-3">
                                <small class="text-muted d-block">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </small>
                                <strong id="previewEmail">{{ $user->email_decrypted ?? $user->email ?? 'Not set' }}</strong>
                            </div>
                            <div class="detail-item mb-3">
                                <small class="text-muted d-block">
                                    <i class="fas fa-graduation-cap me-2"></i>Program
                                </small>
                                <strong id="previewProgram">{{ $user->program_decrypted ?? $user->program ?? 'Not set' }}</strong>
                            </div>
                            <div class="detail-item mb-3">
                                <small class="text-muted d-block">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address
                                </small>
                                <strong id="previewAddress">{{ $user->address ?? 'Not set' }}</strong>
                            </div>
                            <div class="detail-item">
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar-alt me-2"></i>Member Since
                                </small>
                                <strong>{{ $user->created_at->format('F d, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="sendTestEmail()">
                                <i class="fas fa-paper-plane me-2"></i>Send Test Email
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="resetPassword()">
                                <i class="fas fa-key me-2"></i>Reset Password
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="showDeleteModal()">
                                <i class="fas fa-trash me-2"></i>Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Edit Form (Scrollable) -->
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-4">
                    <h5 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Edit User Information</h5>
                </div>
                
                <!-- Scrollable form area -->
                <div class="card-body p-4" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" id="editUserForm" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden profile picture input -->
                        <input type="file" 
                               id="profile_picture" 
                               name="profile_picture" 
                               accept="image/*" 
                               class="d-none" 
                               onchange="previewProfilePicture(this)">

                        <!-- Profile Picture Upload -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-image me-2"></i>Profile Picture
                                </h6>
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="me-4 mb-3 mb-md-0">
                                        <div class="avatar avatar-xxl bg-light rounded-circle overflow-hidden border" 
                                             id="formProfilePreview">
                                            @if($user->profile_picture)
                                                <img src="{{ Storage::url($user->profile_picture) }}" 
                                                     alt="Profile" 
                                                     class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                                    <span class="text-muted fs-1" id="formAvatarInitial">
                                                        {{ strtoupper(substr($user->name_decrypted ?? $user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="text-muted small mb-3">
                                            Upload a profile picture. Recommended size: 400x400px. Max file size: 2MB.
                                        </p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <label for="profile_picture" class="btn btn-primary">
                                                <i class="fas fa-upload me-2"></i>Upload Photo
                                            </label>
                                            @if($user->profile_picture)
                                                <button type="button" class="btn btn-outline-danger" onclick="removeProfilePicture(event)">
                                                    <i class="fas fa-trash me-2"></i>Remove Photo
                                                </button>
                                            @endif
                                        </div>
                                        <input type="hidden" name="remove_profile_picture" id="removeProfilePictureInput" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="section mb-5">
                            <h5 class="text-primary mb-4 border-bottom pb-2">
                                <i class="fas fa-user-circle me-2"></i>Personal Information
                            </h5>
                            <div class="row g-4">
                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $user->name_decrypted ?? $user->name) }}" 
                                               required
                                               oninput="updatePreview('name', this.value)">
                                        <label for="name">
                                            <span class="text-danger">*</span> Full Name
                                        </label>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock me-1"></i>This field will be encrypted
                                        </small>
                                    </div>
                                </div>

                                <!-- Username -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" 
                                               class="form-control @error('username') is-invalid @enderror" 
                                               id="username" 
                                               name="username" 
                                               value="{{ old('username', $user->username_decrypted ?? $user->username) }}" 
                                               required
                                               oninput="updatePreview('username', this.value)">
                                        <label for="username">
                                            <span class="text-danger">*</span> Username
                                        </label>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock me-1"></i>This field will be encrypted
                                        </small>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $user->email_decrypted ?? $user->email) }}"
                                               oninput="updatePreview('email', this.value)">
                                        <label for="email">Email Address</label>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock me-1"></i>Optional - will be encrypted if provided
                                        </small>
                                    </div>
                                </div>

                                <!-- Program -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select @error('program') is-invalid @enderror" 
                                                id="program" 
                                                name="program"
                                                onchange="updatePreview('program', this.options[this.selectedIndex].text)">
                                            <option value="">Select Program</option>
                                            @foreach($programs as $program)
                                                <option value="{{ $program }}" 
                                                    {{ old('program', $user->program_decrypted ?? $user->program) == $program ? 'selected' : '' }}>
                                                    {{ $program }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="program">Program</label>
                                        @error('program')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-eye me-1"></i>This field is stored as plain text
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Settings Section -->
                        <div class="section mb-5">
                            <h5 class="text-primary mb-4 border-bottom pb-2">
                                <i class="fas fa-cog me-2"></i>Account Settings
                            </h5>
                            <div class="row g-4">
                                <!-- Role -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select @error('role') is-invalid @enderror" 
                                                id="role" 
                                                name="role" 
                                                required
                                                onchange="updatePreview('role', this.options[this.selectedIndex].text)">
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role }}" 
                                                    {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                                    {{ ucfirst($role) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="role">
                                            <span class="text-danger">*</span> User Role
                                        </label>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status" 
                                                required
                                                onchange="updatePreview('status', this.value)">
                                            <option value="">Select Status</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" 
                                                    {{ old('status', $user->status) == $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="status">
                                            <span class="text-danger">*</span> Account Status
                                        </label>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email Verification -->
                                <div class="col-12">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <h6 class="mb-3">
                                                <i class="fas fa-envelope me-2"></i>Email Verification Status
                                            </h6>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Verified
                                                    </span>
                                                    <small class="text-muted">
                                                        Verified on: {{ $user->email_verified_at->format('M d, Y') }}
                                                    </small>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Not Verified
                                                    </span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="verifyEmail()">
                                                        Mark as Verified
                                                    </button>
                                                @endif
                                            </div>
                                            <input type="hidden" name="email_verified_at" id="emailVerifiedInput" 
                                                   value="{{ $user->email_verified_at ? '1' : '0' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div class="section mb-5">
                            <h5 class="text-primary mb-4 border-bottom pb-2">
                                <i class="fas fa-shield-alt me-2"></i>Security
                            </h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Leave password fields blank to keep current password
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating position-relative">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password"
                                               oninput="checkPasswordStrength(this.value)">
                                        <label for="password">New Password</label>
                                        <div class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating position-relative">
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <div class="password-toggle" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Strength -->
                            <div class="mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Password Strength</span>
                                    <span class="text-muted" id="strengthText">None</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" id="strengthBar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Requirements -->
                            <div class="mt-3">
                                <small class="text-muted">Requirements:</small>
                                <ul class="list-unstyled mt-2">
                                    <li class="mb-1">
                                        <i class="fas fa-check-circle text-success me-2" id="reqLength"></i>
                                        Minimum 6 characters
                                    </li>
                                    <li class="mb-1">
                                        <i class="fas fa-check-circle text-success me-2" id="reqUppercase"></i>
                                        At least one uppercase letter
                                    </li>
                                    <li class="mb-1">
                                        <i class="fas fa-check-circle text-success me-2" id="reqNumber"></i>
                                        At least one number
                                    </li>
                                    <li>
                                        <i class="fas fa-check-circle text-success me-2" id="reqSpecial"></i>
                                        At least one special character
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="section mb-5">
                            <h5 class="text-primary mb-4 border-bottom pb-2">
                                <i class="fas fa-info-circle me-2"></i>Additional Information
                            </h5>
                            <div class="row g-4">
                                <!-- Address -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" 
                                                  name="address" 
                                                  style="height: 100px;"
                                                  oninput="updatePreview('address', this.value)"
                                                  placeholder="Enter address">{{ old('address', $user->address) }}</textarea>
                                        <label for="address">Address</label>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-eye me-1"></i>This field is stored as plain text
                                        </small>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" 
                                                  name="description" 
                                                  style="height: 150px;"
                                                  placeholder="Enter description">{{ old('description', $user->description) }}</textarea>
                                        <label for="description">Description / Bio</label>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-eye me-1"></i>This field is stored as plain text
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Permissions Info -->
                        <div class="section mb-5">
                            <h5 class="text-primary mb-4 border-bottom pb-2">
                                <i class="fas fa-user-cog me-2"></i>Role Permissions
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="permission-card" data-role="admin">
                                        <h6><i class="fas fa-user-shield text-danger me-2"></i>Administrator</h6>
                                        <p class="text-muted small">Full system access, manage all users and settings</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="permission-card" data-role="dean">
                                        <h6><i class="fas fa-user-graduate text-info me-2"></i>Dean</h6>
                                        <p class="text-muted small">Department-level administration and oversight</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="permission-card" data-role="chairperson">
                                        <h6><i class="fas fa-user-tie text-warning me-2"></i>Chairperson</h6>
                                        <p class="text-muted small">Program management and faculty oversight</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="permission-card" data-role="faculty">
                                        <h6><i class="fas fa-chalkboard-teacher text-success me-2"></i>Faculty</h6>
                                        <p class="text-muted small">Document upload and basic system access</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Single Save Button -->
                        <div class="d-flex justify-content-end mt-5 pt-4 border-top">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div class="avatar avatar-md bg-danger rounded-circle d-flex align-items-center justify-content-center me-3">
                    <i class="fas fa-exclamation text-white"></i>
                </div>
                <h5 class="modal-title text-danger">Delete User Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="lead">Are you sure you want to delete this user account?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone. All user data will be permanently deleted.
                </div>
                <div class="mt-4">
                    <label for="confirmDelete" class="form-label">
                        Type <strong>DELETE</strong> to confirm:
                    </label>
                    <input type="text" class="form-control" id="confirmDelete" placeholder="Type DELETE here">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteUser()" disabled id="deleteButton">
                    <i class="fas fa-trash me-2"></i>Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Preview profile picture
function previewProfilePicture(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Update form preview
            const formPreview = document.getElementById('formProfilePreview');
            formPreview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview" class="w-100 h-100 object-fit-cover">`;
            
            // Update card preview
            const cardPreview = document.getElementById('profilePreviewContainer');
            if (cardPreview.querySelector('#profilePreview')) {
                cardPreview.querySelector('#profilePreview').src = e.target.result;
            } else {
                cardPreview.innerHTML = `<img src="${e.target.result}" alt="Profile" class="w-100 h-100 object-fit-cover" id="profilePreview">`;
            }
            
            const avatarInitial = document.getElementById('avatarInitial');
            if (avatarInitial) avatarInitial.style.display = 'none';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove profile picture
function removeProfilePicture(event) {
    if (confirm('Remove profile picture?')) {
        document.getElementById('removeProfilePictureInput').value = '1';
        
        const formPreview = document.getElementById('formProfilePreview');
        formPreview.innerHTML = `
            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                <span class="text-muted fs-1" id="formAvatarInitial">
                    {{ strtoupper(substr($user->name_decrypted ?? $user->name, 0, 1)) }}
                </span>
            </div>
        `;
        
        const cardPreview = document.getElementById('profilePreviewContainer');
        cardPreview.innerHTML = `
            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                <span class="text-primary fs-2 fw-bold" id="avatarInitial">
                    {{ strtoupper(substr($user->name_decrypted ?? $user->name, 0, 1)) }}
                </span>
            </div>
        `;
        
        if (event && event.target) {
            event.target.style.display = 'none';
        }
    }
}

// Real-time preview updates
function updatePreview(field, value) {
    const previewElements = {
        'name': 'previewName',
        'username': 'previewUsername',
        'email': 'previewEmail',
        'program': 'previewProgram',
        'address': 'previewAddress',
        'role': 'previewRole',
        'status': 'previewStatus'
    };
    
    const elementId = previewElements[field];
    if (elementId) {
        const element = document.getElementById(elementId);
        if (!element) return;

        if (field === 'role') {
            element.innerHTML = `
                <i class="fas fa-${getRoleIcon(value.toLowerCase())} me-2"></i>
                ${value}
            `;
            element.className = `badge bg-${getRoleColor(value.toLowerCase())} px-3 py-2 rounded-pill`;
        } else if (field === 'status') {
            element.textContent = value;
            element.className = `badge bg-${getStatusColor(value)} px-3 py-2 rounded-pill`;
        } else if (field === 'name') {
            element.textContent = value;
            const avatarInitial = document.getElementById('avatarInitial');
            const formAvatarInitial = document.getElementById('formAvatarInitial');
            if (avatarInitial) {
                avatarInitial.textContent = value ? value.charAt(0).toUpperCase() : 'U';
            }
            if (formAvatarInitial) {
                formAvatarInitial.textContent = value ? value.charAt(0).toUpperCase() : 'U';
            }
        } else if (field === 'username') {
            element.textContent = value;
        } else {
            element.textContent = value || 'Not set';
        }
    }
}

// Get role icon
function getRoleIcon(role) {
    const icons = {
        'admin': 'user-shield',
        'dean': 'user-graduate',
        'chairperson': 'user-tie',
        'faculty': 'chalkboard-teacher'
    };
    return icons[role] || 'user';
}

// Get role color
function getRoleColor(role) {
    const colors = {
        'admin': 'danger',
        'dean': 'info',
        'chairperson': 'warning',
        'faculty': 'success'
    };
    return colors[role] || 'secondary';
}

// Get status color
function getStatusColor(status) {
    const colors = {
        'ACTIVE': 'success',
        'INACTIVE': 'secondary',
        'SUSPENDED': 'danger'
    };
    return colors[status] || 'secondary';
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 6) strength += 25;
    if (password.length >= 8) strength += 10;
    
    const hasUppercase = /[A-Z]/.test(password);
    if (hasUppercase) strength += 25;
    document.getElementById('reqUppercase').className = 
        `fas fa-${hasUppercase ? 'check-circle text-success' : 'times-circle text-danger'} me-2`;
    
    const hasNumber = /[0-9]/.test(password);
    if (hasNumber) strength += 25;
    document.getElementById('reqNumber').className = 
        `fas fa-${hasNumber ? 'check-circle text-success' : 'times-circle text-danger'} me-2`;
    
    const hasSpecial = /[^A-Za-z0-9]/.test(password);
    if (hasSpecial) strength += 25;
    document.getElementById('reqSpecial').className = 
        `fas fa-${hasSpecial ? 'check-circle text-success' : 'times-circle text-danger'} me-2`;
    
    const hasMinLength = password.length >= 6;
    document.getElementById('reqLength').className = 
        `fas fa-${hasMinLength ? 'check-circle text-success' : 'times-circle text-danger'} me-2`;
    
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    
    bar.style.width = strength + '%';
    
    if (strength === 0) {
        bar.className = 'progress-bar';
        text.textContent = 'None';
        text.className = 'text-muted';
    } else if (strength < 50) {
        bar.className = 'progress-bar bg-danger';
        text.textContent = 'Weak';
        text.className = 'text-danger';
    } else if (strength < 75) {
        bar.className = 'progress-bar bg-warning';
        text.textContent = 'Fair';
        text.className = 'text-warning';
    } else if (strength < 100) {
        bar.className = 'progress-bar bg-info';
        text.textContent = 'Good';
        text.className = 'text-info';
    } else {
        bar.className = 'progress-bar bg-success';
        text.textContent = 'Strong';
        text.className = 'text-success';
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    const icon = field.parentElement.querySelector('.password-toggle i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Verify email
function verifyEmail() {
    if (confirm('Mark this email as verified?')) {
        document.getElementById('emailVerifiedInput').value = '1';
        alert('Email marked as verified. Remember to save changes.');
    }
}

// Delete confirmation
function showDeleteModal() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Enable delete button when correct text is typed
document.addEventListener('DOMContentLoaded', function () {
    const confirmDeleteInput = document.getElementById('confirmDelete');
    if (confirmDeleteInput) {
        confirmDeleteInput.addEventListener('input', function(e) {
            const deleteBtn = document.getElementById('deleteButton');
            deleteBtn.disabled = e.target.value !== 'DELETE';
        });
    }

    // Highlight permission cards based on selected role
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
        const updateRoleCards = () => {
            const role = roleSelect.value;
            document.querySelectorAll('.permission-card').forEach(card => {
                if (card.dataset.role === role) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            });
        };
        roleSelect.addEventListener('change', updateRoleCards);
        updateRoleCards();
    }

    // Bootstrap form validation
    const form = document.getElementById('editUserForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    }
});

// Delete user
function deleteUser() {
    if (confirm('Final confirmation: Delete this user account?')) {
        fetch('{{ route("admin.users.destroy", $user) }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        }).then(response => {
            if (response.ok) {
                window.location.href = '{{ route("admin.users.index") }}';
            } else {
                alert('Error deleting user');
            }
        });
    }
}

// Quick actions
function sendTestEmail() {
    alert('Test email functionality would be implemented here');
}

function resetPassword() {
    if (confirm('Send password reset email to this user?')) {
        alert('Password reset email sent!');
    }
}
</script>
@endpush

<style>
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.card {
    border-radius: 15px;
    border: 1px solid #eef2f7;
}

.card-header.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px 15px 0 0 !important;
}

.avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.avatar-xl {
    width: 70px;
    height: 70px;
    font-size: 1.75rem;
}

.avatar-xxl {
    width: 100px;
    height: 100px;
    font-size: 2.5rem;
}

.avatar-md {
    width: 45px;
    height: 45px;
}

.avatar.border-3 {
    border-width: 3px !important;
}

.stat-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.bg-light-primary {
    background-color: rgba(102, 126, 234, 0.1) !important;
}

.bg-light-info {
    background-color: rgba(23, 162, 184, 0.1) !important;
}

.form-floating > .form-control {
    height: calc(3.5rem + 2px);
    padding: 1rem 0.75rem;
}

.form-floating > label {
    padding: 1rem 0.75rem;
    color: #6c757d;
}

.form-floating > textarea.form-control {
    height: auto;
    padding-top: 1.5rem;
}

.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    z-index: 10;
}

.permission-card {
    padding: 1.25rem;
    border: 2px solid #eef2f7;
    border-radius: 10px;
    transition: all 0.3s;
}

.permission-card:hover {
    border-color: #667eea;
    background-color: rgba(102, 126, 234, 0.05);
}

.permission-card.active {
    border-color: #667eea;
    background-color: rgba(102, 126, 234, 0.1);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
}

.detail-item {
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-radius: 10px;
    border-left: 3px solid #667eea;
}

.progress {
    background-color: #eef2f7;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.5s ease;
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-weight: 500;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.cursor-pointer {
    cursor: pointer;
}

.modal-content {
    border-radius: 15px;
    border: none;
}

.modal-header {
    padding: 1.5rem 1.5rem 0.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.object-fit-cover {
    object-fit: cover;
}

.section {
    padding-bottom: 2rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid #eef2f7;
}

.section:last-of-type {
    border-bottom: none;
    margin-bottom: 0;
}

/* Scrollbar styling */
.card-body::-webkit-scrollbar {
    width: 6px;
}

.card-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.card-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.card-body::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

@media (max-width: 768px) {
    .avatar-xl {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .avatar-xxl {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .sticky-top {
        position: static !important;
    }
    
    .card-body {
        max-height: none !important;
        overflow-y: visible !important;
    }
}
</style>
@endsection