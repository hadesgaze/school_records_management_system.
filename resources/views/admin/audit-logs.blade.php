@extends('layouts.admin')
@section('page-title', 'Audit Logs')

@section('content')
<style>
.cell-trunc { 
    max-width: 250px; 
    white-space: nowrap; 
    overflow: hidden; 
    text-overflow: ellipsis; 
    display: inline-block;
    vertical-align: middle;
}
.cell-trunc-sm { 
    max-width: 150px; 
    white-space: nowrap; 
    overflow: hidden; 
    text-overflow: ellipsis; 
    display: inline-block;
    vertical-align: middle;
}
.user-avatar { 
    width: 40px; 
    height: 40px; 
    object-fit: cover; 
    border-radius: 50%; 
    border: 2px solid #fff; 
    box-shadow: 0 2px 8px rgba(0,0,0,.1); 
}
.hover-row:hover { 
    background-color: #f8f9fa !important; 
    transition: background-color .2s ease; 
}
.badge-module { 
    background-color: #e7f3ff; 
    color: #0066cc;
    border: 1px solid #b3d9ff;
    padding: 6px 12px;
    font-weight: 500;
    font-size: 0.875rem;
}
.table thead th { 
    font-weight: 600; 
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}
.search-form {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 4px;
}
.search-form input {
    border: none;
    background: transparent;
}
.search-form input:focus {
    box-shadow: none;
}
.card {
    border-radius: 12px;
    overflow: hidden;
}
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}
.page-header h3 {
    margin: 0;
    font-weight: 700;
}
.btn-delete {
    padding: 6px 16px;
    font-size: 0.875rem;
    font-weight: 500;
    background-color: #fee;
    border-color: #fcc;
    color: #d33;
}
.btn-delete:hover {
    background-color: #fdd;
    border-color: #f99;
    color: #b00;
}
.empty-state {
    padding: 4rem 2rem;
}
.empty-state i {
    font-size: 4rem;
    opacity: 0.3;
}
.delete-confirm-modal .modal-header {
    background-color: #f8d7da;
    border-bottom: 1px solid #f5c6cb;
    color: #721c24;
}
.delete-confirm-modal .modal-title {
    font-weight: 600;
}
</style>

<div class="container-fluid py-4">
    {{-- 🧭 Page Header --}}
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>
                    <i class="bi bi-shield-check me-2"></i> Audit Logs
                </h3>
                <p class="mb-0 opacity-75" style="font-size: 0.95rem;">Track all system activities and user actions</p>
            </div>
            <form method="GET" class="search-form" style="width: 400px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-search ms-3 me-2 text-muted"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           class="form-control form-control-sm flex-grow-1" 
                           placeholder="Search by user, action, module, or IP..."
                           style="border: none;">
                    <button class="btn btn-sm btn-primary rounded" type="submit" title="Search">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.audit-logs') }}" 
                           class="btn btn-sm btn-light rounded ms-1" 
                           title="Clear search">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- 📋 Audit Logs Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-secondary">
                            <th scope="col" style="padding: 1.25rem 1.5rem;">
                                <i class="bi bi-person-circle me-1"></i> User
                            </th>
                            <th scope="col" style="padding: 1.25rem 1rem;">
                                <i class="bi bi-activity me-1"></i> Action
                            </th>
                            <th scope="col" style="padding: 1.25rem 1rem;">
                                <i class="bi bi-grid-3x3-gap me-1"></i> Module
                            </th>
                            <th scope="col" style="padding: 1.25rem 1rem;">
                                <i class="bi bi-globe me-1"></i> IP Address
                            </th>
                            <th scope="col" style="padding: 1.25rem 1rem;">
                                <i class="bi bi-calendar3 me-1"></i> Timestamp
                            </th>
                            <th scope="col" class="text-center" style="padding: 1.25rem 1.5rem;">
                                <i class="bi bi-gear-fill me-1"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $user = $log->user;
                                $name = $user->decrypted_name ?? 'System';
                                $action = $log->action ?? 'Unknown Action';
                                $module = $log->module ?? 'Unknown';
                                $ip = $log->ip_address ?: '—';

                                // Profile photo
                                $photoPath = $user->profile_picture ?? null;
                                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                                    $photoUrl = asset('storage/'.$photoPath);
                                } else {
                                    $photoUrl = 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=667eea&color=fff&size=128&bold=true';
                                }
                            @endphp
                            <tr class="hover-row">
                                <td style="padding: 1rem 1.5rem;">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $photoUrl }}" alt="{{ $name }}" class="user-avatar me-3">
                                        <div>
                                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">
                                                <span class="cell-trunc" title="{{ $name }}">{{ $name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="cell-trunc" title="{{ $action }}">{{ $action }}</span>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-module rounded-pill">{{ $module }}</span>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="text-muted cell-trunc-sm" title="{{ $ip }}">
                                        <i class="bi bi-hdd-network me-1"></i>{{ $ip }}
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div class="text-muted" style="font-size: 0.875rem;">
                                        <div>{{ $log->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td class="text-center" style="padding: 1rem 1.5rem;">
                                    <button type="button" 
                                            class="btn btn-sm btn-delete rounded-pill"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal"
                                            data-log-id="{{ $log->id }}"
                                            data-log-action="{{ $action }}"
                                            data-log-module="{{ $module }}"
                                            data-log-user="{{ $name }}">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center empty-state">
                                    <i class="bi bi-inbox d-block mb-3 text-muted"></i>
                                    <h5 class="text-muted mb-2">No Audit Logs Found</h5>
                                    <p class="text-muted mb-0">
                                        @if(request('search'))
                                            No logs match your search criteria. Try different keywords.
                                        @else
                                            There are no audit logs to display yet.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 📌 Pagination --}}
            @if($logs->hasPages())
                <div class="p-4 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                        </div>
                        <div>
                            {{ $logs->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 🗑️ Delete Confirmation Modal --}}
<div class="modal fade delete-confirm-modal" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>Warning:</strong> Deleting audit logs is permanent and cannot be undone.
                </div>
                <p>Are you sure you want to delete this audit log?</p>
                <div class="bg-light p-3 rounded small">
                    <div class="row mb-2">
                        <div class="col-4 fw-semibold">User:</div>
                        <div class="col-8" id="modal-user">—</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 fw-semibold">Action:</div>
                        <div class="col-8" id="modal-action">—</div>
                    </div>
                    <div class="row">
                        <div class="col-4 fw-semibold">Module:</div>
                        <div class="col-8" id="modal-module">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const logId = button.getAttribute('data-log-id');
            const logAction = button.getAttribute('data-log-action');
            const logModule = button.getAttribute('data-log-module');
            const logUser = button.getAttribute('data-log-user');
            
            // Update modal content
            document.getElementById('modal-user').textContent = logUser || '—';
            document.getElementById('modal-action').textContent = logAction || '—';
            document.getElementById('modal-module').textContent = logModule || '—';
            
            // Update form action
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = "{{ route('admin.audit-logs.destroy', '') }}/" + logId;
            
            // Store log ID for reference
            deleteForm.setAttribute('data-log-id', logId);
        });
    }
    
    // Handle form submission
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const logId = this.getAttribute('data-log-id');
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Deleting...';
            submitBtn.disabled = true;
            
            // Submit the form via AJAX
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ _method: 'DELETE' })
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok');
            })
            .then(data => {
                // Close modal
                const modal = bootstrap.Modal.getInstance(deleteModal);
                modal.hide();
                
                // Show success message
                showToast('success', 'Audit log deleted successfully');
                
                // Remove the row from table
                const row = document.querySelector(`button[data-log-id="${logId}"]`).closest('tr');
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-100%)';
                
                setTimeout(() => {
                    row.remove();
                    
                    // Check if table is empty
                    const tbody = document.querySelector('tbody');
                    if (tbody.children.length === 0 || (tbody.children.length === 1 && tbody.children[0].querySelector('.empty-state'))) {
                        // Reload page to show empty state
                        location.reload();
                    }
                }, 300);
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to delete audit log. Please try again.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Toast notification function
    function showToast(type, message) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.right = '20px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.getElementById('toast-container').appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    }
});
</script>
@endpush
@endsection