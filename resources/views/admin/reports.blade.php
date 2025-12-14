@extends('layouts.admin')

@section('page-title', 'System Reports')

@section('styles')
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3a0ca3;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #3b82f6;
        --dark-color: #1f2937;
        --light-color: #f9fafb;
        --chart-1: #4361ee;
        --chart-2: #3a0ca3;
        --chart-3: #7209b7;
        --chart-4: #f72585;
        --chart-5: #4cc9f0;
    }
    
    .glass-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .glass-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card {
        transition: all 0.3s ease;
        border-left: 6px solid;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
    }
    
    .stat-card:hover {
        transform: translateY(-5px) scale(1.02);
    }
    
    .stat-card.user-stat { border-left-color: var(--chart-1); }
    .stat-card.archive-stat { border-left-color: var(--chart-2); }
    .stat-card.file-stat { border-left-color: var(--chart-3); }
    .stat-card.size-stat { border-left-color: var(--chart-4); }
    
    .chart-container {
        position: relative;
        height: 300px;
        background: linear-gradient(145deg, #ffffff, #f5f7fa);
        border-radius: 15px;
        padding: 20px;
    }
    
    .filter-chip {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .file-icon-wrapper {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 24px;
        transition: all 0.3s ease;
    }
    
    .file-icon-wrapper:hover {
        transform: rotate(5deg) scale(1.1);
    }
    
    .file-pdf { 
        background: linear-gradient(135deg, #fecaca, #f87171);
        color: #dc2626;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.2);
    }
    .file-doc { 
        background: linear-gradient(135deg, #dbeafe, #60a5fa);
        color: #1d4ed8;
        box-shadow: 0 6px 20px rgba(29, 78, 216, 0.2);
    }
    .file-xls { 
        background: linear-gradient(135deg, #dcfce7, #4ade80);
        color: #16a34a;
        box-shadow: 0 6px 20px rgba(22, 163, 74, 0.2);
    }
    .file-img { 
        background: linear-gradient(135deg, #fef3c7, #fbbf24);
        color: #d97706;
        box-shadow: 0 6px 20px rgba(217, 119, 6, 0.2);
    }
    .file-default { 
        background: linear-gradient(135deg, #f3f4f6, #9ca3af);
        color: #4b5563;
        box-shadow: 0 6px 20px rgba(75, 85, 99, 0.2);
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .progress-ring {
        width: 80px;
        height: 80px;
    }
    
    .progress-ring__circle {
        stroke-width: 8;
        fill: transparent;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
    
    .floating-action {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
    }
    
    .animated-gradient-text {
        background: linear-gradient(90deg, #4361ee, #3a0ca3, #7209b7);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradient 3s ease infinite;
    }
    
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .hover-lift {
        transition: transform 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-3px);
    }
    
    .glow-effect {
        position: relative;
    }
    
    .glow-effect::after {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #4361ee, #3a0ca3, #7209b7, #f72585);
        border-radius: inherit;
        z-index: -1;
        filter: blur(10px);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .glow-effect:hover::after {
        opacity: 0.6;
    }
    
    .card-hover {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-2 animated-gradient-text fw-bold">📊 System Analytics</h1>
            <p class="text-muted">Comprehensive insights and analytics from your system data</p>
        </div>
        <div class="d-flex gap-2">
            @php
                $categoryFilter = request('category', '');
                $programFilter = request('program', '');
                $roleFilter = request('role', '');
                $dateFrom = request('from', '');
                $dateTo = request('to', '');
            @endphp
            
            <a href="{{ route('admin.reports.export', ['type' => 'excel', 'role' => $roleFilter, 'category' => $categoryFilter, 'program' => $programFilter, 'from' => $dateFrom, 'to' => $dateTo]) }}" 
               class="btn btn-success export-btn glow-effect">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'pdf', 'role' => $roleFilter, 'category' => $categoryFilter, 'program' => $programFilter, 'from' => $dateFrom, 'to' => $dateTo]) }}" 
               class="btn btn-danger export-btn glow-effect ms-2">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card glass-card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3 d-flex align-items-center">
                <i class="fas fa-sliders-h me-2"></i> Advanced Filters
            </h5>
            <form method="GET" action="{{ route('admin.reports') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">👤 User Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        @foreach($usersByRole->keys() as $role)
                            <option value="{{ $role }}" {{ $roleFilter == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">📂 Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryFilter == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">🎓 Program</label>
                    <select name="program" class="form-select form-select-sm">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program }}" {{ $programFilter == $program ? 'selected' : '' }}>
                                {{ $program }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">📅 Date From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">📅 Date To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100 glow-effect">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                </div>
            </form>
            
            <!-- Active Filters -->
            @if($roleFilter || $categoryFilter || $programFilter || $dateFrom || $dateTo)
            <div class="mt-4 pt-3 border-top">
                <small class="text-muted">Active filters:</small>
                <div class="d-flex gap-2 flex-wrap mt-2">
                    @if($roleFilter)
                    <div class="filter-chip">
                        <i class="fas fa-user-tag"></i>
                        Role: {{ ucfirst($roleFilter) }}
                        <a href="{{ request()->fullUrlWithQuery(['role' => null]) }}" class="text-white ms-2">×</a>
                    </div>
                    @endif
                    @if($categoryFilter)
                    @php
                        $selectedCategory = $categories->firstWhere('id', $categoryFilter);
                    @endphp
                    <div class="filter-chip" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-folder"></i>
                        Category: {{ $selectedCategory ? $selectedCategory->name : 'Unknown' }}
                        <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="text-white ms-2">×</a>
                    </div>
                    @endif
                    @if($programFilter)
                    <div class="filter-chip" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-graduation-cap"></i>
                        Program: {{ $programFilter }}
                        <a href="{{ request()->fullUrlWithQuery(['program' => null]) }}" class="text-white ms-2">×</a>
                    </div>
                    @endif
                    @if($dateFrom)
                    <div class="filter-chip" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                        <i class="fas fa-calendar-alt"></i>
                        From: {{ $dateFrom }}
                        <a href="{{ request()->fullUrlWithQuery(['from' => null]) }}" class="text-white ms-2">×</a>
                    </div>
                    @endif
                    @if($dateTo)
                    <div class="filter-chip" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fas fa-calendar-alt"></i>
                        To: {{ $dateTo }}
                        <a href="{{ request()->fullUrlWithQuery(['to' => null]) }}" class="text-white ms-2">×</a>
                    </div>
                    @endif
                    @if($roleFilter || $categoryFilter || $programFilter || $dateFrom || $dateTo)
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-times me-1"></i> Clear All
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card user-stat shadow-sm glass-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">👥 Total Users</h6>
                            <h2 class="fw-bold mb-0">{{ $users->count() }}</h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                @php
                                    $userGrowth = $users->count() > 0 ? number_format($users->count() / max($users->count(), 1) * 100, 1) : '0.0';
                                @endphp
                                {{ $userGrowth }}% growth
                            </small>
                        </div>
                        <div class="position-relative">
                            <div class="progress-ring">
                                <svg class="progress-ring__svg" width="80" height="80">
                                    <circle class="progress-ring__circle" stroke="#4361ee" stroke-dasharray="226.08" stroke-dashoffset="56.52" r="36" cx="40" cy="40"></circle>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card archive-stat shadow-sm glass-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">📁 Archive Files</h6>
                            <h2 class="fw-bold mb-0">{{ $archiveFiles->count() }}</h2>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                @php
                                    $archiveGrowth = $archiveFiles->count() > 0 ? number_format($archiveFiles->count() / max($archiveFiles->count(), 1) * 100, 1) : '0.0';
                                @endphp
                                {{ $archiveGrowth }}% increase
                            </small>
                        </div>
                        <div class="position-relative">
                            <div class="progress-ring">
                                <svg class="progress-ring__svg" width="80" height="80">
                                    <circle class="progress-ring__circle" stroke="#3a0ca3" stroke-dasharray="226.08" stroke-dashoffset="45.216" r="36" cx="40" cy="40"></circle>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <i class="fas fa-archive fa-2x text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card file-stat shadow-sm glass-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">📄 File Types</h6>
                            <h2 class="fw-bold mb-0">{{ $archiveByFileType->count() }}</h2>
                            <small class="text-warning">
                                <i class="fas fa-chart-line me-1"></i>
                                @php
                                    $diversity = $archiveFiles->count() > 0 ? number_format($archiveByFileType->count() / max($archiveFiles->count(), 1) * 100, 1) : '0.0';
                                @endphp
                                {{ $diversity }}% diversity
                            </small>
                        </div>
                        <div class="position-relative">
                            <div class="progress-ring">
                                <svg class="progress-ring__svg" width="80" height="80">
                                    <circle class="progress-ring__circle" stroke="#7209b7" stroke-dasharray="226.08" stroke-dashoffset="67.824" r="36" cx="40" cy="40"></circle>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <i class="fas fa-file-alt fa-2x text-purple"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card size-stat shadow-sm glass-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">💾 Storage Used</h6>
                            <h2 class="fw-bold mb-0">{{ formatBytes($totalFileSize) }}</h2>
                            <small class="text-info">
                                <i class="fas fa-hdd me-1"></i>
                                @php
                                    $gbSize = $totalFileSize / (1024 * 1024 * 1024);
                                @endphp
                                {{ number_format($gbSize, 2) }} GB total
                            </small>
                        </div>
                        <div class="position-relative">
                            <div class="progress-ring">
                                <svg class="progress-ring__svg" width="80" height="80">
                                    <circle class="progress-ring__circle" stroke="#f72585" stroke-dasharray="226.08" stroke-dashoffset="22.608" r="36" cx="40" cy="40"></circle>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <i class="fas fa-database fa-2x text-pink"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Users by Role - Pie Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm h-100 glass-card">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-user-friends me-2 text-primary"></i>
                        User Distribution by Role
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="usersByRolePieChart"></canvas>
                    </div>
                    <div class="row mt-4">
                        @foreach($usersByRole as $role => $count)
                        <div class="col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: {{ getChartColor($loop->index) }}"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">{{ ucfirst($role) }}</small>
                                        <small class="fw-bold">{{ $count }}</small>
                                    </div>
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar" style="width: {{ ($count / max($users->count(), 1)) * 100 }}%; background-color: {{ getChartColor($loop->index) }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- File Types Distribution - Pie Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm h-100 glass-card">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-file me-2 text-success"></i>
                        File Types Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="fileTypesPieChart"></canvas>
                    </div>
                    <div class="row mt-4">
                        @php
                            $fileTypes = $archiveFiles->groupBy(function($file) {
                                if (str_contains($file->file_type, 'pdf')) return 'PDF';
                                if (str_contains($file->file_type, 'word') || str_contains($file->file_type, 'doc')) return 'DOC';
                                if (str_contains($file->file_type, 'excel') || str_contains($file->file_type, 'sheet')) return 'EXCEL';
                                if (str_contains($file->file_type, 'image')) return 'IMAGE';
                                return 'OTHER';
                            });
                        @endphp
                        @foreach($fileTypes as $type => $files)
                        <div class="col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2" style="width: 12px; height: 12px; background-color: {{ getFileTypeColor($type) }}"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">{{ $type }}</small>
                                        <small class="fw-bold">{{ $files->count() }}</small>
                                    </div>
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar" style="width: {{ ($files->count() / max($archiveFiles->count(), 1)) * 100 }}%; background-color: {{ getFileTypeColor($type) }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mb-4">
        <!-- Recent Users -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm h-100 glass-card card-hover">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-user-clock me-2 text-info"></i> Recent Users</span>
                        <span class="badge bg-primary rounded-pill">{{ $users->count() }} total</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users->take(8) as $user)
                                <tr class="hover-lift">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($user->avatar)
                                                <img src="{{ asset($user->avatar) }}" alt="{{ $user->decrypted_name ?? 'User' }}" class="user-avatar">
                                            @else
                                                <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold">
                                                    {{ substr($user->decrypted_name ?? 'U', 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">
                                                    {{ $user->decrypted_name ?? 'Unknown User' }}
                                                </div>
                                                <small class="text-muted">{{ $user->decrypted_email ?? 'No email' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->status == 'active' ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-warning bg-opacity-10 text-warning border border-warning' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                                            <p>No users found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Archives -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm h-100 glass-card card-hover">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-file-upload me-2 text-success"></i> Recent Archives</span>
                        <span class="badge bg-success rounded-pill">{{ $archiveFiles->count() }} total</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>File</th>
                                    <th>Uploader</th>
                                    <th>Size</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($archiveFiles->take(8) as $file)
                                <tr class="hover-lift">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @php
                                                $fileClass = 'file-default';
                                                $fileIcon = 'fa-file';
                                                if (str_contains($file->file_type, 'pdf')) {
                                                    $fileClass = 'file-pdf';
                                                    $fileIcon = 'fa-file-pdf';
                                                } elseif (str_contains($file->file_type, 'word') || str_contains($file->file_type, 'doc')) {
                                                    $fileClass = 'file-doc';
                                                    $fileIcon = 'fa-file-word';
                                                } elseif (str_contains($file->file_type, 'excel') || str_contains($file->file_type, 'sheet')) {
                                                    $fileClass = 'file-xls';
                                                    $fileIcon = 'fa-file-excel';
                                                } elseif (str_contains($file->file_type, 'image')) {
                                                    $fileClass = 'file-img';
                                                    $fileIcon = 'fa-file-image';
                                                }
                                            @endphp
                                            <div class="file-icon-wrapper {{ $fileClass }}">
                                                <i class="fas {{ $fileIcon }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-truncate" style="max-width: 150px;">
                                                    {{ $file->original_name }}
                                                </div>
                                                <small class="text-muted">{{ $file->file_type }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($file->uploader)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="user-avatar bg-info text-white d-flex align-items-center justify-content-center fw-bold">
                                                    {{ substr($file->uploader->decrypted_name ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $file->uploader->decrypted_name ?? 'Unknown Uploader' }}</div>
                                                    <small class="text-muted">{{ ucfirst($file->uploader->role) }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ formatBytes($file->file_size) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $file->created_at->format('M d') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-archive fa-3x mb-3"></i>
                                            <p>No archive files found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Activity Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm glass-card">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-line me-2 text-warning"></i>
                        Monthly Activity Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="monthlyActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Refresh Button -->
<div class="floating-action">
    <button class="btn btn-primary btn-lg rounded-circle shadow-lg glow-effect" onclick="location.reload()" title="Refresh Dashboard">
        <i class="fas fa-sync-alt"></i>
    </button>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    // Register plugin
    Chart.register(ChartDataLabels);

    // Helper functions
    function getChartColor(index) {
        const colors = ['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0', '#4895ef', '#560bad'];
        return colors[index % colors.length];
    }

    function getFileTypeColor(type) {
        const colors = {
            'PDF': '#ef4444',
            'DOC': '#3b82f6',
            'EXCEL': '#10b981',
            'IMAGE': '#f59e0b',
            'OTHER': '#6b7280'
        };
        return colors[type] || '#6b7280';
    }

    // Users by Role Pie Chart
    const usersRolePieCtx = document.getElementById('usersByRolePieChart').getContext('2d');
    const usersRolePieChart = new Chart(usersRolePieCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($usersByRole->keys()->map(fn($role) => ucfirst($role))->toArray()) !!},
            datasets: [{
                data: {!! json_encode($usersByRole->values()->toArray()) !!},
                backgroundColor: Array.from({length: {{ $usersByRole->count() }}}, (_, i) => getChartColor(i)),
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: (value, ctx) => {
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return percentage + '%';
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 2000
            }
        }
    });

    // File Types Pie Chart
    @php
        $fileTypesData = $archiveFiles->groupBy(function($file) {
            if (str_contains($file->file_type, 'pdf')) return 'PDF';
            if (str_contains($file->file_type, 'word') || str_contains($file->file_type, 'doc')) return 'DOC';
            if (str_contains($file->file_type, 'excel') || str_contains($file->file_type, 'sheet')) return 'EXCEL';
            if (str_contains($file->file_type, 'image')) return 'IMAGE';
            return 'OTHER';
        })->map->count();
    @endphp

    const fileTypesPieCtx = document.getElementById('fileTypesPieChart').getContext('2d');
    const fileTypesPieChart = new Chart(fileTypesPieCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($fileTypesData->keys()->toArray()) !!},
            datasets: [{
                data: {!! json_encode($fileTypesData->values()->toArray()) !!},
                backgroundColor: {!! json_encode($fileTypesData->keys()->map(fn($type) => getFileTypeColor($type))) !!},
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 25
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} files (${percentage}%)`;
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 12
                    },
                    formatter: (value) => value
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 2000
            }
        }
    });

    // Monthly Activity Chart
    const monthlyActivityCtx = document.getElementById('monthlyActivityChart').getContext('2d');
    
    // Generate sample monthly data (in real app, this should come from backend)
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const userActivity = Array.from({length: 12}, () => Math.floor(Math.random() * 50) + 20);
    const archiveActivity = Array.from({length: 12}, () => Math.floor(Math.random() * 100) + 30);

    const monthlyActivityChart = new Chart(monthlyActivityCtx, {
        type: 'line',
        data: {
            labels: months.slice(0, 6), // Last 6 months
            datasets: [
                {
                    label: 'New Users',
                    data: userActivity.slice(0, 6),
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4361ee',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                },
                {
                    label: 'New Archives',
                    data: archiveActivity.slice(0, 6),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Animate progress rings
    document.addEventListener('DOMContentLoaded', function() {
        const circles = document.querySelectorAll('.progress-ring__circle');
        circles.forEach(circle => {
            const radius = circle.r.baseVal.value;
            const circumference = radius * 2 * Math.PI;
            const offset = circumference - (75 / 100) * circumference;
            circle.style.strokeDasharray = `${circumference} ${circumference}`;
            circle.style.strokeDashoffset = circumference;
            
            // Animate after a delay
            setTimeout(() => {
                circle.style.transition = 'stroke-dashoffset 1.5s ease-in-out';
                circle.style.strokeDashoffset = offset;
            }, 500);
        });
    });

    // Filter form enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form[method="GET"]');
        const filterInputs = filterForm.querySelectorAll('select, input');
        
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Optional: Add loading state
                filterForm.submit();
            });
        });
    });
</script>

@php
    // Helper function for colors in blade
    function getChartColor($index) {
        $colors = ['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0', '#4895ef', '#560bad'];
        return $colors[$index % count($colors)];
    }

    function getFileTypeColor($type) {
        $colors = [
            'PDF' => '#ef4444',
            'DOC' => '#3b82f6',
            'EXCEL' => '#10b981',
            'IMAGE' => '#f59e0b',
            'OTHER' => '#6b7280'
        ];
        return $colors[$type] ?? '#6b7280';
    }
    
    // Format bytes helper function
    if (!function_exists('formatBytes')) {
        function formatBytes($bytes, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            
            $bytes /= pow(1024, $pow);
            
            return round($bytes, $precision) . ' ' . $units[$pow];
        }
    }
@endphp
@endsection