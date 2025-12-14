@extends('layouts.admin')

@section('title', ($user->name_decrypted ?? $user->name) . ' - User Profile')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ $user->name_decrypted ?? $user->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gradient text-primary">User Profile</h1>
            <p class="text-muted mb-0">View user details, documents, and activity logs</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - User Info -->
        <div class="col-lg-4 mb-4">
            <!-- Profile Card -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <!-- Profile Picture -->
                        <div class="position-relative me-3">
                            <div class="avatar avatar-xl bg-white rounded-circle overflow-hidden border-3 border-white shadow">
                                @if($user->profile_picture)
                                    <img src="{{ Storage::url($user->profile_picture) }}" 
                                         alt="{{ $user->name_decrypted ?? $user->name }}" 
                                         class="w-100 h-100 object-fit-cover"
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name_decrypted ?? $user->name) }}&background={{ [
                                            'admin' => 'dc3545',
                                            'dean' => '0dcaf0',
                                            'chairperson' => 'ffc107',
                                            'faculty' => '198754'
                                         ][$user->role] ?? '6c757d' }}&color=fff&size=200'">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center" 
                                         style="background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));">
                                        <span class="text-white fs-2 fw-bold">
                                            {{ strtoupper(substr($user->name_decrypted ?? $user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            @if($user->email_verified_at)
                            <div class="position-absolute bottom-0 end-0">
                                <div class="avatar avatar-sm bg-success rounded-circle border-2 border-white d-flex align-items-center justify-content-center" 
                                     title="Email Verified">
                                    <i class="fas fa-check text-white" style="font-size: 0.7rem;"></i>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $user->name_decrypted ?? $user->name }}</h4>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-at me-1"></i>
                                {{ $user->username_decrypted ?? $user->username }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Contact Info -->
                    <div class="contact-info mb-4">
                        @if($user->email_decrypted ?? $user->email)
                        <div class="d-flex align-items-center mb-3">
                            <div class="contact-icon bg-light-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <strong>{{ $user->email_decrypted ?? $user->email }}</strong>
                            </div>
                        </div>
                        @endif

                        @if($user->address)
                        <div class="d-flex align-items-center mb-3">
                            <div class="contact-icon bg-light-info rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="fas fa-map-marker-alt text-info"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Address</small>
                                <strong class="text-truncate" style="max-width: 200px;">{{ $user->address }}</strong>
                            </div>
                        </div>
                        @endif

                        @if($user->description)
                        <div class="d-flex align-items-start mb-3">
                            <div class="contact-icon bg-light-success rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="fas fa-info-circle text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Description</small>
                                <p class="mb-0">{{ Str::limit($user->description, 100) }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Account Status -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Account Status</h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-{{ [
                                'admin' => 'danger',
                                'dean' => 'info',
                                'chairperson' => 'warning',
                                'faculty' => 'success'
                            ][$user->role] ?? 'secondary' }} px-3 py-2 rounded-pill shadow-sm">
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
                            ][$user->status] ?? 'secondary' }} px-3 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-{{ [
                                    'ACTIVE' => 'check-circle',
                                    'INACTIVE' => 'pause-circle',
                                    'SUSPENDED' => 'ban'
                                ][$user->status] ?? 'circle' }} me-2"></i>
                                {{ $user->status }}
                            </span>
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="bg-light rounded p-4 mb-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-item">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-graduation-cap me-1"></i>Program
                                    </small>
                                    <strong class="d-block mt-1">{{ $user->program_decrypted ?? $user->program ?? 'N/A' }}</strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar-plus me-1"></i>Joined Date
                                    </small>
                                    <strong class="d-block mt-1">{{ $user->created_at->format('F d, Y') }}</strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock me-1"></i>Last Updated
                                    </small>
                                    <strong class="d-block mt-1">{{ $user->updated_at->diffForHumans() }}</strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-envelope-check me-1"></i>Email Status
                                    </small>
                                    <strong class="d-block mt-1">
                                        @if($user->email_verified_at)
                                            <span class="text-success">Verified</span>
                                        @else
                                            <span class="text-warning">Not Verified</span>
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="d-grid gap-2">
                        @if($user->email_decrypted ?? $user->email)
                        <a href="mailto:{{ $user->email_decrypted ?? $user->email }}" class="btn btn-outline-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Email
                        </a>
                        @endif
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $user->id }})">
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </div>
                </div>
            </div>

            <!-- Activity Stats -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Activity Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="stat-card p-3 bg-primary text-white rounded shadow-sm">
                                <h3 class="mb-1">{{ $documents->total() }}</h3>
                                <small>Documents</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card p-3 bg-info text-white rounded shadow-sm">
                                <h3 class="mb-1">{{ $logs->count() }}</h3>
                                <small>Activities</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card p-3 bg-success text-white rounded shadow-sm">
                                <h3 class="mb-1">{{ $documents->where('created_at', '>=', now()->subDays(30))->count() }}</h3>
                                <small>Last 30 Days</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card p-3 bg-warning text-white rounded shadow-sm">
                                <h3 class="mb-1">{{ number_format($documents->avg('file_size') / 1024, 1) }} KB</h3>
                                <small>Avg. File Size</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Documents & Activity -->
        <div class="col-lg-8">
            <!-- Documents Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-folder me-2"></i>Archived Documents</h6>
                    <div>
                        <span class="badge bg-primary">{{ $documents->total() }} files</span>
                        <span class="badge bg-secondary ms-1">{{ number_format($documents->sum('file_size') / (1024*1024), 2) }} MB total</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form action="{{ route('admin.users.show', $user) }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-filter text-muted"></i>
                                </span>
                                <select name="category" class="form-select" onchange="this.form.submit()">
                                    <option value="all" {{ $category == 'all' ? 'selected' : '' }}>All Categories</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="doc_search" 
                                       class="form-control" 
                                       placeholder="Search documents..."
                                       value="{{ $search }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                                @if(request()->has('category') || request()->has('doc_search') || request()->has('sort'))
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Sort Tabs - FIXED -->
                    <div class="d-flex gap-2 mb-3">
                        <a href="{{ route('admin.users.show', array_merge(['user' => $user], request()->except(['sort', 'direction']), ['sort' => 'created_at', 'direction' => 'desc'])) }}" 
                           class="btn btn-sm {{ $sort == 'created_at' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="fas fa-clock me-1"></i>Newest
                        </a>
                        <a href="{{ route('admin.users.show', array_merge(['user' => $user], request()->except(['sort', 'direction']), ['sort' => 'original_name', 'direction' => 'asc'])) }}" 
                           class="btn btn-sm {{ $sort == 'original_name' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="fas fa-sort-alpha-down me-1"></i>Name (A-Z)
                        </a>
                        <a href="{{ route('admin.users.show', array_merge(['user' => $user], request()->except(['sort', 'direction']), ['sort' => 'file_size', 'direction' => 'desc'])) }}" 
                           class="btn btn-sm {{ $sort == 'file_size' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="fas fa-weight me-1"></i>Largest
                        </a>
                    </div>

                    <!-- Documents Table -->
                    @if($documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">File</th>
                                    <th>Category</th>
                                    <th>Size</th>
                                    <th>Uploaded</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $doc)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon me-3">
                                                <i class="fas fa-2x {{ [
                                                    'pdf' => 'fa-file-pdf text-danger',
                                                    'doc' => 'fa-file-word text-primary',
                                                    'docx' => 'fa-file-word text-primary',
                                                    'xls' => 'fa-file-excel text-success',
                                                    'xlsx' => 'fa-file-excel text-success',
                                                    'ppt' => 'fa-file-powerpoint text-warning',
                                                    'pptx' => 'fa-file-powerpoint text-warning',
                                                    'jpg' => 'fa-file-image text-info',
                                                    'jpeg' => 'fa-file-image text-info',
                                                    'png' => 'fa-file-image text-info',
                                                ][pathinfo($doc->original_name, PATHINFO_EXTENSION)] ?? 'fa-file text-secondary' }}"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-truncate" style="max-width: 250px;" title="{{ $doc->original_name }}">
                                                    {{ $doc->original_name }}
                                                </strong>
                                                <small class="text-muted">{{ strtoupper($doc->file_type) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $doc->category_id }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            @if($doc->file_size < 1024)
                                                {{ $doc->file_size }} B
                                            @elseif($doc->file_size < 1024*1024)
                                                {{ number_format($doc->file_size / 1024, 1) }} KB
                                            @else
                                                {{ number_format($doc->file_size / (1024*1024), 1) }} MB
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="text-dark">{{ $doc->created_at->format('M d, Y') }}</div>
                                            <div class="text-muted">{{ $doc->created_at->diffForHumans() }}</div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ Storage::url($doc->file_path) }}" 
                                               target="_blank" 
                                               class="btn btn-outline-primary" 
                                               title="View"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ Storage::url($doc->file_path) }}" 
                                               download="{{ $doc->original_name }}" 
                                               class="btn btn-outline-success" 
                                               title="Download"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if(auth()->user()->role == 'admin')
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete"
                                                    data-bs-toggle="tooltip"
                                                    onclick="confirmDeleteDocument({{ $doc->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($documents->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $documents->firstItem() }} to {{ $documents->lastItem() }} of {{ $documents->total() }} documents
                        </div>
                        <nav aria-label="Page navigation">
                            {{ $documents->withQueryString()->links() }}
                        </nav>
                    </div>
                    @endif
                    
                    @else
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <h5>No documents found</h5>
                        <p class="text-muted">
                            @if(request()->has('category') || request()->has('doc_search'))
                                Try adjusting your search or filter criteria
                            @else
                                This user hasn't uploaded any documents yet.
                            @endif
                        </p>
                        @if(request()->has('category') || request()->has('doc_search'))
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary mt-2">
                                <i class="fas fa-times me-1"></i> Clear Filters
                            </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Activity Logs Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h6>
                    <span class="badge bg-info">{{ $logs->count() }} activities</span>
                </div>
                <div class="card-body p-0">
                    <div class="activity-timeline">
                        @forelse($logs as $log)
                        <div class="activity-item border-bottom p-4">
                            <div class="d-flex">
                                <div class="activity-icon me-3">
                                    <div class="avatar avatar-sm bg-{{ [
                                        'login' => 'success',
                                        'logout' => 'warning',
                                        'upload' => 'primary',
                                        'download' => 'info',
                                        'create' => 'success',
                                        'update' => 'warning',
                                        'delete' => 'danger',
                                    ][$log->action_type] ?? 'secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                                        <i class="fas fa-{{ [
                                            'login' => 'sign-in-alt',
                                            'logout' => 'sign-out-alt',
                                            'upload' => 'upload',
                                            'download' => 'download',
                                            'create' => 'plus-circle',
                                            'update' => 'edit',
                                            'delete' => 'trash',
                                        ][$log->action_type] ?? 'bell' }}"></i>
                                    </div>
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0">{{ $log->description }}</h6>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="text-muted small">
                                        <div class="d-flex gap-3">
                                            <span><i class="fas fa-globe me-1"></i> IP: {{ $log->ip_address }}</span>
                                            <span><i class="fas fa-calendar me-1"></i> {{ $log->created_at->format('M d, Y h:i A') }}</span>
                                        </div>
                                        @if($log->details)
                                        <div class="mt-1">
                                            <strong>Details:</strong> {{ $log->details }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5>No activity logs</h5>
                            <p class="text-muted">This user hasn't performed any activities yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this user? This action cannot be undone.</p>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Warning:</strong> This will permanently remove the user from the system.
                    @if($documents->total() > 0)
                    <div class="mt-2">
                        <i class="fas fa-folder me-1"></i> This user has {{ $documents->total() }} archived documents.
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <form id="deleteForm" method="POST" action="{{ route('admin.users.destroy', $user) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(userId) {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function confirmDeleteDocument(docId) {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        fetch(`/admin/documents/${docId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error deleting document');
            }
        });
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endpush

<style>
/* Gradient Text */
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Avatar Styles */
.avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.avatar-xl {
    width: 100px;
    height: 100px;
}

.avatar-sm {
    width: 25px;
    height: 25px;
}

.avatar.border-3 {
    border-width: 3px !important;
}

.avatar.shadow {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Profile Picture Container */
.position-relative .avatar-xl {
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

/* Contact Info */
.contact-icon {
    width: 40px;
    height: 40px;
}

.contact-info .d-flex {
    padding: 10px;
    border-radius: 10px;
    transition: all 0.3s;
}

.contact-info .d-flex:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(5px);
}

.bg-light-primary {
    background-color: rgba(102, 126, 234, 0.1) !important;
}

.bg-light-info {
    background-color: rgba(23, 162, 184, 0.1) !important;
}

.bg-light-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

/* Card Styles */
.card {
    border-radius: 15px;
    border: 1px solid #eef2f7;
}

.card-header.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px 15px 0 0 !important;
}

/* Info Items */
.info-item {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 3px solid #4e73df;
    transition: all 0.3s;
}

.info-item:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

/* Stats Cards */
.stat-card {
    transition: all 0.3s;
    border: 1px solid rgba(255,255,255,0.1);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

/* File Icons */
.file-icon {
    width: 40px;
    text-align: center;
}

/* Activity Items */
.activity-item {
    transition: background-color 0.2s;
}

.activity-item:hover {
    background-color: rgba(0,0,0,0.02);
}

.activity-item:last-child {
    border-bottom: none !important;
}

/* Empty States */
.empty-state {
    padding: 3rem 1rem;
}

/* Badges */
.badge {
    font-weight: 500;
}

.badge.shadow-sm {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Buttons */
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

/* Modal */
.modal-content {
    border-radius: 15px;
    border: none;
}

/* Object Fit */
.object-fit-cover {
    object-fit: cover;
}

/* Text Truncate */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive */
@media (max-width: 768px) {
    .avatar-xl {
        width: 80px;
        height: 80px;
    }
    
    .contact-info .d-flex {
        padding: 8px;
    }
    
    .contact-icon {
        width: 35px;
        height: 35px;
    }
}
</style>
@endsection