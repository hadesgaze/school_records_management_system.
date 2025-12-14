@extends('layouts.chairperson')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body py-4">
                    <h2 class="fw-bold mb-2">Welcome, {{ Auth::user()->name }}!</h2>
                    <p class="mb-0 opacity-75">{{ Auth::user()->program }} Program Chairperson</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Faculty -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small text-uppercase">Total Faculty</p>
                            <h3 class="fw-bold mb-0">{{ $totalFaculty ?? 0 }}</h3>
                            <small class="text-muted">Under your program</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i class="fas fa-users text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Your Files -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small text-uppercase">Your Files</p>
                            <h3 class="fw-bold mb-0">{{ $yourFiles ?? 0 }}</h3>
                            <small class="text-muted">Files you uploaded</small>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <i class="fas fa-file-alt text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Total Files -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small text-uppercase">Program Files</p>
                            <h3 class="fw-bold mb-0">{{ $programFiles ?? 0 }}</h3>
                            <small class="text-muted">Total in your program</small>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded p-3">
                            <i class="fas fa-folder-open text-info fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unread Notifications -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 small text-uppercase">Notifications</p>
                            <h3 class="fw-bold mb-0">{{ $unreadCount ?? 0 }}</h3>
                            <small class="text-muted">Unread messages</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded p-3">
                            <i class="fas fa-bell text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Notifications -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Recent Notifications</h5>
                        <a href="{{ route('chairperson.notifications') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($notifications) && $notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item border-0 px-0 {{ !$notification->is_read ? 'bg-light bg-opacity-50' : '' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-1">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary rounded-pill">New</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No notifications yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('chairperson.upload_files') }}" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload File
                        </a>
                        <a href="{{ route('chairperson.archive_files') }}" class="btn btn-outline-primary">
                            <i class="fas fa-archive me-2"></i>View Archives
                        </a>
                        <a href="{{ route('chairperson.settings') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection