@extends('layouts.chairperson') 

@section('page-title', 'Notifications')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Total Notifications --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Notifications</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $notifications->total() }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-blue-600 text-2xl">notifications</span>
                </div>
            </div>
        </div>

        {{-- Unread Notifications --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Unread</p>
                    <h3 class="text-3xl font-bold text-orange-600">{{ $unreadCount }}</h3>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-orange-600 text-2xl">mark_email_unread</span>
                </div>
            </div>
        </div>

        {{-- Read Notifications --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Read</p>
                    <h3 class="text-3xl font-bold text-green-600">{{ $notifications->total() - $unreadCount }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-green-600 text-2xl">mark_email_read</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            {{-- Filter Buttons --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route(auth()->user()->role . '.notifications') }}" 
                   class="px-4 py-2 rounded-lg font-medium transition-all {{ !request('filter') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <span class="material-icons text-sm align-middle mr-1">list</span>
                    All
                </a>
                <a href="{{ route(auth()->user()->role . '.notifications', ['filter' => 'unread']) }}" 
                   class="px-4 py-2 rounded-lg font-medium transition-all {{ request('filter') === 'unread' ? 'bg-gradient-to-r from-orange-600 to-orange-700 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <span class="material-icons text-sm align-middle mr-1">mark_email_unread</span>
                    Unread
                </a>
                {{-- Read Notifications --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600 mb-1">Read</p>
            <h3 class="text-3xl font-bold text-green-600">
                @php
                    $readCount = $notifications->total() - ($unreadCount ?? 0);
                @endphp
                {{ $readCount }}
            </h3>
        </div>
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <span class="material-icons text-green-600 text-2xl">mark_email_read</span>
        </div>
    </div>
</div>
            </div>

            {{-- Bulk Actions --}}
            <div class="flex gap-2">
                 @if(($unreadCount ?? 0) > 0)
        <form action="{{ route(auth()->user()->role . '.notifications.read-all') }}" 
              method="POST" 
              onsubmit="return confirm('Mark all notifications as read?');">
            @csrf
            <button type="submit" 
                    class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all font-medium shadow-sm">
                <span class="material-icons text-sm align-middle mr-1">done_all</span>
                Mark All as Read
            </button>
        </form>
    @endif
                
               
            </div>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <div class="notification-item p-6 hover:bg-gray-50 transition-colors {{ $notification->is_read ? '' : 'bg-blue-50' }}">
                        <div class="flex items-start gap-4">
                            {{-- Unread Indicator --}}
                            <div class="flex-shrink-0 mt-1">
                                @if(!$notification->is_read)
                                    <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                @else
                                    <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                @endif
                            </div>

                            {{-- Notification Content --}}
                            <div class="flex-1 min-w-0">
                                {{-- Message --}}
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <p class="text-gray-800 font-medium leading-relaxed">
                                        {{ $notification->message }}
                                    </p>
                                    
                                    {{-- Status Badge --}}
                                    @if(!$notification->is_read)
                                        <span class="flex-shrink-0 px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                            NEW
                                        </span>
                                    @endif
                                </div>

                                {{-- Meta Information --}}
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-3">
                                    <span class="flex items-center">
                                        <span class="material-icons text-sm mr-1">schedule</span>
                                        {{ $notification->created_at->format('M d, Y h:i A') }}
                                        <span class="text-xs ml-1">({{ $notification->created_at->diffForHumans() }})</span>
                                    </span>
                                    
                                    @if($notification->sender)
                                        <span class="flex items-center">
                                            <span class="material-icons text-sm mr-1">person</span>
                                            <span class="font-medium">{{ $notification->sender->name }}</span>
                                            <span class="text-xs ml-1 px-2 py-0.5 bg-gray-100 rounded-full">
                                                {{ ucfirst($notification->sender_role) }}
                                            </span>
                                        </span>
                                    @endif

                                    @if($notification->receiver_role)
                                        <span class="flex items-center">
                                            <span class="material-icons text-sm mr-1">group</span>
                                            To: {{ ucfirst($notification->receiver_role) }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($notification->related_item_id)
                                        <a href="{{ route(auth()->user()->role . '.view-file-details', $notification->related_item_id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            <span class="material-icons text-sm mr-1">visibility</span>
                                            View Related File
                                        </a>
                                    @endif

                                    @if(!$notification->is_read)
                                        <form action="{{ route(auth()->user()->role . '.notifications.read', $notification->id) }}" 
                                              method="POST" 
                                              class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                                <span class="material-icons text-sm mr-1">check</span>
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button"
                                            onclick="deleteNotification({{ $notification->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                                        <span class="material-icons text-sm mr-1">delete</span>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $notifications->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-icons text-6xl text-gray-400">notifications_off</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Notifications Found</h3>
                <p class="text-gray-500 text-center max-w-md">
                    @if(request('filter') === 'unread')
                        You don't have any unread notifications at the moment.
                    @elseif(request('filter') === 'read')
                        You don't have any read notifications yet.
                    @else
                        You haven't received any notifications yet. They will appear here when you receive them.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

{{-- Send Notification Modal --}}
<div class="modal fade" id="sendNotificationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xl border-0 shadow-2xl">
            <form action="{{ route(auth()->user()->role . '.notifications.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-bold text-xl text-gray-800 flex items-center">
                        <span class="material-icons text-blue-600 mr-2">send</span>
                        Send Notification
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body px-6 py-4">
                    {{-- Send Type Selection --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Send To</label>
                        <select name="send_type" id="sendType" class="form-select w-full rounded-lg border-gray-300" required>
                            <option value="">Select recipient type</option>
                            <option value="role">Send to Role</option>
                            <option value="user">Send to Individual User</option>
                        </select>
                    </div>

                    {{-- Role Selection (shown when send_type = role) --}}
                    <div class="mb-4 hidden" id="roleSection">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Role</label>
                        <select name="receiver_role" class="form-select w-full rounded-lg border-gray-300">
                            <option value="">Choose role...</option>
                            <option value="all">All Users</option>
                            <option value="admin">Admin</option>
                            <option value="chairperson">Chairperson</option>
                            <option value="faculty">Faculty</option>
                        </select>
                    </div>

                    {{-- User Selection (shown when send_type = user) --}}
                    <div class="mb-4 hidden" id="userSection">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select User</label>
                        <select name="user_id" class="form-select w-full rounded-lg border-gray-300">
                            <option value="">Choose user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} - {{ ucfirst($user->role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Message --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                        <textarea name="message" 
                                  rows="4" 
                                  class="form-control w-full rounded-lg border-gray-300" 
                                  placeholder="Enter your notification message..."
                                  maxlength="1000"
                                  required></textarea>
                        <small class="text-gray-500">Maximum 1000 characters</small>
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" 
                            data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm">
                        <span class="material-icons text-sm align-middle mr-1">send</span>
                        Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Toggle between role and user selection
    document.getElementById('sendType').addEventListener('change', function() {
        const roleSection = document.getElementById('roleSection');
        const userSection = document.getElementById('userSection');
        
        if (this.value === 'role') {
            roleSection.classList.remove('hidden');
            userSection.classList.add('hidden');
            userSection.querySelector('select').removeAttribute('required');
            roleSection.querySelector('select').setAttribute('required', 'required');
        } else if (this.value === 'user') {
            userSection.classList.remove('hidden');
            roleSection.classList.add('hidden');
            roleSection.querySelector('select').removeAttribute('required');
            userSection.querySelector('select').setAttribute('required', 'required');
        } else {
            roleSection.classList.add('hidden');
            userSection.classList.add('hidden');
        }
    });

    // Delete notification function
    function deleteNotification(notificationId) {
        Swal.fire({
            title: 'Delete Notification?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/') }}/${document.querySelector('meta[name="user-role"]')?.content || 'dean'}/notifications/${notificationId}/delete`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Auto-refresh notifications (optional)
    setInterval(() => {
        location.reload();
    }, 60000); // Refresh every 60 seconds
</script>

{{-- Add user role meta tag for JavaScript --}}
<meta name="user-role" content="{{ auth()->user()->role }}">
@endpush