@extends('layouts.admin')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .activity-item {
        border-left: 3px solid #e9ecef;
        transition: border-color 0.2s;
    }
    .activity-item:hover {
        border-left-color: #0d6efd;
        background-color: #f8f9fa;
    }
    .badge-role {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <!-- Statistics Cards Row 1 - User Stats -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total Users</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalUsers) }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> Active: {{ $activeUsers }}
                            </small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total Files</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalFiles) }}</h3>
                            <small class="text-info">
                                <i class="bi bi-calendar-check"></i> This month: {{ $filesThisMonth }}
                            </small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total Storage</p>
                            <h3 class="fw-bold mb-0">{{ $storageStats['formatted_size'] }}</h3>
                            <small class="text-warning">
                                <i class="bi bi-speedometer2"></i> Avg: {{ $storageStats['formatted_average'] }}
                            </small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hdd-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Categories</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalCategories) }}</h3>
                            <small class="text-info">
                                <i class="bi bi-check-circle"></i> With files: {{ $categoriesWithFiles }}
                            </small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-folder-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Status Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success fs-3 me-3"></i>
                        <div>
                            <h6 class="mb-0 text-muted">Active Users</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format($activeUsers) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-secondary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-pause-circle-fill text-secondary fs-3 me-3"></i>
                        <div>
                            <h6 class="mb-0 text-muted">Inactive Users</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format($inactiveUsers) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-x-circle-fill text-danger fs-3 me-3"></i>
                        <div>
                            <h6 class="mb-0 text-muted">Suspended Users</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format($suspendedUsers) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Last 7 Days Activity Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Activity Trend (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users by Role -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Users by Role</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="roleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Charts -->
    <div class="row g-3 mb-4">
        <!-- Files by Category -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Files by Category</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files by Type -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Files by Type</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="fileTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Uploads -->
    <div class="row g-3">
        <!-- Recent Activities -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Recent Activity</h5>
                    <a href="{{ route('admin.audit-logs') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentActivities as $activity)
                    <div class="activity-item p-3 border-bottom">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                @if($activity->user && $activity->user->profile_picture)
                                <img src="{{ asset('storage/' . $activity->user->profile_picture) }}" 
                                     class="rounded-circle" width="40" height="40" alt="User">
                                @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $activity->user ? $activity->user->decrypted_name : 'Unknown' }}</strong>
                                        <p class="mb-0 text-muted small">{{ $activity->action }}</p>
                                    </div>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                                @if($activity->module)
                                <span class="badge bg-light text-dark mt-1">{{ $activity->module }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No recent activity
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Uploads -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Recent Uploads</h5>
                    <a href="{{ route('admin.archive_files') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentUploads as $upload)
                    <div class="p-3 border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-light rounded p-2">
                                    @php
                                    $icon = match($upload->file_type) {
                                        'pdf' => 'file-earmark-pdf-fill text-danger',
                                        'doc', 'docx' => 'file-earmark-word-fill text-primary',
                                        'xls', 'xlsx' => 'file-earmark-excel-fill text-success',
                                        'jpg', 'jpeg', 'png', 'gif' => 'file-earmark-image-fill text-info',
                                        default => 'file-earmark-fill text-secondary'
                                    };
                                    @endphp
                                    <i class="bi bi-{{ $icon }} fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <h6 class="mb-1 text-truncate">{{ $upload->original_name }}</h6>
                                <p class="mb-1 small text-muted">
                                    <i class="bi bi-person"></i> {{ $upload->uploader ? $upload->uploader->decrypted_name : 'Unknown' }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark">{{ $upload->category->name ?? 'Uncategorized' }}</span>
                                    <small class="text-muted">{{ $upload->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-cloud-upload fs-1 d-block mb-2"></i>
                        No recent uploads
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Chart.js default configuration
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    Chart.defaults.color = '#6c757d';

    // Activity Trend Chart (Last 7 Days)
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($last7Days->pluck('date')) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($last7Days->pluck('users')) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'New Files',
                data: {!! json_encode($last7Days->pluck('files')) !!},
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Users by Role - Doughnut Chart
    const roleCtx = document.getElementById('roleChart').getContext('2d');
    new Chart(roleCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($usersByRole)) !!},
            datasets: [{
                data: {!! json_encode(array_values($usersByRole)) !!},
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Files by Category - Bar Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($filesByCategory)) !!},
            datasets: [{
                label: 'Files',
                data: {!! json_encode(array_values($filesByCategory)) !!},
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Files by Type - Pie Chart
    const fileTypeCtx = document.getElementById('fileTypeChart').getContext('2d');
    new Chart(fileTypeCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($filesByType)) !!},
            datasets: [{
                data: {!! json_encode(array_values($filesByType)) !!},
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#0dcaf0',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush