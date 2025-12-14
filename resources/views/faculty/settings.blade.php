@extends('layouts.faculty')
@section('page-title', 'Profile Settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-primary rounded-circle p-3 me-3">
                        <i class="bi bi-person-gear text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="h4 mb-1 fw-bold text-dark">Profile Settings</h2>
                        <p class="text-muted mb-0">Manage your account information and security</p>
                    </div>
                </div>
                <div class="badge bg-light text-dark border">
                    <i class="bi bi-shield-check me-2"></i>Faculty Account
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Profile Information Card -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold text-primary mb-0">
                        <i class="bi bi-person-badge me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <div class="avatar-upload position-relative">
                                @if(Auth::user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                         alt="Profile Picture" 
                                         class="rounded-circle shadow"
                                         style="width:120px; height:120px; object-fit:cover;"
                                         id="profilePreview">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4e73df&color=fff&bold=true&size=120"
                                         alt="Default Avatar"
                                         class="rounded-circle shadow"
                                         style="width:120px; height:120px; object-fit:cover;"
                                         id="profilePreview">
                                @endif
                                <div class="avatar-overlay rounded-circle">
                                    <i class="bi bi-camera-fill text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h4>
                            <p class="text-muted mb-2">
                                <i class="bi bi-envelope me-2"></i>{{ Auth::user()->email }}
                            </p>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                <i class="bi bi-award me-1"></i>Faculty
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('faculty.settings.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Profile Picture</label>
                                <input type="file" name="profile_picture" id="profilePictureInput" class="form-control @error('profile_picture') is-invalid @enderror" accept="image/*">
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Recommended: Square image, max 4MB, JPG/PNG format</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control border-start-0 border-top-0 border-end-0 rounded-0 px-0 @error('name') is-invalid @enderror" 
                                      value="{{ old('name', Auth::user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control border-start-0 border-top-0 border-end-0 rounded-0 px-0 @error('username') is-invalid @enderror" 
                                      value="{{ old('username', Auth::user()->username) }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control border-start-0 border-top-0 border-end-0 rounded-0 px-0 @error('email') is-invalid @enderror" 
                                      value="{{ old('email', Auth::user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Program</label>
                                <input type="text" name="program" class="form-control border-start-0 border-top-0 border-end-0 rounded-0 px-0 @error('program') is-invalid @enderror" 
                                      value="{{ old('program', Auth::user()->program) }}"
                                      placeholder="e.g. BSCS, BSIT, BINDTECH">
                                @error('program')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Address</label>
                                <input type="text" name="address" class="form-control border-start-0 border-top-0 border-end-0 rounded-0 px-0 @error('address') is-invalid @enderror" 
                                      value="{{ old('address', Auth::user()->address) }}"
                                      placeholder="Enter your address">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Professional Bio</label>
                                <textarea name="description" class="form-control border-start-0 border-top-0 border-end-0 rounded-0 px-0 @error('description') is-invalid @enderror" 
                                         rows="3"
                                         placeholder="Tell something about yourself...">{{ old('description', Auth::user()->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Describe your role, expertise, and responsibilities</div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>Update Profile
                                </button>
                                <button type="reset" class="btn btn-outline-secondary px-4 py-2 ms-2">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Card -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold text-success mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Security Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="security-status mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="security-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-check-lg text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Account Protected</h6>
                                <small class="text-muted">Last updated: {{ Auth::user()->updated_at->format('M j, Y') }}</small>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('faculty.settings.profile.update') }}" method="POST" id="passwordForm">
                        @csrf
                        <p class="text-muted mb-3 small">Leave password fields blank if you don't want to change your password</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Enter current password">
                                <span class="input-group-text bg-transparent">
                                    <i class="bi bi-key text-muted"></i>
                                </span>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter new password">
                                <span class="input-group-text bg-transparent">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Minimum 8 characters with letters and numbers</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                                <span class="input-group-text bg-transparent">
                                    <i class="bi bi-lock-fill text-muted"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2">
                            <i class="bi bi-shield-check me-2"></i>Update Password
                        </button>
                    </form>

                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-semibold text-dark mb-3">Security Tips</h6>
                        <ul class="list-unstyled text-sm text-muted">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Use a strong, unique password
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Don't reuse passwords from other sites
                            </li>
                            <li class="mb-0">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Update your password regularly
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            <div class="feature-icon bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                                <i class="bi bi-calendar-check text-primary fs-4"></i>
                            </div>
                            <h6 class="fw-bold">Member Since</h6>
                            <p class="text-muted mb-0">{{ Auth::user()->created_at->format('F j, Y') }}</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="feature-icon bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                                <i class="bi bi-clock-history text-info fs-4"></i>
                            </div>
                            <h6 class="fw-bold">Last Updated</h6>
                            <p class="text-muted mb-0">{{ Auth::user()->updated_at->format('F j, Y') }}</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="feature-icon bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                                <i class="bi bi-person-check text-warning fs-4"></i>
                            </div>
                            <h6 class="fw-bold">Account Status</h6>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-upload {
    cursor: pointer;
    transition: transform 0.2s;
}

.avatar-upload:hover {
    transform: scale(1.05);
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.avatar-upload:hover .avatar-overlay {
    opacity: 1;
}

.header-icon {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}

.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.border-start-0:focus {
    border-color: #4e73df !important;
    box-shadow: none !important;
}

.security-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.feature-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Ensure consistent circular profile picture */
#profilePreview {
    object-fit: cover;
    aspect-ratio: 1 / 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profilePictureInput = document.getElementById('profilePictureInput');
    const profilePreview = document.getElementById('profilePreview');
    const avatarUpload = document.querySelector('.avatar-upload');
    
    // Click on avatar to trigger file input
    if (avatarUpload && profilePictureInput) {
        avatarUpload.addEventListener('click', function() {
            profilePictureInput.click();
        });
    }
    
    // Profile picture preview with validation
    if (profilePictureInput && profilePreview) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, JPEG, or PNG)');
                    this.value = '';
                    return;
                }
                
                // Validate file size (4MB max)
                if (file.size > 4 * 1024 * 1024) {
                    alert('File size must be less than 4MB');
                    this.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Add loading state to form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Saving...';
                submitBtn.disabled = true;
                
                // Revert after 5 seconds if still processing (safety measure)
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 5000);
            }
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection