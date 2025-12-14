@extends('layouts.admin')
@section('page-title', 'Categories Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Categories</h1>
            <p class="mb-0">Manage document categories and their access permissions</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm"></i> Add New Category
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Categories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $categories->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Categories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCategoriesCount ?? 'N/A' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Categories List</h6>
                <div class="d-flex">
                    <form method="GET" class="form-inline mr-2">
                        <div class="input-group">
                            <input type="text" class="form-control border-primary" 
                                   name="search" placeholder="Search categories..." 
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn-outline-primary" type="button" data-toggle="collapse" 
                            data-target="#filterCollapse" aria-expanded="false" 
                            aria-controls="filterCollapse">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
            
            <!-- Filter Collapse -->
            <div class="collapse mt-3" id="filterCollapse">
                <form method="GET" class="row">
                    <div class="col-md-4 mb-2">
                        <label class="small">Sort By</label>
                        <select name="sort" class="form-control form-control-sm">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small">Access Role</label>
                        <select name="role" class="form-control form-control-sm">
                            <option value="">All Roles</option>
                            <option value="faculty" {{ request('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="dean" {{ request('role') == 'dean' ? 'selected' : '' }}>Dean</option>
                            <option value="chairperson" {{ request('role') == 'chairperson' ? 'selected' : '' }}>Chairperson</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm mr-2">Apply</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="card-body">
            @if($categories->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No categories found</h5>
                    @if(request()->hasAny(['search', 'role', 'sort']))
                        <p class="text-gray-500">Try adjusting your filters</p>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-primary mt-2">
                            Clear Filters
                        </a>
                    @else
                        <p class="text-gray-500">Get started by creating your first category</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> Create Category
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="categoriesTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'name_asc' ? 'name_desc' : 'name_asc']) }}" 
                                       class="text-dark text-decoration-none">
                                        Name
                                        @if(request('sort') == 'name_asc')
                                            <i class="fas fa-sort-up ml-1"></i>
                                        @elseif(request('sort') == 'name_desc')
                                            <i class="fas fa-sort-down ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="30%">Description</th>
                                <th width="20%">Accessible Roles</th>
                                <th width="15%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'newest' ? 'oldest' : 'newest']) }}" 
                                       class="text-dark text-decoration-none">
                                        Created At
                                        @if(request('sort') == 'newest')
                                            <i class="fas fa-sort-down ml-1"></i>
                                        @elseif(request('sort') == 'oldest')
                                            <i class="fas fa-sort-up ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $category)
                            <tr class="hover-shadow">
                                <td class="text-center">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="font-weight-bold text-primary">{{ $category->name }}</div>
                                    @if($category->slug)
                                        <small class="text-muted">/{{ $category->slug }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" 
                                         data-toggle="tooltip" 
                                         title="{{ $category->description ?? 'No description' }}">
                                        {{ $category->description ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $accessibleRoles = $category->getAccessibleRoles();
                                        $roleBadges = [
                                            'admin' => ['badge-light text-dark', 'Administrator'],
                                            'dean' => ['badge-warning text-dark', 'Dean'],
                                            'chairperson' => ['badge-info text-dark', 'Chairperson'],
                                            'faculty' => ['badge-light text-dark', 'Faculty']
                                        ];
                                    @endphp
                                    
                                    <div class="d-flex flex-wrap gap-1">
                                        @if(is_array($accessibleRoles) && count($accessibleRoles) > 0)
                                            @foreach($accessibleRoles as $role)
                                                @if(isset($roleBadges[$role]))
                                                    <span class="badge {{ $roleBadges[$role][0] }}">
                                                        {{ $roleBadges[$role][1] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="badge badge-secondary">No restrictions</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $category->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $category->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('admin.categories.edit', $category) }}" 
                                           class="btn btn-sm btn-outline-primary mr-2"
                                           data-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="#" 
                                           class="btn btn-sm btn-outline-info mr-2"
                                           data-toggle="modal" 
                                           data-target="#viewCategoryModal{{ $category->id }}"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.categories.destroy', $category) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-toggle="tooltip" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Modal for each category -->
                            <div class="modal fade" id="viewCategoryModal{{ $category->id }}" tabindex="-1" role="dialog" 
                                 aria-labelledby="viewCategoryModalLabel{{ $category->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="viewCategoryModalLabel{{ $category->id }}">
                                                <i class="fas fa-folder mr-2"></i>{{ $category->name }}
                                            </h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="row">
                                                <dt class="col-sm-4">Description:</dt>
                                                <dd class="col-sm-8">{{ $category->description ?? 'Not specified' }}</dd>
                                                
                                                <dt class="col-sm-4">Slug:</dt>
                                                <dd class="col-sm-8">{{ $category->slug ?? 'Not set' }}</dd>
                                                
                                                <dt class="col-sm-4">Accessible Roles:</dt>
                                                <dd class="col-sm-8">
                                                    @if(is_array($accessibleRoles) && count($accessibleRoles) > 0)
                                                        @foreach($accessibleRoles as $role)
                                                            <span class="badge {{ $roleBadges[$role][0] ?? 'badge-secondary' }} mb-1">
                                                                {{ $roleBadges[$role][1] ?? ucfirst($role) }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">No role restrictions</span>
                                                    @endif
                                                </dd>
                                                
                                                <dt class="col-sm-4">Created:</dt>
                                                <dd class="col-sm-8">{{ $category->created_at->format('F d, Y \a\t h:i A') }}</dd>
                                                
                                                <dt class="col-sm-4">Last Updated:</dt>
                                                <dd class="col-sm-8">{{ $category->updated_at->format('F d, Y \a\t h:i A') }}</dd>
                                            </dl>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="btn btn-primary">
                                                <i class="fas fa-edit mr-1"></i> Edit Category
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($categories->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} 
                            of {{ $categories->total() }} entries
                        </div>
                        <div>
                            {{ $categories->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkActionForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Select Action:</label>
                        <select name="bulk_action" class="form-control" required>
                            <option value="">Choose action...</option>
                            <option value="delete">Delete Selected</option>
                            <option value="change_role">Change Access Roles</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Selected Categories:</label>
                        <div id="selectedCategoriesList" class="small text-muted"></div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        This action cannot be undone.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkAction()">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-hide success message
    @if(session('success'))
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
    @endif

    // Initialize tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Enhanced delete confirmation
    function confirmDelete() {
        return confirm('Are you sure you want to delete this category?\n\n⚠️ This action cannot be undone.');
    }

    // Bulk actions functionality
    let selectedCategories = [];

    function toggleCategorySelection(categoryId) {
        const index = selectedCategories.indexOf(categoryId);
        if (index === -1) {
            selectedCategories.push(categoryId);
        } else {
            selectedCategories.splice(index, 1);
        }
        updateBulkActionButton();
    }

    function updateBulkActionButton() {
        const btn = $('#bulkActionBtn');
        if (selectedCategories.length > 0) {
            btn.text(`Bulk Actions (${selectedCategories.length})`).removeClass('disabled');
        } else {
            btn.text('Bulk Actions').addClass('disabled');
        }
    }

    function openBulkActions() {
        if (selectedCategories.length === 0) return;
        
        $('#selectedCategoriesList').text(
            selectedCategories.length + ' category(ies) selected'
        );
        $('#bulkActionsModal').modal('show');
    }

    function submitBulkAction() {
        const action = $('select[name="bulk_action"]').val();
        if (!action) {
            alert('Please select an action');
            return;
        }
        
        if (confirm('Are you sure you want to perform this bulk action?')) {
            $('#bulkActionForm').submit();
        }
    }

    // Export functionality
    function exportCategories(format) {
        const url = new URL(window.location.href);
        url.searchParams.set('export', format);
        window.location.href = url.toString();
    }

    // Sort table on column click
    function sortTable(column, direction) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', column);
        url.searchParams.set('order', direction);
        window.location.href = url.toString();
    }
</script>

<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {
        $('#categoriesTable').DataTable({
            pageLength: 25,
            responsive: true,
            order: [],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search categories...",
                emptyTable: "No categories available",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    });
</script>
@endsection

@section('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: box-shadow 0.2s ease-in-out;
    }
    
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }
    
    .btn-group-sm > .btn, .btn-sm {
        border-radius: 0.2rem;
    }
</style>
@endsection