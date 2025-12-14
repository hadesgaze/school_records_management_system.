<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Admin - Document Management System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            border-radius: 9999px;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 600;
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        #userDropdown, #notificationDropdown {
            animation: dropdownFade 0.2s ease-in-out;
        }
        @keyframes dropdownFade {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex bg-gray-50 min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white shadow-xl flex flex-col fixed inset-y-0 left-0 transform -translate-x-full sm:translate-x-0 transition-transform duration-300 z-50">
        
        <div class="flex flex-col items-center pt-8 pb-4 border-b border-gray-100">
            
            <div class="mb-4">
                @if(file_exists(public_path('images/logo.PNG')))
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto object-contain drop-shadow-md">
                @elseif(file_exists(public_path('public/images/logo.PNG')))
                    <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="h-16 w-auto object-contain drop-shadow-md">
                @else
                    <div class="h-16 w-16 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                        SRMS
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
                        <i class="bi bi-shield-lock text-3xl text-blue-600"></i>
                    </div>
                @endif
            </div>

            <div class="text-center px-4">
                <p class="text-gray-800 font-bold text-lg leading-tight">
                    {{ Auth::user()->name ?? 'Admin' }}
                </p>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mt-1">
                    Administrator
                </p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 overflow-y-auto custom-scrollbar">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('admin.dashboard') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('admin.dashboard') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">dashboard</span> 
                        Dashboard
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('admin.users.*') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('admin.users.*') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">people</span> 
                        Manage Users
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.reports') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('admin.reports.*') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('admin.reports.*') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">bar_chart</span> 
                        Reports
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('admin.categories.*') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('admin.categories.*') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">category</span> 
                        Categories
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.upload_files') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('admin.upload_files') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('admin.upload_files') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">upload</span> 
                        Upload Files
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.archived_files') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('admin.archived_files') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('admin.archived_files') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">folder_open</span> 
                        Archived Files
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.audit-logs') }}"
                    class="flex items-center px-4 py-3 rounded-lg font-medium transition duration-200 group
                    {{ request()->routeIs('admin.audit-logs.*') ? 'active-menu shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-700' }}">
                        <span class="material-icons mr-3 text-xl {{ !request()->routeIs('admin.audit-logs.*') ? 'text-gray-400 group-hover:text-blue-700' : '' }}">history</span> 
                        Audit Logs
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col sm:ml-64 transition-all duration-300 min-h-screen">
        
        <!-- Header -->
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
                            Admin Dashboard
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
                <!-- User Menu -->
                <div class="dropdown relative">
                    <button id="userMenuButton" class="flex items-center space-x-3 focus:outline-none group p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        @if (Auth::user()?->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                class="rounded-full border-2 border-gray-200 w-10 h-10 object-cover group-hover:border-blue-500 transition-colors" alt="Profile">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-100 to-purple-100 text-blue-600 flex items-center justify-center border-2 border-gray-200 group-hover:border-blue-500 transition-colors">
                                <span class="font-bold text-lg">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</p>
                        </div>
                        <span class="material-icons text-gray-400 group-hover:text-gray-600">expand_more</span>
                    </button>
                    
                    <div id="userDropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm transition-colors">
                            <span class="material-icons text-sm mr-3 text-gray-400">settings</span>
                            Settings
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

        <!-- Main Content -->
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
        // Sidebar Toggle (Mobile)
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // User Menu Dropdown
        const userMenuButton = document.getElementById('userMenuButton');
        const userDropdown = document.getElementById('userDropdown');

        userMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
            notificationDropdown.classList.add('hidden');
        });

        // Notification Dropdown
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');

        notificationButton.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
            userDropdown.classList.add('hidden');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                if (window.innerWidth < 640) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
            if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Live Notification Fetch
        document.addEventListener("DOMContentLoaded", () => {
            const notifList = document.getElementById('notifList');
            const notifBadge = document.getElementById('notifBadge');
            const notifIcon = document.getElementById('notifIcon');
            const notifCount = document.getElementById('notifCount');

            async function fetchNotifications() {
                try {
                    const res = await fetch("{{ route('admin.notifications.fetch') }}");
                    const data = await res.json();

                    // Update badge
                    if (data.unreadCount > 0) {
                        notifBadge.textContent = data.unreadCount;
                        notifBadge.classList.remove('d-none');
                        notifIcon.classList.add('text-red-600');
                        notifCount.textContent = `${data.unreadCount} unread`;
                    } else {
                        notifBadge.classList.add('d-none');
                        notifIcon.classList.remove('text-red-600');
                        notifCount.textContent = 'All caught up';
                    }

                    // Populate notification list
                    if (data.notifications.length > 0) {
                        notifList.innerHTML = "";
                        data.notifications.forEach(n => {
                            const item = document.createElement('div');
                            item.className = `px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors ${n.is_read ? '' : 'bg-blue-50'}`;
                            item.innerHTML = `
                                <a href="{{ route('admin.notifications') }}" class="block">
                                    <p class="text-sm ${n.is_read ? 'text-gray-700' : 'font-semibold text-gray-900'}">${n.message}</p>
                                    <p class="text-xs text-gray-500 mt-1">${new Date(n.created_at).toLocaleString()}</p>
                                </a>
                            `;
                            notifList.appendChild(item);
                        });
                    } else {
                        notifList.innerHTML = `<p class="text-center text-gray-500 py-8 text-sm">No notifications yet</p>`;
                    }
                } catch (error) {
                    console.error("Error fetching notifications:", error);
                    notifList.innerHTML = `<p class="text-center text-red-500 py-8 text-sm">Failed to load notifications</p>`;
                }
            }

            // Fetch immediately and refresh every 30 seconds
            fetchNotifications();
            setInterval(fetchNotifications, 30000);
        });
    </script>

    @stack('scripts')
</body>
</html>