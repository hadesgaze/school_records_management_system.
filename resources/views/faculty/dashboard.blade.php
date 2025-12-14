@extends('layouts.faculty')

@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Stats Cards with Gradient Backgrounds --}}
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="gradient-primary position-absolute top-0 start-0 w-100 h-100 opacity-10"></div>
                    <div class="d-flex justify-content-between align-items-center position-relative">
                        <div>
                            <p class="text-muted mb-1">Total Files</p>
                            <h2 class="fw-bold text-dark mb-0">{{ $totalFiles }}</h2>
                            <small class="text-muted">Your uploaded documents</small>
                        </div>
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-3">
                            <span class="material-icons text-primary" style="font-size: 32px;">folder_open</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-primary">
                            <span class="material-icons align-text-bottom" style="font-size: 16px;">trending_up</span>
                            Manage your files
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="gradient-success position-absolute top-0 start-0 w-100 h-100 opacity-10"></div>
                    <div class="d-flex justify-content-between align-items-center position-relative">
                        <div>
                            <p class="text-muted mb-1">Accessible Categories</p>
                            <h2 class="fw-bold text-dark mb-0">{{ $totalCategories }}</h2>
                            <small class="text-muted">Available to you</small>
                        </div>
                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle p-3">
                            <span class="material-icons text-success" style="font-size: 32px;">category</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-success">
                            <span class="material-icons align-text-bottom" style="font-size: 16px;">visibility</span>
                            Browse categories
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="gradient-info position-absolute top-0 start-0 w-100 h-100 opacity-10"></div>
                    <div class="d-flex justify-content-between align-items-center position-relative">
                        <div>
                            <p class="text-muted mb-1">Welcome Back</p>
                            <h3 class="fw-bold text-dark mb-0">{{ Auth::user()->name }}</h3>
                            <small class="text-muted">Faculty Member</small>
                        </div>
                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle p-3">
                            <span class="material-icons text-info" style="font-size: 32px;">person</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-info">
                            <span class="material-icons align-text-bottom" style="font-size: 16px;">schedule</span>
                            Last login: Today
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Uploads & Activity Section --}}
    <div class="row mb-5">
        {{-- Recent Uploads --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">
                                <span class="material-icons align-text-bottom text-primary me-2">upload</span>
                                Recent Uploads
                            </h5>
                            <p class="text-muted mb-0">Your latest uploaded files</p>
                        </div>
                        @if($recentFiles->count() > 0)
                        <a href="{{ route('faculty.archived_files') }}" class="text-decoration-none text-primary small">
                            View All
                            <span class="material-icons align-text-bottom" style="font-size: 16px;">chevron_right</span>
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-4">
                    @if($recentFiles->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentFiles as $file)
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="file-icon me-3">
                                        @php
                                            $extension = pathinfo($file->filename, PATHINFO_EXTENSION);
                                            $icon = match($extension) {
                                                'pdf' => 'picture_as_pdf',
                                                'doc', 'docx' => 'description',
                                                'xls', 'xlsx' => 'table_chart',
                                                'ppt', 'pptx' => 'slideshow',
                                                'jpg', 'jpeg', 'png', 'gif' => 'image',
                                                default => 'insert_drive_file'
                                            };
                                            $color = match($extension) {
                                                'pdf' => 'text-danger',
                                                'doc', 'docx' => 'text-primary',
                                                'xls', 'xlsx' => 'text-success',
                                                'ppt', 'pptx' => 'text-warning',
                                                default => 'text-secondary'
                                            };
                                        @endphp
                                        <span class="material-icons {{ $color }} p-2 bg-light rounded" style="font-size: 32px;">
                                            {{ $icon }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold mb-1 text-truncate" title="{{ $file->original_name }}">
                                            {{ Str::limit($file->original_name, 40) }}
                                        </h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <span class="material-icons align-text-bottom" style="font-size: 14px;">schedule</span>
                                                {{ $file->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <span class="material-icons text-muted" style="font-size: 64px;">cloud_upload</span>
                            </div>
                            <h5 class="text-muted mb-2">No files uploaded yet</h5>
                            <p class="text-muted mb-3">Start by uploading your first document</p>
                            <a href="{{ route('faculty.upload.document') }}" class="btn btn-primary">
                                <span class="material-icons align-text-bottom me-1">add</span>
                                Upload File
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">
                                <span class="material-icons align-text-bottom text-primary me-2">history</span>
                                Recent Activity
                            </h5>
                            <p class="text-muted mb-0">Your latest actions and updates</p>
                        </div>
                        @if($recentActivities->count() > 0)
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            {{ $recentActivities->count() }} activities
                        </span>
                        @endif
                    </div>
                </div>
                <div class="card-body px-4">
                    @if($recentActivities->count() > 0)
                        <div class="timeline">
                            @foreach($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold mb-1">{{ $activity->action }}</h6>
                                            <p class="mb-1">
                                                @php
                                                    $data = json_decode($activity->data);
                                                    $fileName = $data->file ?? '';
                                                    $fileName = strlen($fileName) > 50 ? substr($fileName, 0, 50).'...' : $fileName;
                                                @endphp
                                                @if($fileName)
                                                <span class="text-dark">{{ $fileName }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-light text-dark border px-3 py-1">
                                                {{ $activity->created_at->format('h:i A') }}
                                            </span>
                                            <p class="text-muted small mt-1 mb-0">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <span class="material-icons text-muted" style="font-size: 64px;">inbox</span>
                            </div>
                            <h5 class="text-muted mb-2">No activities yet</h5>
                            <p class="text-muted">Your activities will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-1">
                    <span class="material-icons align-text-bottom text-primary me-2">rocket_launch</span>
                    Quick Actions
                </h5>
                <p class="text-muted mb-0">Frequently used actions</p>
            </div>
            <div class="card-body px-4">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('faculty.upload_files') }}" class="card action-card border-0 text-decoration-none">
                            <div class="card-body text-center py-4">
                                <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-3 mb-3 mx-auto">
                                    <span class="material-icons text-primary" style="font-size: 32px;">add</span>
                                </div>
                                <h6 class="fw-semibold text-dark mb-0">Upload File</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('faculty.archived_files') }}" class="card action-card border-0 text-decoration-none">
                            <div class="card-body text-center py-4">
                                <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle p-3 mb-3 mx-auto">
                                    <span class="material-icons text-success" style="font-size: 32px;">folder</span>
                                </div>
                                <h6 class="fw-semibold text-dark mb-0">My Files</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('faculty.settings') }}" class="card action-card border-0 text-decoration-none">
                            <div class="card-body text-center py-4">
                                <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle p-3 mb-3 mx-auto">
                                    <span class="material-icons text-warning" style="font-size: 32px;">settings</span>
                                </div>
                                <h6 class="fw-semibold text-dark mb-0">Profile</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <form method="POST" action="{{ route('logout') }}" class="h-100">
                            @csrf
                            <button type="submit" class="card action-card border-0 text-decoration-none w-100 h-100 bg-transparent p-0">
                                <div class="card-body text-center py-4">
                                    <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle p-3 mb-3 mx-auto">
                                        <span class="material-icons text-danger" style="font-size: 32px;">logout</span>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-0">Log Out</h6>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
{{-- Custom CSS for the new design --}}
<style>
    .gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .icon-wrapper {
        transition: transform 0.3s ease;
    }
    
    .card:hover .icon-wrapper {
        transform: translateY(-5px);
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-marker {
        position: absolute;
        left: -9px;
        top: 0;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 3px var(--bs-primary);
    }
    
    .timeline-content {
        padding-left: 20px;
        border-left: 2px solid #e9ecef;
    }
    
    .timeline-item:last-child .timeline-content {
        border-left-style: dotted;
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }
    
    .badge {
        transition: all 0.3s ease;
    }
    
    .action-card {
        transition: all 0.3s ease;
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08) !important;
    }
    
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    
    .file-icon {
        flex-shrink: 0;
    }
    
    .text-truncate {
        max-width: 300px;
    }
    
    @media (max-width: 768px) {
        .text-truncate {
            max-width: 200px;
        }
        
        .file-actions .btn {
            padding: 0.25rem 0.5rem;
        }
    }
</style>
@endsection