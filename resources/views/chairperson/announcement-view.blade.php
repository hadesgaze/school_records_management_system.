@extends('layouts.faculty')

@section('title', 'Announcement')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3>{{ $announcement->title }}</h3>
            <p class="text-muted">{{ $announcement->created_at->format('F d, Y h:i A') }}</p>
            <hr>
            <p>{{ $announcement->content }}</p>
            @if($announcement->attachment)
                <a href="{{ asset('storage/'.$announcement->attachment) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-paperclip"></i> View Attachment
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
