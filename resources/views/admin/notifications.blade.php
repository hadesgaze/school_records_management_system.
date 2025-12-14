@extends('layouts.admin')
@section('title', 'Notifications')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-bell"></i> Notifications</h3>
        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addNotificationForm">
            <i class="bi bi-plus-circle"></i> Add Notification
        </button>
    </div>

    {{-- ✅ Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 📨 Add Notification Form --}}
    <div class="collapse mb-4" id="addNotificationForm">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3"><i class="bi bi-megaphone"></i> Create New Notification</h5>

                <form action="{{ route('admin.notifications.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        {{-- 🔘 Send Type --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Send To</label>
                            <select id="send_type" name="send_type" class="form-select" required onchange="toggleRecipient()">
                                <option value="role">By Role</option>
                                <option value="user">Individual User</option>
                            </select>
                        </div>

                        {{-- 🎯 Recipient by Role --}}
                        <div class="col-md-4" id="role_field">
                            <label for="receiver_role" class="form-label fw-semibold">Recipient Role</label>
                            <select name="receiver_role" id="receiver_role" class="form-select">
                                <option value="">Select Role</option>
                                <option value="dean">Dean</option>
                                <option value="chairperson">Chairperson</option>
                                <option value="faculty">Faculty</option>
                                <option value="all">All</option>
                            </select>
                        </div>

                        {{-- 👤 Recipient by User --}}
                        <div class="col-md-4 d-none" id="user_field">
                            <label for="user_id" class="form-label fw-semibold">Select User</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ✉️ Message --}}
                        <div class="col-md-8">
                            <label for="message" class="form-label fw-semibold">Message</label>
                            <input type="text" class="form-control" id="message" name="message"
                                   placeholder="Enter your notification message..." required>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button class="btn btn-success">
                            <i class="bi bi-send"></i> Send Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 🔔 Notification List --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($notifications->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-3">No notifications available.</p>
                </div>
            @else
                <div class="list-group">
                    @foreach($notifications as $notif)
                    <div class="list-group-item mb-2 rounded shadow-sm border-0
                        {{ $notif->is_read ? '' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-envelope-open"></i> From {{ ucfirst($notif->sender_role ?? 'System') }}
                                </h6>
                                <p class="mb-1 text-muted">{{ $notif->message }}</p>
                                <small class="text-secondary">
                                    <i class="bi bi-clock"></i> {{ $notif->created_at->diffForHumans() }}
                                </small>
                            </div>
                            @if(!$notif->is_read)
                            <form action="{{ route('admin.notifications.markRead', $notif->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-check2-circle"></i> Mark as Read
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 🧠 Toggle Role/User Selection --}}
<script>
function toggleRecipient() {
    const sendType = document.getElementById('send_type').value;
    document.getElementById('role_field').classList.toggle('d-none', sendType !== 'role');
    document.getElementById('user_field').classList.toggle('d-none', sendType !== 'user');
}
</script>
@endsection
