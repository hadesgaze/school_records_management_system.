@extends('layouts.chairperson')
@section('title', 'Faculty Profile')

@section('content')
@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    // Safe getters that work with arrays or objects
    $photo    = data_get($faculty, 'photo', '');
    $name     = data_get($faculty, 'name', 'Faculty');
    $username = data_get($faculty, 'username', '');
    $email    = data_get($faculty, 'email', '');
    $program  = data_get($faculty, 'program', '');

    // Group documents by type
    $groupedDocuments = collect($documents ?? [])->groupBy(function($doc) {
        return data_get($doc, 'document_type', data_get($doc, 'type', 'Other'));
    });
@endphp

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-person-badge me-2"></i> Faculty Profile
        </h4>
        <a href="{{ route('chairperson.faculty.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Faculty
        </a>
    </div>

    {{-- Profile Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img src="{{ $photo }}"
                     alt="{{ $name }}"
                     class="rounded-circle me-3"
                     style="width:80px; height:80px; object-fit:cover;">
                <div>
                    <h5 class="mb-1">{{ $name }}</h5>
                    @if(!empty($username))
                        <div class="text-muted">{{ '@'.$username }}</div>
                    @endif
                    <div class="mt-1">
                        @if(!empty($email))
                            <span class="me-3"><i class="bi bi-envelope"></i> {{ $email }}</span>
                        @endif
                        @if(!empty($program))
                            <span class="badge bg-info">{{ $program }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Documents by Type --}}
    @if($groupedDocuments->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No documents found.
            </div>
        </div>
    @else
        @foreach($groupedDocuments as $type => $typeDocuments)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-folder2 me-2"></i> {{ $type }}
                        <span class="text-muted">({{ $typeDocuments->count() }})</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Uploaded</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($typeDocuments as $doc)
                                @php
                                    $title     = data_get($doc, 'title', 'Untitled');
                                    $created   = data_get($doc, 'created_at');
                                    $date      = $created ? Carbon::parse($created)->format('M d, Y') : '—';
                                    $docType   = data_get($doc, 'type');
                                    $docId     = data_get($doc, 'id');

                                    $filePath  = data_get($doc, 'file_path');
                                    $fileName  = data_get($doc, 'file_name');
                                    $originalName = data_get($doc, 'original_name');
                                    $ext       = strtolower(pathinfo($fileName ?? $originalName ?? '', PATHINFO_EXTENSION));
                                    $viewable  = in_array($ext, ['pdf','jpg','jpeg','png','gif','webp','txt']);
                                    
                                    $downloadUrl = $docId ? route('chairperson.faculty.document.download', [
                                        'user' => $faculty->id,
                                        'type' => $docType,
                                        'id' => $docId
                                    ]) : '#';
                                    
                                    $viewUrl = $filePath
                                        ? (Str::startsWith($filePath, ['http://','https://','/storage/']) 
                                            ? $filePath 
                                            : asset('storage/' . $filePath))
                                        : null;
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $title }}</td>
                                    <td class="text-muted">{{ $date }}</td>
                                    <td class="text-center">
                                        @if($filePath || $fileName)
                                            @if($viewable && $viewUrl)
                                                <a href="{{ $viewUrl }}" target="_blank" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1"
                                                   title="View Document">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            @endif
                                            
                                            @if($docId)
                                                <a href="{{ $downloadUrl }}" 
                                                   class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                   title="Download Document"
                                                   download="{{ $originalName ?? $fileName ?? 'document' }}">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            @else
                                                <span class="text-muted small">Download unavailable</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">No file attached</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
.badge.bg-secondary-subtle{ background:#f0f0f0; border:1px solid #ddd; }
.table td { vertical-align: middle; }
</style>
@endsection