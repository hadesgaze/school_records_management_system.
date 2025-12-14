@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Create User')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ isset($user) ? 'Edit User' : 'Create User' }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Card -->
            <div class="card shadow-lg border-0">
                <!-- Card Header -->
                <div class="card-header bg-gradient {{ isset($user) ? 'bg-warning' : 'bg-primary' }} text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg bg-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="fas {{ isset($user) ? 'fa-edit text-warning' : 'fa-user-plus text-primary' }} fs-4"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ isset($user) ? 'Edit User Profile' : 'Create New User' }}</h4>
                                <p class="mb-0 opacity-75">
                                    {{ isset($user) ? 'Update user information and permissions' : 'Add a new user to the system' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Users
                        </a>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-5">
                    <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" 
                          method="POST" 
                          class="needs-validation" 
                          novalidate
                          id="userForm">
                        @csrf
                        @if(isset($user))
                            @method('PUT')
                        @endif

                        <!-- Progress Indicator -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-indicator active">
                                    <span class="step-number">1</span>
                                    <span class="step-label">Personal Info</span>
                                </div>
                                <div class="step-line"></div>
                                <div class="step-indicator {{ old('role') || isset($user) ? 'active' : '' }}">
                                    <span class="step-number">2</span>
                                    <span class="step-label">Account Settings</span>
                                </div>
                                <div class="step-line"></div>
                                <div class="step-indicator {{ old('password') || !isset($user) ? 'active' : '' }}">
                                    <span class="step-number">3</span>
                                    <span class="step-label">Password</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Section 1: Personal Information -->
                            <div class="col-12">
                                <div class="section-header mb-4">
                                    <h5 class="text-primary mb-2">
                                        <i class="fas fa-user-circle me-2"></i>Personal Information
                                    </h5>
                                    <p class="text-muted small mb-0">Enter the user's basic details</p>
                                </div>
                            </div>
                            
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label fw-bold">
                                        <span class="text-danger">*</span> Full Name
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-primary"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $user->name_decrypted ?? ($user->name ?? '')) }}" 
                                               required
                                               placeholder="Enter full name">
                                    </div>
                                    <div class="form-text">This name will be encrypted for security</div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username" class="form-label fw-bold">
                                        <span class="text-danger">*</span> Username
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-at text-primary"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('username') is-invalid @enderror" 
                                               id="username" 
                                               name="username" 
                                               value="{{ old('username', $user->username_decrypted ?? ($user->username ?? '')) }}" 
                                               required
                                               placeholder="Choose a username">
                                    </div>
                                    <div class="form-text">Username will be encrypted for security</div>
                                    @error('username')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label fw-bold">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $user->email_decrypted ?? ($user->email ?? '')) }}"
                                               placeholder="user@example.com">
                                    </div>
                                    <div class="form-text">This email will be encrypted for security </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Section 2: Account Settings -->
                            <div class="col-12 mt-5">
                                <div class="section-header mb-4">
                                    <h5 class="text-primary mb-2">
                                        <i class="fas fa-cog me-2"></i>Account Settings
                                    </h5>
                                    <p class="text-muted small mb-0">Configure user role and permissions</p>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role" class="form-label fw-bold">
                                        <span class="text-danger">*</span> Role
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user-tag text-primary"></i>
                                        </span>
                                        <select class="form-select @error('role') is-invalid @enderror" 
                                                id="role" 
                                                name="role" 
                                                required
                                                onchange="updateRoleInfo(this.value)">
                                            <option value="">Select User Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role }}" 
                                                    {{ old('role', $user->role ?? '') == $role ? 'selected' : '' }}
                                                    data-description="{{ [
                                                        'admin' => 'Full system access',
                                                        'dean' => 'Department-level administration',
                                                        'chairperson' => 'Program-level management',
                                                        'faculty' => 'Teaching and document upload'
                                                    ][$role] ?? 'User role' }}">
                                                    {{ ucfirst($role) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="roleDescription" class="form-text mt-2"></div>
                                    @error('role')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label fw-bold">
                                        <span class="text-danger">*</span> Status
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-circle text-primary"></i>
                                        </span>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status" 
                                                required>
                                            <option value="">Select Account Status</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" 
                                                    {{ old('status', $user->status ?? '') == $status ? 'selected' : '' }}
                                                    data-icon="{{ [
                                                        'ACTIVE' => 'fa-check-circle text-success',
                                                        'INACTIVE' => 'fa-pause-circle text-secondary',
                                                        'SUSPENDED' => 'fa-ban text-danger'
                                                    ][$status] }}">
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Program -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="program" class="form-label fw-bold">Program</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-graduation-cap text-primary"></i>
                                        </span>
                                        <select class="form-select @error('program') is-invalid @enderror" 
                                                id="program" 
                                                name="program">
                                            <option value="">Select Program (Optional)</option>
                                            @foreach($programs as $program)
                                                <option value="{{ $program }}" 
                                                    {{ old('program', $user->program_decrypted ?? ($user->program ?? '')) == $program ? 'selected' : '' }}>
                                                    {{ $program }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-text">Program is stored as plain text (not encrypted)</div>
                                    @error('program')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Section 3: Password -->
                            <div class="col-12 mt-5">
                                <div class="section-header mb-4">
                                    <h5 class="text-primary mb-2">
                                        <i class="fas fa-lock me-2"></i>Password Settings
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        {{ isset($user) ? 'Enter new password to change, or leave blank to keep current' : 'Set initial password for the user' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label fw-bold">
                                        @if(isset($user))
                                            New Password
                                        @else
                                            <span class="text-danger">*</span> Password
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-key text-primary"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               {{ isset($user) ? '' : 'required' }}
                                               minlength="6"
                                               placeholder="{{ isset($user) ? 'Enter new password (min. 6 chars)' : 'Enter password (min. 6 chars)' }}">
                                        <button type="button" 
                                                class="btn btn-outline-secondary border-start-0" 
                                                onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Minimum 6 characters</div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label fw-bold">
                                        @if(isset($user))
                                            Confirm New Password
                                        @else
                                            <span class="text-danger">*</span> Confirm Password
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-key text-primary"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               {{ isset($user) ? '' : 'required' }}
                                               minlength="6"
                                               placeholder="Confirm password">
                                        <button type="button" 
                                                class="btn btn-outline-secondary border-start-0" 
                                                onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @if(isset($user))
                                        <div class="form-text">Leave both fields blank to keep current password</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Password Strength -->
                            <div class="col-12">
                                <div class="password-strength mb-3">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" id="passwordStrengthBar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <div class="mt-2">
                                        <small id="passwordStrengthText" class="text-muted">Password strength</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-5 pt-4 border-top">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if(isset($user))
                                            <div class="text-muted small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Last updated: {{ $user->updated_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-3">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-lg btn-outline-secondary px-4">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-lg btn-primary px-4 shadow-sm">
                                            <i class="fas fa-save me-2"></i>
                                            {{ isset($user) ? 'Update User' : 'Create User' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-info mt-4">
                <div class="d-flex">
                    <i class="fas fa-shield-alt fs-4 me-3 mt-1"></i>
                    <div>
                        <h6 class="alert-heading mb-2">Security Information</h6>
                        <p class="mb-2">For security reasons, sensitive user information (name, username, email) is encrypted in the database. The program field is stored as plain text for better search performance.</p>
                        <small class="text-muted">
                            <i class="fas fa-lock me-1"></i>
                            Encryption: AES-256-CBC | Hashing: Bcrypt
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Update role description
function updateRoleInfo(role) {
    const descriptionElement = document.getElementById('roleDescription');
    const selectedOption = document.querySelector(`#role option[value="${role}"]`);
    
    if (selectedOption && selectedOption.dataset.description) {
        descriptionElement.innerHTML = `
            <i class="fas fa-info-circle me-1 text-info"></i>
            ${selectedOption.dataset.description}
        `;
    } else {
        descriptionElement.innerHTML = '';
    }
}

// Initialize role description on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentRole = document.getElementById('role').value;
    if (currentRole) {
        updateRoleInfo(currentRole);
    }
    
    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let color = 'bg-danger';
            let text = 'Weak';
            
            if (password.length >= 6) strength += 20;
            if (password.length >= 8) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            
            if (strength >= 80) {
                color = 'bg-success';
                text = 'Strong';
            } else if (strength >= 60) {
                color = 'bg-info';
                text = 'Good';
            } else if (strength >= 40) {
                color = 'bg-warning';
                text = 'Fair';
            }
            
            strengthBar.style.width = strength + '%';
            strengthBar.className = `progress-bar ${color}`;
            strengthText.textContent = `Password strength: ${text}`;
        });
    }
    
    // Show/hide password requirements
    const requirementsBtn = document.getElementById('showRequirements');
    const requirements = document.getElementById('passwordRequirements');
    
    if (requirementsBtn && requirements) {
        requirementsBtn.addEventListener('click', function() {
            requirements.classList.toggle('d-none');
            const icon = this.querySelector('i');
            if (requirements.classList.contains('d-none')) {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });
    }
});

// Auto-check username availability (optional enhancement)
document.getElementById('username').addEventListener('blur', function() {
    const username = this.value;
    if (username.length < 3) return;
    
    // This would require an AJAX endpoint to check username availability
    // For now, it's just a placeholder
    console.log('Checking username:', username);
});
</script>
@endpush

<style>
.card-header.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
}
.card-header.bg-warning.bg-gradient {
    background: linear-gradient(135deg, var(--bs-warning), var(--bs-orange));
}
.avatar {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-lg {
    width: 60px;
    height: 60px;
}
.step-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}
.step-number {
    width: 40px;
    height: 40px;
    background: #e9ecef;
    color: #6c757d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all 0.3s;
}
.step-indicator.active .step-number {
    background: var(--bs-primary);
    color: white;
    box-shadow: 0 0 0 5px rgba(var(--bs-primary-rgb), 0.1);
}
.step-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
}
.step-indicator.active .step-label {
    color: var(--bs-primary);
    font-weight: 600;
}
.step-line {
    flex: 1;
    height: 2px;
    background: #e9ecef;
    margin: 0 20px;
    align-self: center;
    min-width: 60px;
}
.section-header {
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    margin-bottom: 0.5rem;
}
.input-group-text {
    transition: all 0.3s;
}
.input-group:focus-within .input-group-text {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}
.password-strength .progress {
    background: #e9ecef;
    border-radius: 3px;
}
.progress-bar {
    transition: width 0.3s ease;
}
</style>
@endsection