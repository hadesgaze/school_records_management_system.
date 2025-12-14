@extends('layouts.admin')

@section('page-title', 'File Details - ' . $file->original_name)

@section('content')
<div class="container-fluid py-4">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header with Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.archived_files') }}">Archived Files</a></li>
                    <li class="breadcrumb-item active" aria-current="page">File Details</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h2 mb-0">File Details</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.archived_files') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Archive
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- File Overview Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>File Overview</h5>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-folder me-1"></i>{{ $file->category->name }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- File Icon -->
                        <div class="col-md-3 text-center mb-4 mb-md-0">
                            @php
                                $iconMap = [
                                    'pdf' => ['fas fa-file-pdf', 'text-danger', 'PDF Document'],
                                    'doc' => ['fas fa-file-word', 'text-primary', 'Word Document'],
                                    'docx' => ['fas fa-file-word', 'text-primary', 'Word Document'],
                                    'xls' => ['fas fa-file-excel', 'text-success', 'Excel Spreadsheet'],
                                    'xlsx' => ['fas fa-file-excel', 'text-success', 'Excel Spreadsheet'],
                                    'ppt' => ['fas fa-file-powerpoint', 'text-warning', 'PowerPoint Presentation'],
                                    'pptx' => ['fas fa-file-powerpoint', 'text-warning', 'PowerPoint Presentation'],
                                    'jpg' => ['fas fa-file-image', 'text-info', 'Image File'],
                                    'jpeg' => ['fas fa-file-image', 'text-info', 'Image File'],
                                    'png' => ['fas fa-file-image', 'text-info', 'Image File'],
                                    'txt' => ['fas fa-file-alt', 'text-secondary', 'Text File'],
                                ];
                                $icon = $iconMap[$file->file_type] ?? ['fas fa-file', 'text-secondary', 'File'];
                            @endphp
                        </div>
                        
                        <!-- File Info -->
                        <div class="col-md-9">
                            <h4 class="mb-3 text-break">{{ $file->original_name }}</h4>
                            
                            <div class="row mb-4">
                                <div class="col-sm-6 mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user text-primary me-3 fa-lg"></i>
                                                <div class="w-100">
                                                    <small class="text-muted d-block">Uploaded By</small>
                                                    <strong class="text-break">{{ $file->uploader->decrypted_name ?? 'Unknown' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar text-success me-3 fa-lg"></i>
                                                <div class="w-100">
                                                    <small class="text-muted d-block">Upload Date</small>
                                                    <strong>{{ $file->created_at->format('M d, Y h:i A') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-hdd text-info me-3 fa-lg"></i>
                                                <div class="w-100">
                                                    <small class="text-muted d-block">File Size</small>
                                                    <strong>
                                                        @php
                                                            $size = $file->file_size;
                                                            if ($size >= 1048576) {
                                                                echo number_format($size / 1048576, 2) . ' MB';
                                                            } elseif ($size >= 1024) {
                                                                echo number_format($size / 1024, 2) . ' KB';
                                                            } else {
                                                                echo $size . ' bytes';
                                                            }
                                                        @endphp
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Added Semester Card -->
                                <div class="col-sm-6 mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-graduation-cap text-indigo me-3 fa-lg"></i>
                                                <div class="w-100">
                                                    <small class="text-muted d-block">Semester</small>
                                                    <strong>{{ $file->semester }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Added School Year Card -->
                                <div class="col-sm-6 mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-alt text-purple me-3 fa-lg"></i>
                                                <div class="w-100">
                                                    <small class="text-muted d-block">School Year</small>
                                                    <strong>{{ $file->school_year }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-tag text-warning me-3 fa-lg"></i>
                                                <div class="w-100">
                                                    <small class="text-muted d-block">File Type</small>
                                                    <strong>{{ strtoupper($file->file_type) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- File Actions -->
                            <div class="row g-2 mt-4">
                                <div class="col-md-4">
                                    <a href="{{ route('admin.download-archive-file', $file->id) }}" 
                                       class="btn btn-success w-100">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                                
                                @if(in_array($file->file_type, ['pdf', 'jpg', 'jpeg', 'png', 'txt']))
                                    <div class="col-md-4">
                                        <a href="{{ route('admin.view-file', $file->id) }}" 
                                            target="_blank"
                                            class="btn btn-info w-100">
                                                <i class="fas fa-eye me-2"></i>View File
                                        </a>
                                    </div>
                                @endif
                                
                                <div class="col-md-4">
                                    <button type="button" 
                                            onclick="confirmDelete()"
                                            class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Custom Fields Data -->
            @if(!empty($fieldData) && count($fieldData) > 0)
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Document Metadata</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($file->category->fields as $field)
                            @if(isset($fieldData[$field->slug]))
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">
                                                {{ $field->name }}
                                                @if($field->is_required)
                                                    <span class="badge bg-danger ms-1">Required</span>
                                                @endif
                                            </h6>
                                            <span class="badge bg-light text-dark">
                                                {{ ucfirst($field->type) }}
                                            </span>
                                        </div>
                                        
                                        @if($field->description)
                                            <p class="text-muted small mb-3">
                                                <i class="fas fa-info-circle me-1"></i>{{ $field->description }}
                                            </p>
                                        @endif
                                        
                                        <div class="mt-auto">
                                            @if($field->type === 'date')
                                                <div class="alert alert-light mb-0">
                                                    <i class="fas fa-calendar me-2"></i>
                                                    {{ \Carbon\Carbon::parse($fieldData[$field->slug])->format('F d, Y') }}
                                                </div>
                                            @elseif($field->type === 'textarea')
                                                <div class="border rounded p-3 bg-light">
                                                    <p class="mb-0 text-break">{{ $fieldData[$field->slug] }}</p>
                                                </div>
                                            @elseif($field->type === 'file' && $fieldData[$field->slug])
                                                <div class="alert alert-light mb-0">
                                                    <i class="fas fa-paperclip me-2"></i>
                                                    {{ basename($fieldData[$field->slug]) }}
                                                </div>
                                            @else
                                                <div class="alert alert-light mb-0">
                                                    {{ $fieldData[$field->slug] ?? 'Not specified' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Category Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Category Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 p-4 rounded-circle d-inline-block mb-3">
                            <i class="fas fa-folder fa-3x text-primary"></i>
                        </div>
                        <h5 class="mt-0">{{ $file->category->name }}</h5>
                        @if($file->category->description)
                            <p class="text-muted mb-0">{{ $file->category->description }}</p>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-tags me-2"></i>Category Fields
                        </h6>
                        <div class="list-group list-group-flush">
                            @foreach($file->category->fields as $field)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <span class="text-break">{{ $field->name }}</span>
                                        @if($field->is_required)
                                            <span class="badge bg-danger ms-1">R</span>
                                        @endif
                                    </div>
                                    <span class="badge bg-light text-dark">{{ ucfirst($field->type) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="alert alert-light mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        <strong>Total Fields:</strong> {{ $file->category->fields->count() }}
                    </div>
                </div>
            </div>

            <!-- File Information -->
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>File Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td style="width: 50%"><i class="fas fa-hdd text-muted me-2"></i>File Size:</td>
                                <td class="text-end">
                                    @php
                                        $size = $file->file_size;
                                        if ($size >= 1048576) {
                                            echo number_format($size / 1048576, 2) . ' MB';
                                        } elseif ($size >= 1024) {
                                            echo number_format($size / 1024, 2) . ' KB';
                                        } else {
                                            echo $size . ' bytes';
                                        }
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-tag text-muted me-2"></i>File Type:</td>
                                <td class="text-end">{{ strtoupper($file->file_type) }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-calendar text-muted me-2"></i>Upload Date:</td>
                                <td class="text-end">{{ $file->created_at->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-clock text-muted me-2"></i>Upload Time:</td>
                                <td class="text-end">{{ $file->created_at->format('h:i A') }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-user text-muted me-2"></i>Uploaded By:</td>
                                <td class="text-end">{{ $file->uploader->decrypted_name ?? 'Unknown' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="mt-3 pt-2 border-top">
                        <small class="text-muted text-break">
                            <i class="fas fa-database me-1"></i>
                            File Path: {{ $file->file_path }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this file?\n\nThis action cannot be undone and the file will be permanently removed.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>

<style>
.card-header {
    border-bottom: none;
}

.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.list-group-item {
    border-color: rgba(0,0,0,.125);
    padding: 0.75rem 0;
}

.table-sm td {
    padding: 0.5rem 0;
}

.fa-3x {
    font-size: 3em;
}

.fa-4x {
    font-size: 4em;
}

.fa-5x {
    font-size: 5em;
}

.text-break {
    word-break: break-word;
    overflow-wrap: break-word;
}

.h-100 {
    height: 100%;
}

.w-100 {
    width: 100%;
}

.mt-auto {
    margin-top: auto;
}
</style>
@endsection