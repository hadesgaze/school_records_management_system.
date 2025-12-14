@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-folder-plus text-primary mr-2"></i>Create New Category
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

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Create Form -->
        <div class="col-lg-8">
            <form action="{{ route('admin.categories.store') }}" method="POST" id="categoryForm">
                @csrf
                
                <!-- Category Information Card -->
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-gradient-primary text-white py-3">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-plus-circle mr-1"></i>Category Information
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        <!-- Name -->
                        <div class="form-group mb-4">
                            <label for="name" class="font-weight-bold text-dark">
                                <i class="fas fa-tag text-primary mr-1"></i>Category Name *
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Enter category name"
                                   required>
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
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Enter category description">{{ old('description') }}</textarea>
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
                                    $selectedRoles = old('accessible_roles', []);
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
                    </div>
                </div>
                
                <!-- Fields Section INSIDE THE FORM NOW -->
                <div class="card shadow-lg border-0 rounded-lg mt-4">
                    <div class="card-header bg-gradient-info text-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-list-alt mr-1"></i>Category Fields (Optional)
                        </h6>
                        <button type="button" class="btn btn-light btn-sm shadow-sm" id="addFieldBtn">
                            <i class="fas fa-plus mr-1"></i> Add Field
                        </button>
                    </div>

                    <div class="card-body" id="fieldsContainer">
                        <div class="alert alert-info mb-0" id="noFieldsMessage">
                            <i class="fas fa-info-circle mr-2"></i>
                            No fields added yet. Click "Add Field" to define metadata fields for this category. 
                            You can also add fields later after creating the category.
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons INSIDE THE FORM -->
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Create Category
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">
            <!-- Tips Card -->
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-gradient-warning text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-lightbulb mr-1"></i>Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle text-success mt-1 mr-3"></i>
                                <div>
                                    <small class="font-weight-bold d-block">Unique Names</small>
                                    <small class="text-muted">Category name must be unique across the system</small>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-shield-alt text-primary mt-1 mr-3"></i>
                                <div>
                                    <small class="font-weight-bold d-block">Role Access</small>
                                    <small class="text-muted">Select at least one role to access this category</small>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-list-alt text-info mt-1 mr-3"></i>
                                <div>
                                    <small class="font-weight-bold d-block">Optional Fields</small>
                                    <small class="text-muted">Fields are optional during creation, you can add them later</small>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-file-upload text-warning mt-1 mr-3"></i>
                                <div>
                                    <small class="font-weight-bold d-block">Metadata Fields</small>
                                    <small class="text-muted">Fields capture additional information for uploaded files</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Field Types Card -->
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient-secondary text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-shapes mr-1"></i>Available Field Types
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-font text-primary mr-2"></i>
                            <strong class="small">Text</strong>
                        </div>
                        <small class="text-muted ml-4">Single-line text input</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-align-left text-success mr-2"></i>
                            <strong class="small">Text Area</strong>
                        </div>
                        <small class="text-muted ml-4">Multi-line text input</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-hashtag text-info mr-2"></i>
                            <strong class="small">Number</strong>
                        </div>
                        <small class="text-muted ml-4">Numeric values only</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope text-danger mr-2"></i>
                            <strong class="small">Email</strong>
                        </div>
                        <small class="text-muted ml-4">Email address validation</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar text-warning mr-2"></i>
                            <strong class="small">Date</strong>
                        </div>
                        <small class="text-muted ml-4">Date picker field</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-list text-primary mr-2"></i>
                            <strong class="small">Dropdown</strong>
                        </div>
                        <small class="text-muted ml-4">Select from predefined options</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-square text-success mr-2"></i>
                            <strong class="small">Checkbox</strong>
                        </div>
                        <small class="text-muted ml-4">Multiple selection options</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-dot-circle text-info mr-2"></i>
                            <strong class="small">Radio Button</strong>
                        </div>
                        <small class="text-muted ml-4">Single selection from options</small>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-file-upload text-danger mr-2"></i>
                            <strong class="small">File Upload</strong>
                        </div>
                        <small class="text-muted ml-4">Allow file attachments</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Field Template (Hidden) -->
<template id="fieldTemplate">
    <div class="field-item card mb-3 border-left-4 border-left-primary">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small">Field Name *</label>
                        <input type="text" 
                               class="form-control form-control-sm field-name-input" 
                               name="fields[INDEX][name]"
                               placeholder="e.g., Author Name"
                               required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small">Field Type *</label>
                        <select class="form-control form-control-sm field-type-select" 
                                name="fields[INDEX][type]"
                                required>
                            <option value="">Select type</option>
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
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small">Order</label>
                        <input type="number" 
                               class="form-control form-control-sm" 
                               name="fields[INDEX][order]"
                               value="0"
                               min="0">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small d-block">Options</label>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="required_INDEX"
                                   name="fields[INDEX][is_required]"
                                   value="1">
                            <label class="custom-control-label small" for="required_INDEX">
                                Required Field
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small">Description (Optional)</label>
                        <textarea class="form-control form-control-sm" 
                                  name="fields[INDEX][description]"
                                  rows="2"
                                  placeholder="Brief description of this field"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row options-container" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small">Options (comma-separated) *</label>
                        <input type="text" 
                               class="form-control form-control-sm options-input" 
                               name="fields[INDEX][options]"
                               placeholder="e.g., Option 1, Option 2, Option 3">
                        <small class="text-muted">Required for dropdown, checkbox, and radio button fields</small>
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <button type="button" class="btn btn-danger btn-sm remove-field-btn">
                    <i class="fas fa-trash mr-1"></i> Remove Field
                </button>
            </div>
        </div>
    </div>
</template>
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
    
    .field-item {
        transition: all 0.3s ease;
    }
    
    .field-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let fieldIndex = 0;
        const fieldsContainer = document.getElementById('fieldsContainer');
        const noFieldsMessage = document.getElementById('noFieldsMessage');
        const addFieldBtn = document.getElementById('addFieldBtn');
        const fieldTemplate = document.getElementById('fieldTemplate');
        const categoryForm = document.getElementById('categoryForm');
        
        // Add field function
        function addField(fieldData = {}) {
            // Hide no fields message
            if (noFieldsMessage) {
                noFieldsMessage.style.display = 'none';
            }
            
            // Clone template
            const template = fieldTemplate.content.cloneNode(true);
            const fieldItem = template.querySelector('.field-item');
            
            // Replace INDEX with actual index
            fieldItem.innerHTML = fieldItem.innerHTML.replace(/INDEX/g, fieldIndex);
            
            // Set values if provided (for old input)
            if (fieldData.name) {
                fieldItem.querySelector(`input[name="fields[${fieldIndex}][name]"]`).value = fieldData.name;
            }
            if (fieldData.type) {
                fieldItem.querySelector(`select[name="fields[${fieldIndex}][type]"]`).value = fieldData.type;
                // Show options if needed
                if (['select', 'checkbox', 'radio'].includes(fieldData.type)) {
                    fieldItem.querySelector('.options-container').style.display = 'block';
                    if (fieldData.options) {
                        fieldItem.querySelector(`input[name="fields[${fieldIndex}][options]"]`).value = fieldData.options;
                    }
                }
            }
            if (fieldData.description) {
                fieldItem.querySelector(`textarea[name="fields[${fieldIndex}][description]"]`).value = fieldData.description;
            }
            if (fieldData.order) {
                fieldItem.querySelector(`input[name="fields[${fieldIndex}][order]"]`).value = fieldData.order;
            }
            if (fieldData.is_required) {
                fieldItem.querySelector(`input[name="fields[${fieldIndex}][is_required]"]`).checked = true;
            }
            
            // Append to container
            fieldsContainer.appendChild(fieldItem);
            
            // Add event listeners
            const typeSelect = fieldsContainer.querySelector(`select[name="fields[${fieldIndex}][type]"]`);
            const optionsContainer = fieldItem.querySelector('.options-container');
            const optionsInput = fieldItem.querySelector('.options-input');
            
            typeSelect.addEventListener('change', function() {
                const needsOptions = ['select', 'checkbox', 'radio'].includes(this.value);
                optionsContainer.style.display = needsOptions ? 'block' : 'none';
                optionsInput.required = needsOptions;
            });
            
            fieldIndex++;
        }
        
        // Add field button click
        addFieldBtn.addEventListener('click', function() {
            addField();
        });
        
        // Remove field handler
        fieldsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-field-btn')) {
                const fieldItem = e.target.closest('.field-item');
                fieldItem.remove();
                
                // Show no fields message if no fields left
                const remainingFields = fieldsContainer.querySelectorAll('.field-item');
                if (remainingFields.length === 0 && noFieldsMessage) {
                    noFieldsMessage.style.display = 'block';
                }
            }
        });
        
        // Restore old input values if validation fails
        @if(old('fields'))
            const oldFields = @json(old('fields'));
            oldFields.forEach(function(field) {
                addField(field);
            });
        @endif
        
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
            
            // Validate fields
            const fields = fieldsContainer.querySelectorAll('.field-item');
            let hasError = false;
            
            fields.forEach((field, index) => {
                const nameInput = field.querySelector('.field-name-input');
                const typeSelect = field.querySelector('.field-type-select');
                const optionsInput = field.querySelector('.options-input');
                
                if (!nameInput.value.trim()) {
                    hasError = true;
                    nameInput.classList.add('is-invalid');
                } else {
                    nameInput.classList.remove('is-invalid');
                }
                
                if (!typeSelect.value) {
                    hasError = true;
                    typeSelect.classList.add('is-invalid');
                } else {
                    typeSelect.classList.remove('is-invalid');
                    
                    // Check if options are required and provided
                    if (['select', 'checkbox', 'radio'].includes(typeSelect.value)) {
                        if (!optionsInput.value.trim()) {
                            hasError = true;
                            optionsInput.classList.add('is-invalid');
                        } else {
                            optionsInput.classList.remove('is-invalid');
                        }
                    }
                }
            });
            
            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required field information.',
                });
            }
        });
    });
</script>
@endpush