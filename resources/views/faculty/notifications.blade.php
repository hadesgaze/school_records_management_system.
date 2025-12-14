@extends('layouts.faculty')
@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-bell"></i> Notifications</h3>
        <form method="POST" action="{{ route('faculty.notifications.readAll') }}">
            @csrf
            <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-check2-all me-1"></i> Mark all as read
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $noItems = method_exists($notifications, 'isEmpty')
            ? $notifications->isEmpty()
            : ($notifications->count() === 0);
    @endphp

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($noItems)
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-3">No notifications yet.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notif)
                        <div class="list-group-item d-flex justify-content-between align-items-start {{ $notif->is_read ? '' : 'bg-light' }}">
                            <div class="me-3">
                               {{-- Header line --}}
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fw-semibold mb-0">
                                    <i class="bi bi-megaphone"></i>
                                    {{ optional($notif->sender)->name ?? 'System' }}
                                </h6>

                            @if(!empty($notif->user_id))
                                <span class="badge text-bg-primary">Direct to you</span>
                            @elseif($notif->receiver_role === 'faculty')
                                <span class="badge text-bg-secondary">To: All Faculty</span>
                            @else
                                <span class="badge text-bg-light text-dark">General</span>
                            @endif
                            </div>

                            {{-- Message (decrypted/parsed) --}}
                           <p class="mb-1 text-muted break-anywhere">{{ $notif->message_text }}</p>
                            {{-- Actions --}}
                            <div class="text-nowrap d-flex gap-2">
                                @if(!$notif->is_read)
                                    <form action="{{ route('faculty.notifications.read', $notif->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check2-circle"></i> Mark as Read
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('faculty.notifications.unread', $notif->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-arrow-counterclockwise"></i> Mark as Unread
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('faculty.notifications.destroy', $notif->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this notification? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>


    {{-- Pagination (only if paginator) --}}
    @if(method_exists($notifications, 'links'))
        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
