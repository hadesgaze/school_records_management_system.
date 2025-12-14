@extends('layouts.dean')
@section('page-title', 'Dashboard Overview')
@section('content')

<div class="container-fluid px-4">
    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Faculty Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Faculty</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalFaculty }}</div>
                            <div class="mt-2">
                                <span class="text-success">
                                    <i class="fas fa-users mr-1"></i>
                                    Active Members
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Chairperson Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Chairperson</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalChairperson }}</div>
                            <div class="mt-2">
                                <span class="text-info">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    Department Heads
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Programs Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Programs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPrograms }}</div>
                            <div class="mt-2">
                                <span class="text-warning">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    Academic Programs
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Archived Files Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Archived Files</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalArchivedFiles }}</div>
                            <div class="mt-2">
                                <span class="text-primary">
                                    <i class="fas fa-database mr-1"></i>
                                    Total Documents
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-archive fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activities Row -->
    <div class="row">
        <!-- Recent Uploaded Files -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Uploaded Files</h6>
                    <a href="{{ route('dean.archived_files') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentUploads->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Category</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUploads as $file)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $fileIcon = 'fa-file';
                                                if(in_array($file->file_type, ['pdf'])) {
                                                    $fileIcon = 'fa-file-pdf text-danger';
                                                } elseif(in_array($file->file_type, ['doc', 'docx'])) {
                                                    $fileIcon = 'fa-file-word text-primary';
                                                } elseif(in_array($file->file_type, ['xls', 'xlsx'])) {
                                                    $fileIcon = 'fa-file-excel text-success';
                                                } elseif(in_array($file->file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                    $fileIcon = 'fa-file-image text-info';
                                                }
                                            @endphp
                                            <i class="fas {{ $fileIcon }} fa-lg mr-2"></i>
                                            <span class="text-truncate" style="max-width: 200px;" title="{{ $file->original_name }}">
                                                {{ Str::limit($file->original_name, 30) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span  class="badge bg-light text-dark">
                                            {{ $file->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $uploaderName = $file->uploader->decrypted_name ?? 'Unknown User';
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $file->uploader->profile_picture ? Storage::url($file->uploader->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($uploaderName) . '&background=random' }}" 
                                                 class="rounded-circle mr-2" width="30" height="30" alt="{{ $uploaderName }}">
                                            <span>{{ $uploaderName }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $file->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('dean.view-file-details', $file->id) }}" 
                                               class="btn btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('dean.download-archive-file', $file->id) }}" 
                                               class="btn btn-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No files uploaded yet.</p>
                        <a href="{{ route('dean.upload_files') }}" class="btn btn-primary">
                            <i class="fas fa-upload mr-2"></i>Upload First File
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats & Notifications -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="{{ route('dean.upload_files') }}" class="btn btn-primary btn-block py-3">
                                <i class="fas fa-upload fa-2x mb-2"></i><br>
                                Upload File
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('dean.archived_files') }}" class="btn btn-success btn-block py-3">
                                <i class="fas fa-archive fa-2x mb-2"></i><br>
                                View Archives
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('dean.notifications') }}" class="btn btn-warning btn-block py-3 position-relative">
                                @if($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unreadCount }}
                                    <span class="visually-hidden">unread notifications</span>
                                </span>
                                @endif
                                <i class="fas fa-bell fa-2x mb-2"></i><br>
                                Notifications
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('dean.settings') }}" class="btn btn-info btn-block py-3">
                                <i class="fas fa-cog fa-2x mb-2"></i><br>
                                Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Notifications Card -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Notifications</h6>
                    <span class="badge badge-danger">{{ $unreadCount }} unread</span>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        <a href="{{ route('dean.mark_notification_read', $notification->id) }}" 
                           class="list-group-item list-group-item-action {{ $notification->is_read ? '' : 'bg-light' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 {{ $notification->is_read ? 'text-muted' : 'text-primary' }}">
                                    {{ Str::limit($notification->message, 50) }}
                                </h6>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-muted">
                                <i class="fas fa-user mr-1"></i>
                                {{ $notification->sender->decrypted_name ?? 'System' }}
                            </p>
                            @if(!$notification->is_read)
                            <small class="text-warning">
                                <i class="fas fa-circle"></i> Unread
                            </small>
                            @endif
                        </a>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('dean.notifications') }}" class="btn btn-sm btn-outline-primary">
                            View All Notifications
                        </a>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash fa-2x text-gray-300 mb-3"></i>
                        <p class="text-muted">No notifications yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- System Statistics Row -->
    <div class="row">
        <!-- File Type Distribution -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">File Type Distribution</h6>
                </div>
                <div class="card-body">
                    @if($fileTypeStats->count() > 0)
                    <div class="chart-pie pt-4">
                        <canvas id="fileTypeChart" width="100%" height="200"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($fileTypeStats as $stat)
                        <span class="mr-3">
                            <i class="fas fa-circle" style="color: {{ $stat['color'] }}"></i>
                            {{ ucfirst($stat['type']) }} ({{ $stat['count'] }})
                        </span>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-muted">No file data available</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker">
                                @if($activity->action == 'Uploaded a document')
                                <i class="fas fa-upload text-primary"></i>
                                @elseif($activity->action == 'Logged in')
                                <i class="fas fa-sign-in-alt text-success"></i>
                                @else
                                <i class="fas fa-history text-info"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $activity->user->decrypted_name ?? 'Unknown User' }}</h6>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $activity->action }}</p>
                                @if($activity->data)
                                <small class="text-muted">
                                    @php
                                        $data = json_decode($activity->data, true);
                                    @endphp
                                    @if(isset($data['file']))
                                    File: {{ $data['file'] }}
                                    @endif
                                </small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-2x text-gray-300 mb-3"></i>
                        <p class="text-muted">No recent activity</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

   <!-- Welcome Message -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Welcome, {{ Auth::user()->name ?? 'User' }}!</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @php
                            $displayName = Auth::user()->name ?? 'User';
                        @endphp
                        <img src="{{ Auth::user()->profile_picture ? Storage::url(Auth::user()->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=0D8ABC&color=fff&size=150' }}" 
                             class="rounded-circle shadow" width="120" height="120" alt="Profile">
                    </div>
                    <div class="col-md-9">
                        
                        <p class="mb-4">{{ Auth::user()->description ?? 'Welcome to the Dean Dashboard. Manage Archived Files, and oversee operations.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 0;
        width: 20px;
        height: 20px;
        background: #fff;
        border: 3px solid #4e73df;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .timeline-content {
        padding-left: 15px;
    }
    .card {
        border-radius: 10px;
        border: none;
    }
    .btn-block {
        border-radius: 8px;
        transition: all 0.3s;
    }
    .btn-block:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File Type Chart
        @if($fileTypeStats->count() > 0)
        var ctx = document.getElementById('fileTypeChart').getContext('2d');
        var fileTypeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($fileTypeStats as $stat)
                    '{{ ucfirst($stat['type']) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($fileTypeStats as $stat)
                        {{ $stat['count'] }},
                        @endforeach
                    ],
                    backgroundColor: [
                        @foreach($fileTypeStats as $stat)
                        '{{ $stat['color'] }}',
                        @endforeach
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        @endif

        // Auto-refresh notifications every 30 seconds
        setInterval(function() {
            fetch('{{ route("dean.notifications.fetch") }}')
                .then(response => response.json())
                .then(data => {
                    // Update notification badge
                    if (data.unreadCount > 0) {
                        document.querySelector('.badge-danger').textContent = data.unreadCount;
                    }
                });
        }, 30000);
    });
</script>
@endpush

@endsection