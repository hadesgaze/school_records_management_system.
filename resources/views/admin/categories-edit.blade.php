@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-folder text-primary mr-2"></i>Edit Category: {{ $category->name }}
        </h1>
        <a href="{{ route('admin.categories.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Back to Categories
        </a>
    </div>

    <!-- Flash Messages -->
    <div class="row mb-4">
        <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Edit Form -->
        <div class="col-lg-8">
            <!-- Main Card -->
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-edit mr-1"></i>Edit Category Information
                        </h6>
                        <span class="badge badge-light">ID: {{ $category->id }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" id="categoryForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Category Name -->
                        <div class="form-group mb-4">
                            <label for="name" class="font-weight-bold text-dark">
                                <i class="fas fa-tag text-primary mr-1"></i>Category Name *
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-heading text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $category->name) }}"
                                       placeholder="Enter category name"
                                       required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block mt-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-4">
                            <label for="description" class="font-weight-bold text-dark">
                                <i class="fas fa-align-left text-primary mr-1"></i>Description
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light align-items-start">
                                        <i class="fas fa-file-alt text-muted mt-1"></i>
                                    </span>
                                </div>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Enter category description">{{ old('description', $category->description) }}</textarea>
                            </div>
                            @error('description')
                                <div class="invalid-feedback d-block mt-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Accessible Roles -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-user-shield text-primary mr-1"></i>Accessible Roles *
                            </label>
                            <div class="alert alert-info border-left-info border-3 py-2 mb-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                <small>Select roles that can access and manage this category</small>
                            </div>
                            
                            <div class="row">
                                @php
                                    $selectedRoles = old('accessible_roles', $category->accessible_roles_array ?? []);
                                @endphp
                                
                                @foreach($roles as $key => $label)
                                <div class="col-md-6 mb-3">
                                    <div class="card role-card h-100 border-left-4 
                                        {{ in_array($key, $selectedRoles) ? 'border-left-primary' : 'border-left-light' }} 
                                        shadow-sm hover-shadow">
                                        <div class="card-body p-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" 
                                                       class="custom-control-input role-checkbox" 
                                                       id="role_{{ $key }}" 
                                                       name="accessible_roles[]" 
                                                       value="{{ $key }}"
                                                       {{ in_array($key, $selectedRoles) ? 'checked' : '' }}>
                                                <label class="custom-control-label d-flex align-items-center" for="role_{{ $key }}">
                                                    <span class="role-icon mr-3">
                                                        @switch($key)
                                                            @case('administrator')
                                                                <i class="fas fa-crown text-warning fa-lg"></i>
                                                                @break
                                                            @case('dean')
                                                                <i class="fas fa-user-graduate text-success fa-lg"></i>
                                                                @break
                                                            @case('chairperson')
                                                                <i class="fas fa-user-tie text-info fa-lg"></i>
                                                                @break
                                                            @case('faculty')
                                                                <i class="fas fa-chalkboard-teacher text-primary fa-lg"></i>
                                                                @break
                                                        @endswitch
                                                    </span>
                                                    <div>
                                                        <strong class="d-block">{{ $label }}</strong>
                                                        <small class="text-muted">
                                                            @switch($key)
                                                                @case('administrator')
                                                                    Full system access and control
                                                                    @break
                                                                @case('dean')
                                                                    College-level management access
                                                                    @break
                                                                @case('chairperson')
                                                                    Department-level management access
                                                                    @break
                                                                @case('faculty')
                                                                    Faculty member access
                                                                    @break
                                                            @endswitch
                                                        </small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            @error('accessible_roles')
                                <div class="alert alert-danger border-left-danger border-3 py-2 mt-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group pt-3 border-top">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                                        <i class="fas fa-save mr-1"></i> Update Category
                                    </button>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                        Cancel
                                    </a>
                                </div>
                                <button type="button" class="btn btn-danger btn-lg px-4 shadow-sm" 
                                        onclick="confirmDelete()">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Delete Form -->
                    <form id="delete-form" 
                          action="{{ route('admin.categories.destroy', $category) }}" 
                          method="POST" 
                          style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
            
            <!-- Category Fields Section -->
            <div class="card shadow-lg border-0 rounded-lg mt-4">
                <div class="card-header bg-gradient-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-list-alt mr-1"></i>Category Fields
                        </h6>
                        <button type="button" class="btn btn-light btn-sm shadow-sm" id="toggleFieldForm">
                            <i class="fas fa-plus mr-1"></i> Add New Field
                        </button>
                    </div>
                </div>
                
                <!-- Add/Edit Field Form (Initially Hidden) -->
                <div class="card-body border-bottom" id="fieldFormContainer" style="display: none;">
                    <form id="fieldForm" method="POST">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                        <input type="hidden" id="field_id" name="field_id">
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="field_name" class="font-weight-bold text-dark">
                                        Field Name *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('field_name') is-invalid @enderror" 
                                           id="field_name" 
                                           name="field_name" 
                                           placeholder="Enter field name"
                                           required>
                                    @error('field_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="field_type" class="font-weight-bold text-dark">
                                        Field Type *
                                    </label>
                                    <select class="form-control @error('field_type') is-invalid @enderror" 
                                            id="field_type" 
                                            name="field_type"
                                            required>
                                        <option value="">Select field type</option>
                                        <option value="text">Text</option>
                                        <option value="textarea">Text Area</option>
                                        <option value="number">Number</option>
                                        <option value="email">Email</option>
                                        <option value="date">Date</option>
                                        <option value="select">Dropdown</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="radio">Radio Button</option>
                                        <option value="file">File Upload</option>
                                    </select>
                                    @error('field_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="field_description" class="font-weight-bold text-dark">
                                        Description (Optional)
                                    </label>
                                    <textarea class="form-control" 
                                              id="field_description" 
                                              name="field_description" 
                                              rows="2"
                                              placeholder="Enter field description"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="options" class="font-weight-bold text-dark" id="optionsLabel" style="display: none;">
                                        Options (comma-separated)
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="options" 
                                           name="options" 
                                           style="display: none;"
                                           placeholder="Option 1, Option 2, Option 3">
                                    <small class="text-muted" id="optionsHelp" style="display: none;">
                                        Required for dropdown, checkbox group, and radio button types
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="field_order" class="font-weight-bold text-dark">
                                        Display Order
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="field_order" 
                                           name="field_order" 
                                           value="0"
                                           min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_required" 
                                               name="is_required" 
                                               value="1">
                                        <label class="custom-control-label font-weight-bold text-dark" for="is_required">
                                            Required Field
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary" id="fieldSubmitBtn">
                                    <i class="fas fa-save mr-1"></i> Save Field
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="cancelFieldBtn">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Fields List -->
                @if($category->fields && $category->fields->count() > 0)
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 pl-4">
                                        <i class="fas fa-font text-muted mr-1"></i>Field Name
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-cog text-muted mr-1"></i>Type
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-asterisk text-muted mr-1"></i>Required
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-sort-numeric-up text-muted mr-1"></i>Order
                                    </th>
                                    <th class="border-0 text-center">
                                        <i class="fas fa-tasks text-muted mr-1"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->fields as $field)
                                <tr class="border-bottom" id="field-row-{{ $field->id }}">
                                    <td class="pl-4">
                                        <strong>{{ $field->name }}</strong>
                                        @if($field->description)
                                            <br>
                                            <small class="text-muted">{{ $field->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info badge-pill px-3 py-1">
                                            {{ ucfirst($field->type) }}
                                        </span>
                                        @if(in_array($field->type, ['select', 'checkbox', 'radio']) && $field->options)
                                            <br>
                                            <small class="text-muted">
                                                {{ count(explode(',', $field->options)) }} options
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($field->is_required)
                                            <span class="badge badge-success badge-pill px-3 py-1">
                                                <i class="fas fa-check mr-1"></i>Required
                                            </span>
                                        @else
                                            <span class="badge badge-light badge-pill px-3 py-1">
                                                <i class="fas fa-times mr-1"></i>Optional
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $field->order }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-outline-warning btn-sm px-3 edit-field-btn"
                                                    data-field-id="{{ $field->id }}"
                                                    data-field-name="{{ $field->name }}"
                                                    data-field-type="{{ $field->type }}"
                                                    data-field-description="{{ $field->description ?? '' }}"
                                                    data-field-options="{{ $field->options ?? '' }}"
                                                    data-field-order="{{ $field->order }}"
                                                    data-field-required="{{ $field->is_required }}"
                                                    data-toggle="tooltip" 
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm px-3 delete-field-btn"
                                                    data-field-id="{{ $field->id }}"
                                                    data-field-name="{{ $field->name }}"
                                                    data-toggle="tooltip" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="card-body text-center py-5">
                    <i class="fas fa-list-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Fields Added Yet</h5>
                    <p class="text-muted mb-4">Click "Add New Field" to define the data structure for this category</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">
            <!-- Category Stats -->
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-gradient-success text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-bar mr-1"></i>Category Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6 border-right">
                            <div class="text-primary mb-1">
                                <i class="fas fa-list-alt fa-2x"></i>
                            </div>
                            <div class="h5 font-weight-bold">{{ $category->fields_count ?? $category->fields->count() }}</div>
                            <div class="text-muted small">Total Fields</div>
                        </div>
                        <div class="col-6">
    <div class="text-success mb-1">
        <i class="fas fa-users fa-2x"></i>
    </div>
    <div class="h5 font-weight-bold">
        @php
            $roleCount = 0;
            if (isset($category->accessible_roles)) {
                if (is_string($category->accessible_roles)) {
                    $roleCount = count(json_decode($category->accessible_roles, true) ?? []);
                } else {
                    $roleCount = count($category->accessible_roles ?? []);
                }
            }
        @endphp
        {{ $roleCount }}
    </div>
    <div class="text-muted small">Accessible Roles</div>
</div>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-primary" 
                             role="progressbar" 
                             style="width: {{ min((($category->fields_count ?? $category->fields->count()) / 10) * 100, 100) }}%">
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-chart-line mr-1"></i>
                        Field usage progress
                    </small>
                </div>
            </div>
            
            <!-- Important Notes -->
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-gradient-warning text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-exclamation-circle mr-1"></i>Important Notes
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-shield-alt text-warning mt-1 mr-3"></i>
                                <div>
                                    <small class="font-weight-bold d-block">Role Permissions</small>
                                    <small class="text-muted">Changing accessible roles affects user permissions immediately</small>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-unique text-info mt-1 mr-3"></i>
                                <div>
                                    <small class="font-weight-bold d-block">Unique Names</small>
                                    <small class="text-muted">Category names must remain unique across the system</small>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-trash text-danger mt-1 mr-3"></i>
                                <div>
                                    <small class="font-weight-bold d-block">Deletion Warning</small>
                                    <small class="text-muted">Deleting a category will also delete all associated fields</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient-secondary text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-bolt mr-1"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-block text-left py-3" id="addFieldBtn">
                            <i class="fas fa-plus-circle mr-2"></i>
                            <strong>Add New Field</strong>
                        </button>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="btn btn-outline-info btn-block text-left py-3">
                            <i class="fas fa-list mr-2"></i>
                            <strong>View All Categories</strong>
                        </a>
                        <a href="{{ route('admin.categories.create') }}" 
                           class="btn btn-outline-success btn-block text-left py-3">
                            <i class="fas fa-plus mr-2"></i>
                            <strong>Create New Category</strong>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .role-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .role-checkbox:checked + label {
        color: #4e73df;
    }
    
    .role-icon {
        width: 40px;
        text-align: center;
    }
    
    .border-left-4 {
        border-left-width: 4px !important;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    #fieldFormContainer {
        background-color: #f8f9fc;
        border-top: 3px solid #4e73df;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Field type change handler
        const fieldTypeSelect = document.getElementById('field_type');
        const optionsLabel = document.getElementById('optionsLabel');
        const optionsInput = document.getElementById('options');
        const optionsHelp = document.getElementById('optionsHelp');
        
        fieldTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            const needsOptions = ['select', 'checkbox', 'radio'].includes(selectedType);
            
            if (needsOptions) {
                optionsLabel.style.display = 'block';
                optionsInput.style.display = 'block';
                optionsInput.required = true;
                optionsHelp.style.display = 'block';
            } else {
                optionsLabel.style.display = 'none';
                optionsInput.style.display = 'none';
                optionsInput.required = false;
                optionsHelp.style.display = 'none';
            }
        });
        
        // Toggle field form
        const toggleFieldFormBtn = document.getElementById('toggleFieldForm');
        const addFieldBtn = document.getElementById('addFieldBtn');
        const fieldFormContainer = document.getElementById('fieldFormContainer');
        const fieldForm = document.getElementById('fieldForm');
        const fieldSubmitBtn = document.getElementById('fieldSubmitBtn');
        const cancelFieldBtn = document.getElementById('cancelFieldBtn');
        const formMethodInput = document.getElementById('formMethod');
        const fieldIdInput = document.getElementById('field_id');
        
        function showFieldForm(isEdit = false) {
            fieldFormContainer.style.display = 'block';
            if (!isEdit) {
                resetFieldForm();
            }
            fieldFormContainer.scrollIntoView({ behavior: 'smooth' });
        }
        
        function hideFieldForm() {
            fieldFormContainer.style.display = 'none';
            resetFieldForm();
        }
        
        function resetFieldForm() {
            fieldForm.reset();
            fieldIdInput.value = '';
            formMethodInput.value = 'POST';
            fieldSubmitBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Save Field';
            fieldSubmitBtn.classList.remove('btn-success');
            fieldSubmitBtn.classList.add('btn-primary');
            document.querySelector('.card-header .btn').innerHTML = '<i class="fas fa-plus mr-1"></i> Add New Field';
            
            // Hide options if visible
            optionsLabel.style.display = 'none';
            optionsInput.style.display = 'none';
            optionsInput.required = false;
            optionsHelp.style.display = 'none';
        }
        
        toggleFieldFormBtn.addEventListener('click', function() {
            if (fieldFormContainer.style.display === 'none') {
                showFieldForm();
            } else {
                hideFieldForm();
            }
        });
        
        addFieldBtn.addEventListener('click', function() {
            showFieldForm();
        });
        
        cancelFieldBtn.addEventListener('click', function() {
            hideFieldForm();
        });
        
        // Edit field button handler
        document.querySelectorAll('.edit-field-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const fieldId = this.dataset.fieldId;
                const fieldName = this.dataset.fieldName;
                const fieldType = this.dataset.fieldType;
                const fieldDescription = this.dataset.fieldDescription;
                const fieldOptions = this.dataset.fieldOptions;
                const fieldOrder = this.dataset.fieldOrder;
                const fieldRequired = this.dataset.fieldRequired === '1';
                
                // Set form values
                document.getElementById('field_name').value = fieldName;
                document.getElementById('field_type').value = fieldType;
                document.getElementById('field_description').value = fieldDescription;
                document.getElementById('options').value = fieldOptions;
                document.getElementById('field_order').value = fieldOrder;
                document.getElementById('is_required').checked = fieldRequired;
                fieldIdInput.value = fieldId;
                
                // Update form for editing
                formMethodInput.value = 'PUT';
                fieldSubmitBtn.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Update Field';
                fieldSubmitBtn.classList.remove('btn-primary');
                fieldSubmitBtn.classList.add('btn-success');
                document.querySelector('.card-header .btn').innerHTML = '<i class="fas fa-edit mr-1"></i> Edit Field';
                
                // Show options if needed
                if (['select', 'checkbox', 'radio'].includes(fieldType)) {
                    optionsLabel.style.display = 'block';
                    optionsInput.style.display = 'block';
                    optionsInput.required = true;
                    optionsHelp.style.display = 'block';
                }
                
                showFieldForm(true);
            });
        });
        
       
  // Field form submission
fieldForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const fieldId = fieldIdInput.value;
    const isEdit = formMethodInput.value === 'PUT';
    const categoryId = "{{ $category->id }}";

    const url = isEdit
        ? `/admin/categories/fields/${fieldId}`     // UPDATE
        : `/admin/categories/${categoryId}/fields`; // CREATE

    fetch(url, {
        method: 'POST', // Laravel will detect PUT via the formMethod hidden input
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });

            // Refresh so UI updates cleanly
            setTimeout(() => {
                window.location.reload();
            }, 1500);

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'An error occurred.',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An unexpected error occurred. Please try again.',
        });
    });
});

        
        // Delete field button handler
        document.querySelectorAll('.delete-field-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const fieldId = this.dataset.fieldId;
                const fieldName = this.dataset.fieldName;
                
                Swal.fire({
                    title: 'Delete Field?',
                    html: `Are you sure you want to delete the field <strong>"${fieldName}"</strong>?<br><br>This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/category-fields/${fieldId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove the row from table
                                document.getElementById(`field-row-${fieldId}`).remove();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                
                                // Update stats if needed
                                const fieldCountElement = document.querySelector('.h5.font-weight-bold:first-child');
                                if (fieldCountElement) {
                                    const currentCount = parseInt(fieldCountElement.textContent);
                                    fieldCountElement.textContent = currentCount - 1;
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'Failed to delete field.',
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An unexpected error occurred. Please try again.',
                            });
                        });
                    }
                });
            });
        });
        
        // Role card click handler
        document.querySelectorAll('.role-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.custom-control-input')) {
                    const checkbox = this.querySelector('.role-checkbox');
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        });
        
        // Role checkbox change handler
        document.querySelectorAll('.role-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.role-card');
                if (this.checked) {
                    card.classList.remove('border-left-light');
                    card.classList.add('border-left-primary');
                } else {
                    card.classList.remove('border-left-primary');
                    card.classList.add('border-left-light');
                }
            });
        });
        
        // Category form validation
        const categoryForm = document.getElementById('categoryForm');
        categoryForm.addEventListener('submit', function(e) {
            const nameInput = document.getElementById('name');
            const roleCheckboxes = document.querySelectorAll('.role-checkbox:checked');
            
            if (nameInput.value.trim() === '') {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Category name is required.',
                });
                nameInput.focus();
                return;
            }
            
            if (roleCheckboxes.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please select at least one accessible role.',
                });
                return;
            }
        });
    });
    
    function confirmDelete() {
        Swal.fire({
            title: 'Delete Category?',
            html: `This will delete the category <strong>"{{ $category->name }}"</strong> and all <strong>{{ $category->fields->count() }} associated fields</strong>.<br><br>This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        });
    }
</script>
@endpush