@extends('layouts.admin')

@section('page-title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h3 mb-2 text-gradient text-primary">User Management</h1>
            <p class="text-muted">Manage system users and their permissions</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i>Add New User
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('status', 'ACTIVE')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Faculty Members</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('role', 'faculty')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Administrators</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('role', 'admin')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-light border-bottom-0 py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Search & Filter Users
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0" 
                               placeholder="Search by name, username, email..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-user-tag text-primary"></i>
                        </span>
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="dean" {{ request('role') == 'dean' ? 'selected' : '' }}>Dean</option>
                            <option value="chairperson" {{ request('role') == 'chairperson' ? 'selected' : '' }}>Chairperson</option>
                            <option value="faculty" {{ request('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-graduation-cap text-primary"></i>
                        </span>
                        <select name="program" class="form-select">
                            <option value="">All Programs</option>
                            @foreach(['BSCS', 'BSIS', 'BINDTECH'] as $program)
                                <option value="{{ $program }}" {{ request('program') == $program ? 'selected' : '' }}>
                                    {{ $program }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        @if(request()->anyFilled(['search', 'role', 'program']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            
            <!-- Active Filters Badges -->
            @if(request()->anyFilled(['search', 'role', 'program']))
            <div class="mt-3">
                <small class="text-muted me-2">Active filters:</small>
                @if(request('search'))
                <span class="badge bg-primary me-2">
                    Search: "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white ms-2">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
                @if(request('role'))
                <span class="badge bg-info me-2">
                    Role: {{ ucfirst(request('role')) }}
                    <a href="{{ request()->fullUrlWithQuery(['role' => null]) }}" class="text-white ms-2">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
                @if(request('program'))
                <span class="badge bg-success">
                    Program: {{ request('program') }}
                    <a href="{{ request()->fullUrlWithQuery(['program' => null]) }}" class="text-white ms-2">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-light border-bottom-0 py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h6 class="m-0 font-weight-bold text-primary me-3">
                    <i class="fas fa-users me-2"></i>Users List
                </h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-sort me-1"></i>Sort By
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'asc']) }}">
                                <i class="fas fa-sort-alpha-down me-2"></i>Name (A-Z)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}">
                                <i class="fas fa-clock me-2"></i>Newest First
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => 'asc']) }}">
                                <i class="fas fa-circle me-2"></i>Status
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div>
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    <i class="fas fa-users me-1"></i>{{ $users->total() }} users
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-top-0 ps-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-dark text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-user me-2"></i>User
                                    @if(request('sort') == 'name')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-top-0">Role</th>
                            <th class="border-top-0">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-dark text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-circle me-2"></i>Status
                                    @if(request('sort') == 'status')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-top-0">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'program', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-dark text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-graduation-cap me-2"></i>Program
                                    @if(request('sort') == 'program')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-top-0">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-dark text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-calendar me-2"></i>Joined
                                    @if(request('sort') == 'created_at')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-top-0 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        @if($user->profile_picture)
                                            <div class="avatar avatar-sm rounded-circle overflow-hidden border-2 border-white shadow-sm">
                                                <img src="{{ Storage::url($user->profile_picture) }}" 
                                                     alt="{{ $user->name_decrypted ?? $user->name }}" 
                                                     class="w-100 h-100 object-fit-cover"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name_decrypted ?? $user->name) }}&background={{ [
                                                        'admin' => 'dc3545',
                                                        'dean' => '0dcaf0',
                                                        'chairperson' => 'ffc107',
                                                        'faculty' => '198754'
                                                     ][$user->role] ?? '6c757d' }}&color=fff&size=100'">
                                            </div>
                                        @else
                                            <div class="avatar avatar-sm bg-{{ [
                                                'admin' => 'danger',
                                                'dean' => 'info',
                                                'chairperson' => 'warning',
                                                'faculty' => 'primary'
                                            ][$user->role] ?? 'secondary' }} rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                                                <span class="text-white fw-bold">
                                                    {{ strtoupper(substr($user->name_decrypted ?? $user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        @if($user->email_verified_at)
                                        <div class="position-absolute bottom-0 end-0">
                                            <div class="avatar avatar-xs bg-success rounded-circle border-2 border-white d-flex align-items-center justify-content-center" 
                                                 title="Email Verified">
                                                <i class="fas fa-check text-white" style="font-size: 0.5rem;"></i>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $user->name_decrypted ?? $user->name }}</h6>
                                        <div class="text-muted small">
                                            <div><i class="fas fa-at me-1"></i>{{ $user->username_decrypted ?? $user->username }}</div>
                                            @if($user->email_decrypted ?? $user->email)
                                            <div><i class="fas fa-envelope me-1"></i>{{ Str::limit($user->email_decrypted ?? $user->email, 20) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ [
                                    'admin' => 'danger',
                                    'dean' => 'info',
                                    'chairperson' => 'warning',
                                    'faculty' => 'primary'
                                ][$user->role] ?? 'secondary' }} rounded-pill py-1 px-3 shadow-sm">
                                    <i class="fas fa-{{ [
                                        'admin' => 'user-shield',
                                        'dean' => 'user-graduate',
                                        'chairperson' => 'user-tie',
                                        'faculty' => 'chalkboard-teacher'
                                    ][$user->role] ?? 'user' }} me-1"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ [
                                    'ACTIVE' => 'success',
                                    'INACTIVE' => 'secondary',
                                    'SUSPENDED' => 'danger'
                                ][$user->status] ?? 'secondary' }} rounded-pill py-1 px-3 shadow-sm">
                                    <i class="fas fa-{{ [
                                        'ACTIVE' => 'check-circle',
                                        'INACTIVE' => 'pause-circle',
                                        'SUSPENDED' => 'ban'
                                    ][$user->status] ?? 'circle' }} me-1"></i>
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td>
                                @if($user->program_decrypted ?? $user->program)
                                <span class="badge bg-light text-dark border py-1 px-3 shadow-sm">
                                    <i class="fas fa-graduation-cap me-1 text-primary"></i>
                                    {{ $user->program_decrypted ?? $user->program }}
                                </span>
                                @else
                                <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    <div class="text-dark">{{ $user->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted">{{ $user->created_at->diffForHumans() }}</div>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex flex-column gap-2">
                                    <div class="btn-group btn-group-sm shadow-sm" role="group">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="btn btn-primary rounded-start d-flex align-items-center justify-content-center"
                                           title="View Profile"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-info d-flex align-items-center justify-content-center"
                                           title="Edit User"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->email_decrypted ?? $user->email)
                                        <a href="mailto:{{ $user->email_decrypted ?? $user->email }}" 
                                           class="btn btn-warning d-flex align-items-center justify-content-center"
                                           title="Send Email"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        @endif
                                        <button type="button" 
                                                class="btn btn-danger rounded-end d-flex align-items-center justify-content-center"
                                                title="Delete User"
                                                data-bs-toggle="tooltip"
                                                onclick="confirmDelete({{ $user->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @if($user->description)
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary w-100 d-flex align-items-center justify-content-center"
                                            onclick="showDescription('{{ addslashes($user->description) }}')">
                                        <i class="fas fa-info-circle me-1"></i> View Bio
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>No users found</h5>
                                    <p class="text-muted">
                                        @if(request()->anyFilled(['search', 'role', 'program']))
                                            Try adjusting your search or filter criteria
                                        @else
                                            Start by adding a new user
                                        @endif
                                    </p>
                                    @if(request()->anyFilled(['search', 'role', 'program']))
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-times me-1"></i> Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer border-top-0 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                <nav aria-label="Page navigation">
                    {{ $users->withQueryString()->links() }}
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <form id="deleteForm" method="POST">
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

<!-- Description Modal -->
<div class="modal fade" id="descriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2 text-info"></i>User Bio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="descriptionContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(userId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/users/${userId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function showDescription(description) {
    document.getElementById('descriptionContent').textContent = description;
    new bootstrap.Modal(document.getElementById('descriptionModal')).show();
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Auto-submit filters on change
    const roleSelect = document.querySelector('select[name="role"]');
    const programSelect = document.querySelector('select[name="program"]');
    
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
@endpush

<style>
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.avatar-sm {
    width: 40px;
    height: 40px;
}

.avatar-xs {
    width: 15px;
    height: 15px;
}

.avatar.border-2 {
    border-width: 2px !important;
}

.avatar.shadow-sm {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.empty-state {
    padding: 3rem 0;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.02);
    transition: background-color 0.2s;
}

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.btn-group .btn {
    padding: 0.375rem 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
}

.btn-group.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    min-width: 32px;
}

.btn-group .btn:first-child {
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

.btn-group .btn:last-child {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}

.btn-group.shadow-sm {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1) !important;
}

.badge.shadow-sm {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

th a {
    transition: color 0.2s;
}

th a:hover {
    color: #4e73df !important;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.position-relative {
    position: relative;
}

.position-absolute {
    position: absolute;
}

.bottom-0 {
    bottom: 0;
}

.end-0 {
    right: 0;
}

.object-fit-cover {
    object-fit: cover;
}

/* Hover effects for table rows */
.table-hover tbody tr {
    transition: all 0.2s;
}

.table-hover tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

/* Filter badges remove button */
.badge a {
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.badge a:hover {
    opacity: 1;
}

/* Dropdown styling */
.dropdown-toggle::after {
    margin-left: 0.5em;
}
</style>
@endsection