@extends('layouts.dean')

@section('page-title', 'Archived Files')

@section('content')
<div class="container-fluid mx-auto px-4">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded-lg shadow-sm">
            <div class="flex items-center">
                <span class="material-icons text-green-500 mr-2">check_circle</span>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-lg shadow-sm">
            <div class="flex items-center">
                <span class="material-icons text-red-500 mr-2">error</span>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">All Archived Documents</h1>
                <p class="text-gray-600 mt-1">Manage and access all uploaded documents from all roles</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('dean.upload_files') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition duration-150">
                    <span class="material-icons mr-2 text-sm">upload</span>
                    Upload New File
                </a>
            </div>
        </div>
    </div>

    <!-- Compact Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
            <!-- Search File -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Files</label>
                <div class="relative">
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Filename..." 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm">
                    <span class="material-icons absolute left-2 top-2 text-gray-400 text-sm">search</span>
                </div>
            </div>
            
            <!-- Search Uploader -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Uploader</label>
                <div class="relative">
                    <input type="text" 
                           id="uploaderSearchInput" 
                           placeholder="Uploader name..." 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm">
                    <span class="material-icons absolute left-2 top-2 text-gray-400 text-sm">person</span>
                </div>
            </div>
            
            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm">
                    <option value="">All Categories</option>
                    @if(isset($archivedFiles) && $archivedFiles->count() > 0)
                        @php
                            $categories = $archivedFiles->pluck('category')->unique('id')->filter();
                        @endphp
                        @foreach($categories as $category)
                            @if($category)
                                <option value="{{ strtolower($category->name) }}">{{ $category->name }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
            
            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="roleFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm">
                    <option value="">All Roles</option>
                    @if(isset($archivedFiles) && $archivedFiles->count() > 0)
                        @php
                            $roles = $archivedFiles->map(function($file) {
                                return $file->uploader->role ?? null;
                            })->unique()->filter();
                        @endphp
                        @foreach($roles as $role)
                            @if($role)
                                <option value="{{ strtolower($role) }}">{{ ucfirst($role) }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
            
            <!-- Program Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <select id="programFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm">
                    <option value="">All Programs</option>
                    @if(isset($archivedFiles) && $archivedFiles->count() > 0)
                        @php
                            $programs = $archivedFiles->map(function($file) {
                                return $file->uploader->program ?? null;
                            })->unique()->filter();
                        @endphp
                        @foreach($programs as $program)
                            @if($program)
                                <option value="{{ strtolower($program) }}">{{ $program }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
            
            <!-- Semester Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select id="semesterFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm">
                    <option value="">All Semesters</option>
                    @if(isset($archivedFiles) && $archivedFiles->count() > 0)
                        @php
                            $semesters = $archivedFiles->pluck('semester')->unique()->filter();
                        @endphp
                        @foreach($semesters as $semester)
                            @if($semester)
                                <option value="{{ strtolower($semester) }}">{{ ucfirst($semester) }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
            
            <!-- School Year Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">School Year</label>
                <select id="schoolYearFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm">
                    <option value="">All Years</option>
                    @if(isset($archivedFiles) && $archivedFiles->count() > 0)
                        @php
                            $schoolYears = $archivedFiles->pluck('school_year')->unique()->filter();
                        @endphp
                        @foreach($schoolYears as $schoolYear)
                            @if($schoolYear)
                                <option value="{{ strtolower($schoolYear) }}">{{ $schoolYear }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button onclick="applyFilters()" 
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition duration-200 text-sm">
                    Apply Filters
                </button>
                <button onclick="resetFilters()" 
                        class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition duration-200 text-sm">
                    Reset
                </button>
            </div>
        </div>
        
        <!-- Quick Date Filter Row -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quick Date Filter</label>
            <div class="flex flex-wrap gap-2">
                <button onclick="setDateFilter('today')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm">Today</button>
                <button onclick="setDateFilter('this_week')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm">This Week</button>
                <button onclick="setDateFilter('this_month')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm">This Month</button>
                <button onclick="setDateFilter('last_month')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm">Last Month</button>
                <button onclick="setDateFilter('this_year')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm">This Year</button>
                <button onclick="clearDateFilter()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm">Clear Date</button>
            </div>
        </div>
    </div>

    <!-- Files Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if(isset($archivedFiles) && $archivedFiles->count() > 0)
            <!-- Desktop Table View -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                File Info
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Uploader
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Details
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Upload Date
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="filesTableBody">
                        @foreach($archivedFiles as $file)
                            <tr class="hover:bg-gray-50 transition duration-150 file-row" 
                                data-filename="{{ strtolower($file->original_name ?? '') }}"
                                data-category="{{ strtolower($file->category->name ?? '') }}"
                                data-role="{{ strtolower($file->uploader->role ?? '') }}"
                                data-program="{{ strtolower($file->uploader->program ?? '') }}"
                                data-semester="{{ strtolower($file->semester ?? '') }}"
                                data-school-year="{{ strtolower($file->school_year ?? '') }}"
                                data-uploader="{{ strtolower($file->uploader->decrypted_name ?? '') }}"
                                data-date="{{ $file->created_at ? $file->created_at->format('Y-m-d') : '' }}"
                                data-status="{{ strtolower($file->status ?? 'active') }}">
                                
                                <!-- File Info Column -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @php
                                            $iconMap = [
                                                'pdf' => ['picture_as_pdf', 'text-red-500'],
                                                'doc' => ['description', 'text-blue-500'],
                                                'docx' => ['description', 'text-blue-500'],
                                                'xls' => ['table_chart', 'text-green-500'],
                                                'xlsx' => ['table_chart', 'text-green-500'],
                                                'ppt' => ['slideshow', 'text-orange-500'],
                                                'pptx' => ['slideshow', 'text-orange-500'],
                                                'jpg' => ['image', 'text-purple-500'],
                                                'jpeg' => ['image', 'text-purple-500'],
                                                'png' => ['image', 'text-purple-500'],
                                            ];
                                            $fileType = $file->file_type ?? 'unknown';
                                            $icon = $iconMap[$fileType] ?? ['insert_drive_file', 'text-gray-500'];
                                        @endphp
                                        <span class="material-icons {{ $icon[1] }} mr-3 text-lg">{{ $icon[0] }}</span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" title="{{ $file->original_name ?? 'Unknown' }}">
                                                {{ $file->original_name ?? 'Unknown' }}
                                            </p>
                                            <div class="flex items-center mt-1 space-x-2">
                                                @if($file->category)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $file->category->name }}
                                                    </span>
                                                @endif
                                                <span class="text-xs text-gray-500">{{ $file->formatted_file_size ?? '0 KB' }}</span>
                                                @if($file->is_compressed)
                                                    <span class="material-icons text-green-500 text-xs" title="Compressed">compress</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Uploader Column -->
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $file->uploader->decrypted_name ?? 'Unknown' }}
                                        </p>
                                        <div class="flex items-center mt-1 space-x-2">
                                            @php
                                                $roleColors = [
                                                    'admin' => 'bg-red-100 text-red-800',
                                                    'dean' => 'bg-purple-100 text-purple-800',
                                                    'chairperson' => 'bg-indigo-100 text-indigo-800',
                                                    'faculty' => 'bg-green-100 text-green-800',
                                                ];
                                                $roleColor = $roleColors[$file->uploader->role ?? ''] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $roleColor }}">
                                                {{ ucfirst($file->uploader->role ?? 'unknown') }}
                                            </span>
                                            @if($file->uploader->program)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    {{ $file->uploader->program }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Details Column -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col space-y-1">
                                        @if($file->semester)
                                            <div class="flex items-center">
                                                <span class="text-xs text-gray-500 w-16">Semester:</span>
                                                @php
                                                    $semesterColors = [
                                                        'first' => 'bg-blue-100 text-blue-800',
                                                        'second' => 'bg-green-100 text-green-800',
                                                        'summer' => 'bg-yellow-100 text-yellow-800',
                                                    ];
                                                    $semesterColor = $semesterColors[strtolower($file->semester)] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $semesterColor }} ml-2">
                                                    {{ ucfirst($file->semester) }}
                                                </span>
                                            </div>
                                        @endif
                                        @if($file->school_year)
                                            <div class="flex items-center">
                                                <span class="text-xs text-gray-500 w-16">School Year:</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 ml-2">
                                                    {{ $file->school_year }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Upload Date Column -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">
                                        {{ $file->created_at ? $file->created_at->format('M d, Y') : 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $file->created_at ? $file->created_at->format('h:i A') : '' }}
                                    </div>
                                </td>
                                
                                <!-- Actions Column -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        @if($file->id)
                                            <a href="{{ route('dean.view-file-details', $file->id) }}" 
                                               class="inline-flex items-center p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition duration-150"
                                               title="View Details">
                                                <span class="material-icons text-sm">visibility</span>
                                            </a>
                                            <a href="{{ route('dean.download-archive-file', $file->id) }}" 
                                               class="inline-flex items-center p-2 text-green-600 hover:bg-green-50 rounded-lg transition duration-150"
                                               title="Download">
                                                <span class="material-icons text-sm">download</span>
                                            </a>
                                            @if($file->status !== 'deleted')
                                                <button type="button" 
                                                        onclick="confirmDelete({{ $file->id }})"
                                                        class="inline-flex items-center p-2 text-red-600 hover:bg-red-50 rounded-lg transition duration-150"
                                                        title="Delete">
                                                    <span class="material-icons text-sm">delete</span>
                                                </button>
                                            @endif

                                            <form id="delete-form-{{ $file->id }}" 
                                                  action="{{ route('dean.delete-archive-file', $file->id) }}" 
                                                  method="POST" 
                                                  class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <span class="text-sm text-gray-400">No Actions</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-gray-200" id="filesCardView">
                @foreach($archivedFiles as $file)
                    <div class="p-4 file-card" 
                         data-filename="{{ strtolower($file->original_name ?? '') }}"
                         data-category="{{ strtolower($file->category->name ?? '') }}"
                         data-role="{{ strtolower($file->uploader->role ?? '') }}"
                         data-program="{{ strtolower($file->uploader->program ?? '') }}"
                         data-semester="{{ strtolower($file->semester ?? '') }}"
                         data-school-year="{{ strtolower($file->school_year ?? '') }}"
                         data-uploader="{{ strtolower($file->uploader->decrypted_name ?? '') }}"
                         data-date="{{ $file->created_at ? $file->created_at->format('Y-m-d') : '' }}"
                         data-status="{{ strtolower($file->status ?? 'active') }}">
                        <!-- Same mobile card content as before... -->
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($archivedFiles->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $archivedFiles->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <span class="material-icons text-gray-300 mb-4" style="font-size: 64px;">folder_open</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No archived files found</h3>
                <p class="text-gray-500 mb-4">Start uploading documents to see them here.</p>
                <a href="{{ route('dean.upload_files') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition duration-150">
                    <span class="material-icons mr-2 text-sm">upload</span>
                    Upload Files
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    let activeDateFilter = null;
    
    function confirmDelete(fileId) {
        if (confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
            document.getElementById('delete-form-' + fileId).submit();
        }
    }

    function setDateFilter(range) {
        activeDateFilter = range;
        applyFilters();
    }

    function clearDateFilter() {
        activeDateFilter = null;
        applyFilters();
    }

    function applyFilters() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const uploaderSearchTerm = document.getElementById('uploaderSearchInput').value.toLowerCase();
        const categoryValue = document.getElementById('categoryFilter').value.toLowerCase();
        const roleValue = document.getElementById('roleFilter').value.toLowerCase();
        const programValue = document.getElementById('programFilter').value.toLowerCase();
        const semesterValue = document.getElementById('semesterFilter').value.toLowerCase();
        const schoolYearValue = document.getElementById('schoolYearFilter').value.toLowerCase();
        
        const fileRows = document.querySelectorAll('.file-row');
        const fileCards = document.querySelectorAll('.file-card');

        fileRows.forEach(row => {
            const filename = row.dataset.filename || '';
            const uploader = row.dataset.uploader || '';
            const category = row.dataset.category || '';
            const role = row.dataset.role || '';
            const program = row.dataset.program || '';
            const semester = row.dataset.semester || '';
            const schoolYear = row.dataset.schoolYear || '';
            const fileDate = row.dataset.date || '';

            const matchesSearch = !searchTerm || filename.includes(searchTerm);
            const matchesUploader = !uploaderSearchTerm || uploader.includes(uploaderSearchTerm);
            const matchesCategory = !categoryValue || category.includes(categoryValue);
            const matchesRole = !roleValue || role.includes(roleValue);
            const matchesProgram = !programValue || program.includes(programValue);
            const matchesSemester = !semesterValue || semester.includes(semesterValue);
            const matchesSchoolYear = !schoolYearValue || schoolYear.includes(schoolYearValue);
            
            let matchesDate = true;
            if (activeDateFilter) {
                matchesDate = checkDateFilter(fileDate, activeDateFilter);
            }

            if (matchesSearch && matchesUploader && matchesCategory && matchesRole && 
                matchesProgram && matchesSemester && matchesSchoolYear && matchesDate) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Also filter mobile cards
        fileCards.forEach(card => {
            const filename = card.dataset.filename || '';
            const uploader = card.dataset.uploader || '';
            const category = card.dataset.category || '';
            const role = card.dataset.role || '';
            const program = card.dataset.program || '';
            const semester = card.dataset.semester || '';
            const schoolYear = card.dataset.schoolYear || '';
            const fileDate = card.dataset.date || '';

            const matchesSearch = !searchTerm || filename.includes(searchTerm);
            const matchesUploader = !uploaderSearchTerm || uploader.includes(uploaderSearchTerm);
            const matchesCategory = !categoryValue || category.includes(categoryValue);
            const matchesRole = !roleValue || role.includes(roleValue);
            const matchesProgram = !programValue || program.includes(programValue);
            const matchesSemester = !semesterValue || semester.includes(semesterValue);
            const matchesSchoolYear = !schoolYearValue || schoolYear.includes(schoolYearValue);
            
            let matchesDate = true;
            if (activeDateFilter) {
                matchesDate = checkDateFilter(fileDate, activeDateFilter);
            }

            if (matchesSearch && matchesUploader && matchesCategory && matchesRole && 
                matchesProgram && matchesSemester && matchesSchoolYear && matchesDate) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function checkDateFilter(fileDate, dateFilter) {
        if (!fileDate) return false;
        
        const fileDateObj = new Date(fileDate);
        const today = new Date();
        
        switch(dateFilter) {
            case 'today':
                return fileDateObj.toDateString() === today.toDateString();
            case 'this_week':
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                startOfWeek.setHours(0, 0, 0, 0);
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                endOfWeek.setHours(23, 59, 59, 999);
                return fileDateObj >= startOfWeek && fileDateObj <= endOfWeek;
            case 'this_month':
                return fileDateObj.getMonth() === today.getMonth() && 
                       fileDateObj.getFullYear() === today.getFullYear();
            case 'last_month':
                const lastMonth = today.getMonth() === 0 ? 11 : today.getMonth() - 1;
                const lastMonthYear = today.getMonth() === 0 ? today.getFullYear() - 1 : today.getFullYear();
                return fileDateObj.getMonth() === lastMonth && 
                       fileDateObj.getFullYear() === lastMonthYear;
            case 'this_year':
                return fileDateObj.getFullYear() === today.getFullYear();
            default:
                return true;
        }
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('uploaderSearchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('roleFilter').value = '';
        document.getElementById('programFilter').value = '';
        document.getElementById('semesterFilter').value = '';
        document.getElementById('schoolYearFilter').value = '';
        activeDateFilter = null;
        applyFilters();
    }

    // Add event listeners for real-time search on text inputs
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const uploaderSearchInput = document.getElementById('uploaderSearchInput');
        
        // Real-time filtering for search inputs
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 300);
        });
        
        uploaderSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 300);
        });
        
        // Select filters require apply button
        const selectFilters = ['categoryFilter', 'roleFilter', 'programFilter', 'semesterFilter', 'schoolYearFilter'];
        selectFilters.forEach(filterId => {
            document.getElementById(filterId).addEventListener('change', applyFilters);
        });
    });
</script>
@endpush
@endsection