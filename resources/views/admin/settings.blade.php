@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div class="container py-4">

    <h3 class="mb-4"><i class="bi bi-gear"></i> Settings</h3>

    {{-- ✅ Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 mt-3">

        {{-- 🧩 System Settings --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light fw-bold">
                    <i class="bi bi-hdd-network"></i> System Configuration
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- System Title --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">System Title</label>
                            <input type="text" name="system_title" 
                                   value="{{ $settings['system_title'] ?? '' }}" 
                                   class="form-control" required>
                        </div>

                        {{-- Department Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Department Name</label>
                            <input type="text" name="department_name" 
                                   value="{{ $settings['department_name'] ?? '' }}" 
                                   class="form-control" required>
                        </div>

                        {{-- Upload Logo --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Logo</label>
                            <input type="file" name="system_logo" class="form-control">
                            @if(!empty($settings['system_logo']))
                                <div class="mt-3 text-center">
                                    <img src="{{ asset('storage/'.$settings['system_logo']) }}" 
                                         width="90" class="rounded shadow-sm border" 
                                         alt="System Logo">
                                </div>
                            @endif
                        </div>

                        <div class="text-end mt-3">
                            <button class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 👤 Admin Profile Settings --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light fw-bold">
                    <i class="bi bi-person-circle"></i> Profile Settings
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- 👤 Profile Picture --}}
                        <div class="mb-3 text-center">
                            <label class="form-label fw-semibold">Profile Picture</label><br>
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                    class="rounded-circle shadow"
                                    style="width:120px; height:120px; object-fit:cover;">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" 
                                     alt="Default Avatar"
                                     class="rounded-circle shadow"
                                     style="width:120px; height:120px; object-fit:cover;">
                            @endif
                            <input type="file" name="profile_picture" class="form-control mt-2">
                        </div>

                        {{-- 👤 Full Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" 
                                   value="{{ Auth::user()->name }}" 
                                   class="form-control" required>
                        </div>

                        {{-- 📧 Email --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" 
                                   value="{{ Auth::user()->email }}" 
                                   class="form-control" required>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-secondary"><i class="bi bi-lock-fill"></i> Change Password</h6>

                        {{-- 🔑 Password Section --}}
                        <div class="mb-2">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter new password">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                        </div>

                        <div class="text-end mt-3">
                            <button class="btn btn-success">
                                <i class="bi bi-person-check"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
