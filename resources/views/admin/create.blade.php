@extends('layouts.admin')
@section('title', 'Create Notification')

@section('content')
<div class="container py-4">
    <h3 class="mb-4"><i class="bi bi-megaphone"></i> Create Notification</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="receiver_role" class="form-label">Recipient Role</label>
                    <select name="receiver_role" id="receiver_role" class="form-select" required>
                        <option value="">Select Role</option>
                        <option value="dean">Dean</option>
                        <option value="chairperson">Chairperson</option>
                        <option value="faculty">Faculty</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea name="message" id="message" rows="4" class="form-control" placeholder="Write your notification message..." required></textarea>
                </div>

                <button class="btn btn-primary">
                    <i class="bi bi-send"></i> Send Notification
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
