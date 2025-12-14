<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Dean - Document Management System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .gradient-bg {
            background: linear-gradient(90deg, #3542A1 8%, #FFF873 100%, #E8E872 100%);
        }
        .gradient-text {
            background: linear-gradient(90deg, #3542A1 8%, #b8b206 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .active-menu {
            background: linear-gradient(90deg, #3542A1 8%, #FFF873 100%, #E8E872 100%) !important;
            color: white !important;
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 4px;
        }
        .page-title {
            background: linear-gradient(90deg, #3542A1 8%, #b8b206 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="flex bg-gray-50 min-h-screen">

    <aside id="sidebar" class="w-64 bg-white shadow-xl flex flex-col fixed inset-y-0 left-0 transform -translate-x-full sm:translate-x-0 transition-transform duration-300 z-50">
        
        <div class="flex flex-col items-center pt-8 pb-4 border-b border-gray-100">
            
            <div class="mb-4">
                @if(file_exists(public_path('images/logo.PNG')))
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto object-contain drop-shadow-md">
                @elseif(file_exists(public_path('public/images/logo.PNG')))
                    <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="h-16 w-auto object-contain drop-shadow-md">
                @else
                    <div class="h-16 w-16 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                        DE
                    </div>
                @endif
            </div>

            <div class="mb-3">
                @if(Auth::user()?->profile_picture)
                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                        alt="Profile"
                        class="w-20 h-20 rounded-full border-4 border-gray-100 shadow-md object-cover">
                @else
                    <div class="w-20 h-20 rounded-full border-4 border-gray-100 shadow-md bg-gradient-to-r from-blue-100 to-purple-100 flex items-center justify-center">
                        <i class="bi bi-person text-3xl text-blue-600"></i>
                    </div>
                @endif
            </div>

            <div class="text-center px-4">
                <p class="text-gray-800 font-bold text-lg leading-tight">
                    {{ Auth::user()->name ?? 'User' }}
                </p>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mt-1">
                    Dean
                </p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 overflow-y-auto custom-scrollbar">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dean.dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('dean.dashboard') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('dean.dashboard') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">dashboard</span> 
                        Dashboard
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('dean.upload_files') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('dean.upload_files') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('dean.upload_files') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">upload</span> 
                        Upload Files
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('dean.archived_files') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('dean.archived_files') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('dean.archived_files') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">folder_open</span> 
                        Archived Files
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col sm:ml-64 transition-all duration-300 min-h-screen">
        
        <header class="bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-6 py-4 sticky top-0 z-40">
            <div class="flex items-center">
                <button id="sidebarToggle" class="sm:hidden text-gray-600 hover:text-gray-800 focus:outline-none mr-4 transition-colors">
                    <span class="material-icons text-2xl">menu</span>
                </button>
                <div class="flex flex-col">
                    <h1 class="text-2xl font-bold page-title">
                        @hasSection('page-title')
                            @yield('page-title')
                        @else
                            Dean Dashboard
                        @endif
                    </h1>
                    @hasSection('page-subtitle')
                        <p class="text-sm text-gray-600 mt-1">
                            @yield('page-subtitle')
                        </p>
                    @endif
                </div>
            </div>

<div class="flex items-center space-x-4">
    {{-- Notification Bell --}}
    <div class="relative notification-dropdown">
        <button id="notificationBell" 
                class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition-colors focus:outline-none">
            <span class="material-icons text-2xl">notifications</span>
            @if(isset($unreadCount) && $unreadCount > 0)
                <span id="notificationCount" 
                      class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full transform translate-x-1 -translate-y-1">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @else
                <span id="notificationCount" 
                      class="hidden absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full transform translate-x-1 -translate-y-1">
                    0
                </span>
            @endif
        </button>

        {{-- Notification Dropdown --}}
        <div id="notificationDropdown" 
             class="hidden absolute right-0 top-full mt-2 w-96 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <span class="material-icons text-blue-600 mr-2">notifications_active</span>
                    Notifications
                </h3>
                <a href="{{ route(auth()->user()->role . '.notifications') }}" 
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    View All
                </a>
            </div>

            {{-- Notification List --}}
            <div id="notificationList" class="max-h-96 overflow-y-auto custom-scrollbar">
                @if(isset($notifications) && $notifications->count() > 0)
                    @foreach($notifications->take(5) as $notification)
                        <div class="notification-item px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 {{ $notification->is_read ? '' : 'bg-blue-50' }}">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-2 mb-1">
                                        @if(!$notification->is_read)
                                            <span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-1.5"></span>
                                        @endif
                                        <p class="text-sm text-gray-800 leading-relaxed">
                                            {{ $notification->message }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 ml-4">
                                        <span class="flex items-center">
                                            <span class="material-icons text-xs mr-1">schedule</span>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if($notification->sender)
                                            <span class="flex items-center">
                                                <span class="material-icons text-xs mr-1">person</span>
                                                {{ $notification->sender->name }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($notification->related_item_id)
                                        <a href="{{ route(auth()->user()->role . '.view.file.details', $notification->related_item_id) }}" 
                                           class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 mt-2 font-medium transition-colors">
                                            <span class="material-icons text-sm mr-1">visibility</span>
                                            View File
                                        </a>
                                    @endif
                                </div>
                                
                                @if(!$notification->is_read)
                                    <form action="{{ route(auth()->user()->role . '.notifications.read', $notification->id) }}" 
                                          method="POST" class="flex-shrink-0 mark-read-form">
                                        @csrf
                                        <button type="submit" 
                                                class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                title="Mark as read">
                                            <span class="material-icons text-lg">check_circle</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center py-12 px-4">
                        <span class="material-icons text-6xl text-gray-300 mb-3">notifications_off</span>
                        <p class="text-gray-500 text-sm">No new notifications</p>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            @if(isset($unreadCount) && $unreadCount > 0)
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    <form action="{{ route(auth()->user()->role . '.notifications.read-all') }}" 
                          method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-medium text-sm shadow-sm">
                            <span class="material-icons text-sm mr-2 align-middle">done_all</span>
                            Mark All as Read
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

            <div class="flex items-center space-x-4">
                
                <div class="dropdown relative">
                    <button id="userMenuButton" class="flex items-center space-x-3 focus:outline-none group p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        @if (Auth::user()?->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                class="rounded-full border-2 border-gray-200 w-10 h-10 object-cover group-hover:border-blue-500 transition-colors" alt="Profile">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-100 to-purple-100 text-blue-600 flex items-center justify-center border-2 border-gray-200 group-hover:border-blue-500 transition-colors">
                                <span class="font-bold text-lg">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500">Dean</p>
                        </div>
                        <span class="material-icons text-gray-400 group-hover:text-gray-600">expand_more</span>
                    </button>
                    
                    <div id="userDropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <a href="{{ route('dean.settings') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm transition-colors">
                            <span class="material-icons text-sm mr-3 text-gray-400">settings</span>
                            Profile Settings
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <a class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 text-sm transition-colors" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                            <span class="material-icons text-sm mr-3">logout</span>
                            Sign Out
                        </a>
                        <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 overflow-x-hidden bg-gray-50/50">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg shadow-sm animate-fade-in">
                    <div class="flex items-center">
                        <span class="material-icons text-green-500 mr-3">check_circle</span>
                        <div>
                            <p class="text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-sm animate-fade-in">
                    <div class="flex items-center">
                        <span class="material-icons text-red-500 mr-3">error</span>
                        <div>
                            <p class="text-red-700 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // UI Interaction Scripts
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const userMenuButton = document.getElementById('userMenuButton');
        const userDropdown = document.getElementById('userDropdown');

        // Sidebar Toggle (Mobile)
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Dropdown Toggle
        userMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                if (window.innerWidth < 640) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #userDropdown {
            animation: dropdownFade 0.2s ease-in-out;
        }

        @keyframes dropdownFade {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
    // Notification Bell Toggle
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');

    notificationBell.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
        
        // Close user dropdown if open
        const userDropdown = document.getElementById('userDropdown');
        if (userDropdown && !userDropdown.classList.contains('hidden')) {
            userDropdown.classList.add('hidden');
        }
    });

    // Close notification dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });

    // Fetch notifications via AJAX
    function fetchNotifications() {
        const role = '{{ auth()->user()->role }}';
        const url = `/${role}/notifications/fetch`;
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update notification count
            const countBadge = document.getElementById('notificationCount');
            if (countBadge) {
                if (data.unreadCount > 0) {
                    countBadge.textContent = data.unreadCount > 99 ? '99+' : data.unreadCount;
                    countBadge.classList.remove('hidden');
                } else {
                    countBadge.classList.add('hidden');
                }
            }
            
            // Update notification list
            const notificationList = document.getElementById('notificationList');
            if (notificationList && data.notifications) {
                if (data.notifications.length > 0) {
                    let html = '';
                    data.notifications.forEach(notification => {
                        const readClass = notification.is_read ? '' : 'bg-blue-50';
                        const unreadDot = !notification.is_read 
                            ? '<span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-1.5"></span>' 
                            : '';
                        
                        html += `
                            <div class="notification-item px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 ${readClass}">
                                <div class="flex justify-between items-start gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start gap-2 mb-1">
                                            ${unreadDot}
                                            <p class="text-sm text-gray-800 leading-relaxed">
                                                ${notification.message}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-gray-500 ${!notification.is_read ? 'ml-4' : ''}">
                                            <span class="flex items-center">
                                                <span class="material-icons text-xs mr-1">schedule</span>
                                                ${notification.created_at}
                                            </span>
                                            ${notification.sender_name ? `
                                                <span class="flex items-center">
                                                    <span class="material-icons text-xs mr-1">person</span>
                                                    ${notification.sender_name}
                                                </span>
                                            ` : ''}
                                        </div>
                                        ${notification.file_id ? `
                                            <a href="/${role}/view-file-details/${notification.file_id}" 
                                               class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 mt-2 font-medium transition-colors">
                                                <span class="material-icons text-sm mr-1">visibility</span>
                                                View File
                                            </a>
                                        ` : ''}
                                    </div>
                                    ${!notification.is_read ? `
                                        <form action="/${role}/notifications/${notification.id}/read" 
                                              method="POST" class="flex-shrink-0 mark-read-form">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" 
                                                    class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                    title="Mark as read">
                                                <span class="material-icons text-lg">check_circle</span>
                                            </button>
                                        </form>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    });
                    notificationList.innerHTML = html;
                } else {
                    notificationList.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-12 px-4">
                            <span class="material-icons text-6xl text-gray-300 mb-3">notifications_off</span>
                            <p class="text-gray-500 text-sm">No new notifications</p>
                        </div>
                    `;
                }
            }
        })
        .catch(error => console.error('Error fetching notifications:', error));
    }

    // Fetch notifications every 30 seconds
    setInterval(fetchNotifications, 30000);

    // Initial fetch on page load
    document.addEventListener('DOMContentLoaded', fetchNotifications);

    // Handle mark as read forms with AJAX
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('mark-read-form')) {
            e.preventDefault();
            
            fetch(e.target.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(() => {
                // Refresh notifications after marking as read
                fetchNotifications();
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }
    });
</script>

<style>
    /* Animation for notification dropdown */
    #notificationDropdown {
        animation: dropdownFade 0.2s ease-in-out;
    }

    /* Smooth scrollbar for notifications */
    .notification-dropdown .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .notification-dropdown .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notification-dropdown .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .notification-dropdown .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Notification item hover effect */
    .notification-item {
        transition: all 0.2s ease;
    }

    /* Pulse animation for notification badge */
    @keyframes pulse {
        0%, 100% { transform: translate(0.25rem, -0.25rem) scale(1); }
        50% { transform: translate(0.25rem, -0.25rem) scale(1.1); }
    }

    #notificationCount {
        animation: pulse 2s infinite;
    }
</style>

    @stack('scripts')
</body>
</html>