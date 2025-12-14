@extends('layouts.admin')

@section('page-title', 'Upload Documents')

@section('content')
<div class="container-fluid py-4">
    
    <!-- Notification Messages -->
    @if(session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <div class="toast show success-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <div class="toast show error-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            
            <!-- Progress Steps -->
            <div class="progress-steps mb-5">
                <div class="steps-container">
                    <div class="step {{ request('category_id') ? 'active' : 'completed' }}">
                        <div class="step-circle">1</div>
                        <div class="step-label">Select Category</div>
                    </div>
                    <div class="step-divider {{ request('category_id') ? 'active' : '' }}"></div>
                    <div class="step {{ request('category_id') ? 'active' : '' }}">
                        <div class="step-circle">2</div>
                        <div class="step-label">Enter Details</div>
                    </div>
                    <div class="step-divider"></div>
                    <div class="step">
                        <div class="step-circle">3</div>
                        <div class="step-label">Upload Document</div>
                    </div>
                </div>
            </div>
            
            <!-- Main Card -->
            <div class="card modern-card border-0 shadow-lg overflow-hidden">
                <div class="card-header p-4 pb-0 border-0 bg-transparent">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h1 class="h3 mb-2 fw-bold text-gradient-blue">Document Archival</h1>
                            <p class="text-muted mb-0">Securely upload and organize your documents with proper categorization</p>
                        </div>
                        <div class="icon-wrapper">
                            <i class="fas fa-archive fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    
                    <!-- Category Selection -->
                    <div class="category-section mb-5">
                        <div class="section-header mb-4">
                            <h3 class="h5 mb-1 fw-semibold d-flex align-items-center">
                                <span class="section-icon me-2">
                                    <i class="fas fa-folder-open"></i>
                                </span>
                                Document Category
                            </h3>
                            <p class="text-muted mb-0">Choose the appropriate category for your document</p>
                        </div>
                        
                        <form method="GET" action="{{ route('admin.upload_files') }}" class="category-form">
                            <div class="card category-selector-card p-3 border-0 shadow-sm">
                                <label class="form-label fw-semibold mb-3">Select a category to proceed</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-tag text-primary"></i>
                                    </span>
                                    <select class="form-select form-select-lg border-start-0 ps-0" name="category_id" onchange="this.form.submit()">
                                        <option value="">Choose a category...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected':'' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text mt-3 ps-1">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    Each category has specific fields tailored for that document type
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    @php
                        $selected = request('category_id') 
                            ? $categories->where('id', request('category_id'))->first() 
                            : null;
                    @endphp
                    
                    @if($selected)
                    <!-- Semester and School Year Section -->
                    <div class="semester-section mb-5">
                        <div class="section-header mb-4">
                            <h3 class="h5 mb-1 fw-semibold d-flex align-items-center">
                                <span class="section-icon me-2">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                Academic Period
                            </h3>
                            <p class="text-muted mb-0">Specify the semester and school year for this document</p>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card category-selector-card p-3 border-0 shadow-sm">
                                    <label class="form-label fw-semibold mb-3">Semester <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-graduation-cap text-primary"></i>
                                        </span>
                                        <select class="form-select form-select-lg border-start-0 ps-0" id="semesterSelect" required>
                                            <option value="">Select semester...</option>
                                            <option value="1st Semester">1st Semester</option>
                                            <option value="2nd Semester">2nd Semester</option>
                                            <option value="Summer">Summer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card category-selector-card p-3 border-0 shadow-sm">
                                    <label class="form-label fw-semibold mb-3">School Year <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-calendar text-primary"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control form-control-lg border-start-0 ps-0" 
                                               id="schoolYearInput" 
                                               placeholder="e.g., 2024-2025"
                                               pattern="\d{4}-\d{4}"
                                               value="{{ date('Y') }}-{{ date('Y') + 1 }}"
                                               required>
                                    </div>
                                    <div class="form-text mt-2 ps-1">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        Format: YYYY-YYYY (e.g., 2024-2025)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Document Details Form -->
                    <div class="details-section">
                        <div class="section-header mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="h5 mb-1 fw-semibold d-flex align-items-center">
                                        <span class="section-icon me-2">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                        Document Details
                                    </h3>
                                    <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                        <i class="fas fa-tag me-1"></i> {{ $selected->name }}
                                    </div>
                                </div>
                                <a href="{{ route('admin.upload_files') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-exchange-alt me-1"></i> Change Category
                                </a>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('admin.upload.document') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $selected->id }}">
                            <input type="hidden" name="semester" id="hiddenSemester">
                            <input type="hidden" name="school_year" id="hiddenSchoolYear">
                            
                            <!-- Dynamic Fields Grid -->
                            <div class="row g-4 mb-5">
                                @foreach($selected->fields as $field)
                                <div class="col-md-6">
                                    <div class="form-card p-4 h-100 border rounded-3">
                                        <div class="form-header mb-3">
                                            <label class="form-label fw-semibold d-flex align-items-center">
                                                {{ $field->name }}
                                                @if($field->is_required)
                                                <span class="text-danger ms-1">*</span>
                                                @endif
                                                @if($field->type === 'file')
                                                <span class="badge bg-info bg-opacity-10 text-info ms-2">
                                                    <i class="fas fa-paperclip me-1"></i> File
                                                </span>
                                                @endif
                                            </label>
                                            @if($field->description)
                                            <p class="text-muted small mb-0">{{ $field->description }}</p>
                                            @endif
                                        </div>
                                        
                                        @if($field->type === 'textarea')
                                            <textarea name="fields[{{ $field->slug }}]" class="form-control modern-textarea" 
                                                rows="3" {{ $field->is_required ? 'required':'' }}
                                                placeholder="Type your {{ strtolower($field->name) }} here..."></textarea>
                                        @elseif($field->type === 'date')
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-calendar text-primary"></i>
                                                </span>
                                                <input type="date" name="fields[{{ $field->slug }}]" class="form-control" 
                                                    {{ $field->is_required ? 'required':'' }}>
                                            </div>
                                        @elseif($field->type === 'file')
                                            <div class="file-upload-area border-dashed rounded-3 p-4 text-center position-relative" 
                                                 onclick="document.getElementById('field_{{ $field->id }}').click()">
                                                <input type="file" name="fields[{{ $field->slug }}]" class="form-control d-none" 
                                                    id="field_{{ $field->id }}" {{ $field->is_required ? 'required':'' }}>
                                                <div class="upload-icon mb-3">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
                                                </div>
                                                <h6 class="mb-2">Upload {{ strtolower($field->name) }}</h6>
                                                <p class="text-muted small mb-0">Click or drag & drop your file here</p>
                                                <small class="text-muted">Max size: 10MB • PDF, DOC, DOCX, Images</small>
                                            </div>
                                            <div class="selected-file mt-3" id="selected_{{ $field->id }}" style="display: none;">
                                                <div class="alert alert-success d-flex align-items-center py-2 px-3">
                                                    <i class="fas fa-check-circle me-2"></i>
                                                    <span class="file-name">File selected</span>
                                                    <button type="button" class="btn-close ms-auto" onclick="clearFileInput('field_{{ $field->id }}', 'selected_{{ $field->id }}')"></button>
                                                </div>
                                            </div>
                                        @else
                                            <input type="text" name="fields[{{ $field->slug }}]" class="form-control modern-input" 
                                                {{ $field->is_required ? 'required':'' }}
                                                placeholder="Enter {{ strtolower($field->name) }}">
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Main Document Upload -->
                            <div class="main-upload-section mb-5">
                                <div class="section-header mb-4">
                                    <h3 class="h5 mb-1 fw-semibold d-flex align-items-center">
                                        <span class="section-icon me-2">
                                            <i class="fas fa-file-upload"></i>
                                        </span>
                                        Upload Main Document
                                    </h3>
                                    <p class="text-muted mb-0">Select the primary document file to archive</p>
                                </div>
                                
                                <div class="card main-upload-card border-dashed p-0 overflow-hidden">
                                    <div class="card-body p-4">
                                        <input type="file" name="document_file" class="form-control d-none" id="mainDocumentFile" required>
                                        <div class="upload-zone text-center py-5" onclick="document.getElementById('mainDocumentFile').click()">
                                            <div class="upload-icon-large mb-4">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                            </div>
                                            <h4 class="mb-3">Drop your file here or click to browse</h4>
                                            <p class="text-muted mb-4">Supported formats: PDF, DOC, DOCX, PPT, XLS, Images</p>
                                            <button type="button" class="btn btn-primary btn-lg px-4">
                                                <i class="fas fa-folder-open me-2"></i>Browse Files
                                            </button>
                                        </div>
                                        <div class="selected-file-main text-center mt-3" id="selectedMainFile" style="display: none;">
                                            <div class="alert alert-success d-flex align-items-center justify-content-between py-3 px-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-alt fa-lg me-3 text-success"></i>
                                                    <div class="text-start">
                                                        <h6 class="mb-0 fw-semibold" id="mainFileName">Document.pdf</h6>
                                                        <small class="text-muted" id="mainFileSize">2.4 MB</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success me-3">Ready to upload</span>
                                                    <button type="button" class="btn-close" onclick="clearFileInput('mainDocumentFile', 'selectedMainFile')"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="action-buttons pt-4 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                    </a>
                                    <div class="d-flex gap-3">
                                        <button type="reset" class="btn btn-light">
                                            <i class="fas fa-redo me-2"></i>Clear Form
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-primary">
                                            <i class="fas fa-upload me-2"></i>Upload Document
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="empty-state text-center py-5 my-4">
                        <div class="empty-icon mb-4">
                            <div class="icon-circle">
                                <i class="fas fa-folder-open fa-3x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">No Category Selected</h3>
                        <p class="text-muted mb-4 px-5">Please select a document category from the dropdown above to view the required fields and upload your document.</p>
                        <div class="d-inline-block p-4 border rounded-3 bg-light bg-opacity-50">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            <span class="text-muted">Pro tip: Categories help organize your files for easier retrieval and management.</span>
                        </div>
                    </div>
                    @endif
                    
                </div>
            </div>
            
            <!-- Quick Stats -->
            @if($selected)
            <div class="row g-4 mt-4">
                <div class="col-md-4">
                    <div class="stat-card card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3">
                                    <i class="fas fa-layer-group fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Category Fields</h6>
                                    <h3 class="h2 mb-0 fw-bold">{{ count($selected->fields) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3">
                                    <i class="fas fa-asterisk fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Required Fields</h6>
                                    <h3 class="h2 mb-0 fw-bold">{{ $selected->fields->where('is_required', true)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3">
                                    <i class="fas fa-paperclip fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">File Uploads</h6>
                                    <h3 class="h2 mb-0 fw-bold">{{ $selected->fields->where('type', 'file')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Main file upload handling
        const mainFileInput = document.getElementById('mainDocumentFile');
        const mainFileDisplay = document.getElementById('selectedMainFile');
        const mainFileName = document.getElementById('mainFileName');
        const mainFileSize = document.getElementById('mainFileSize');
        
        if (mainFileInput) {
            mainFileInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    mainFileName.textContent = file.name;
                    mainFileSize.textContent = formatFileSize(file.size);
                    mainFileDisplay.style.display = 'block';
                    
                    // Add visual feedback
                    mainFileDisplay.querySelector('.alert').classList.remove('alert-secondary');
                    mainFileDisplay.querySelector('.alert').classList.add('alert-success');
                }
            });
        }
        
        // Field file inputs handling
        @if($selected)
            @foreach($selected->fields as $field)
                @if($field->type === 'file')
                    const fieldInput{{ $field->id }} = document.getElementById('field_{{ $field->id }}');
                    const fieldDisplay{{ $field->id }} = document.getElementById('selected_{{ $field->id }}');
                    
                    if (fieldInput{{ $field->id }}) {
                        fieldInput{{ $field->id }}.addEventListener('change', function(e) {
                            if (this.files.length > 0) {
                                fieldDisplay{{ $field->id }}.style.display = 'block';
                                const fileName = fieldDisplay{{ $field->id }}.querySelector('.file-name');
                                if (fileName) {
                                    fileName.textContent = this.files[0].name;
                                }
                            }
                        });
                    }
                @endif
            @endforeach
        @endif
        
        // Form validation and submission
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                // Sync semester and school year values
                const semester = document.getElementById('semesterSelect').value;
                const schoolYear = document.getElementById('schoolYearInput').value;
                
                document.getElementById('hiddenSemester').value = semester;
                document.getElementById('hiddenSchoolYear').value = schoolYear;
                
                // Validate semester and school year
                if (!semester) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('Please select a semester');
                    return false;
                }
                
                if (!schoolYear || !schoolYear.match(/^\d{4}-\d{4}$/)) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('Please enter a valid school year in format YYYY-YYYY');
                    return false;
                }
                
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    // Add shake animation to invalid fields
                    const invalidFields = form.querySelectorAll(':invalid');
                    invalidFields.forEach(field => {
                        field.classList.add('invalid-shake');
                        setTimeout(() => {
                            field.classList.remove('invalid-shake');
                        }, 500);
                    });
                }
                form.classList.add('was-validated');
            }, false);
        });
        
        // Drag and drop functionality
        const uploadZones = document.querySelectorAll('.file-upload-area, .upload-zone');
        uploadZones.forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('drag-over');
            });
            
            zone.addEventListener('dragleave', () => {
                zone.classList.remove('drag-over');
            });
            
            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('drag-over');
                
                if (e.dataTransfer.files.length) {
                    const fileInput = zone.parentElement.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.files = e.dataTransfer.files;
                        
                        // Trigger change event
                        const event = new Event('change');
                        fileInput.dispatchEvent(event);
                    }
                }
            });
        });
        
        // Auto-dismiss toasts
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(toast => {
            setTimeout(() => {
                toast.classList.remove('show');
                toast.classList.add('hide');
            }, 5000);
        });
        
        // Validate school year input
        const schoolYearInput = document.getElementById('schoolYearInput');
        if (schoolYearInput) {
            schoolYearInput.addEventListener('blur', function() {
                const pattern = /^\d{4}-\d{4}$/;
                if (this.value && !pattern.test(this.value)) {
                    this.classList.add('is-invalid');
                    this.setCustomValidity('Please enter school year in format YYYY-YYYY');
                } else {
                    this.classList.remove('is-invalid');
                    this.setCustomValidity('');
                }
            });
        }
    });
    
    // Helper function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Clear file input
    function clearFileInput(inputId, displayId) {
        const input = document.getElementById(inputId);
        const display = document.getElementById(displayId);
        
        if (input) {
            input.value = '';
        }
        if (display) {
            display.style.display = 'none';
        }
    }
</script>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --primary-color: #667eea;
        --secondary-color: #764ba2;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --light-bg: #f8fafc;
    }
    
    /* Modern Card */
    .modern-card {
        border-radius: 16px;
        background: white;
        transition: transform 0.3s ease;
    }
    
    .modern-card:hover {
        transform: translateY(-2px);
    }
    
    /* Progress Steps */
    .progress-steps {
        padding: 0 1rem;
    }
    
    .steps-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }
    
    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 2;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        background: #e2e8f0;
        color: #64748b;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    
    .step.active .step-circle {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .step.completed .step-circle {
        background: var(--success-color);
        color: white;
    }
    
    .step-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
    }
    
    .step.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .step-divider {
        flex: 1;
        height: 2px;
        background: #e2e8f0;
        margin: 0 10px;
        position: relative;
        top: -20px;
    }
    
    .step-divider.active {
        background: linear-gradient(to right, var(--primary-color), #e2e8f0);
    }
    
    /* Text Gradient */
    .text-gradient-blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Section Headers */
    .section-header {
        position: relative;
        padding-left: 1.5rem;
    }
    
    .section-header::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--primary-gradient);
        border-radius: 4px;
    }
    
    .section-icon {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
    }
    
    /* Form Cards */
    .form-card {
        background: white;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .form-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }
    
    /* Modern Inputs */
    .modern-input, .modern-textarea {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        transition: all 0.3s ease;
    }
    
    .modern-input:focus, .modern-textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    /* File Upload Areas */
    .border-dashed {
        border: 2px dashed #cbd5e1;
    }
    
    .file-upload-area, .upload-zone {
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .file-upload-area:hover, .upload-zone:hover {
        background: #f1f5f9;
        border-color: var(--primary-color);
    }
    
    .drag-over {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-color: var(--primary-color) !important;
        border-style: solid !important;
    }
    
    /* Main Upload Card */
    .main-upload-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .upload-icon-large {
        opacity: 0.8;
    }
    
    /* Stats Cards */
    .stat-card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Toast Notifications */
    .toast {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .success-toast .toast-header {
        background: var(--success-color);
    }
    
    .error-toast .toast-header {
        background: #ef4444;
    }
    
    /* Animations */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .invalid-shake {
        animation: shake 0.3s ease-in-out;
        border-color: #ef4444 !important;
    }
    
    /* Empty State */
    .empty-icon .icon-circle {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    /* Button Styles */
    .shadow-primary {
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-primary {
        background: var(--primary-gradient);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }
    
    /* Badge Styles */
    .badge.bg-primary.bg-opacity-10 {
        background-color: rgba(102, 126, 234, 0.1) !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .steps-container {
            flex-direction: column;
            gap: 1rem;
        }
        
        .step-divider {
            display: none;
        }
        
        .action-buttons .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .action-buttons .d-flex .btn {
            width: 100%;
        }
    }
</style>
@endpush
@endsection