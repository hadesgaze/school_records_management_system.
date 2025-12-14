<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileTypeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChairController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Dean\DeanController;
use App\Http\Controllers\Dean\ProgramController;
use App\Http\Controllers\Chairperson\ChairpersonController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UploadArchiveController;
use App\Http\Controllers\Admin\CategoryFieldController;
use App\Http\Controllers\Faculty\FacultyController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', fn () => view('welcome'))->name('home');

// Quick cache/config/view clear (dev only)
Route::get('config', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:forget spatie.permission.cache');
    return "Cache and configs cleared!";
});

// =======================
// 🔹 AUTHENTICATION
// =======================
Route::get('/select-role', fn () => view('auth.select-role'))->name('select');

Route::get('/login/{role}', [LoginController::class, 'showRoleLogin'])->name('role.login');
Route::post('/login/{role}', [LoginController::class, 'roleLogin'])->name('role.login.submit');

  // User can view categories they have access to
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show']) ->middleware(CheckCategoryAccess::class)->name('categories.show');

// =======================
// 🔹 DASHBOARDS
// =======================

// Admin
Route::middleware(['auth', 'checkRole:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::match(['get','post'],'/profile', [AdminController::class,'profile'])->name('profile.manage');      
    });

// Dean
Route::middleware(['auth', 'checkRole:dean'])
    ->prefix('dean')
    ->group(fn () => Route::get('/dashboard', [DeanController::class, 'index'])->name('dean.dashboard'));

// Chairperson
Route::middleware(['auth', 'checkRole:chairperson'])
    ->prefix('chair')
    ->group(fn () => Route::get('/dashboard', [ChairController::class, 'index'])->name('chair.dashboard'));

// Logout route
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');










// In routes/web.php or your routes file

// Faculty Routes
Route::middleware(['auth', 'faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [FacultyController::class, 'dashboard'])->name('dashboard');
    Route::get('/upload-files', [FacultyController::class, 'uploadFiles'])->name('upload_files');
    
    // Category routes
    Route::post('/categories', [FacultyController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}', [FacultyController::class, 'showCategory'])->name('categories.show');
    Route::put('/categories/{category}', [FacultyController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [FacultyController::class, 'deleteCategory'])->name('categories.destroy');
    Route::get('/categories/{category}/fields', [FacultyController::class, 'getCategoryFields'])->name('categories.fields');
    
    // Field routes
    Route::delete('/fields/{field}', [FacultyController::class, 'deleteField'])->name('fields.destroy');

    // Archive Files
    Route::get('/archive-files', [FacultyController::class, 'archivedFiles'])->name('archive_files');
    Route::get('/archive-files/{id}', [FacultyController::class, 'viewFileDetails'])->name('view-file-details');
    Route::get('/archive-files/{id}/download', [FacultyController::class, 'downloadArchiveFile'])->name('download-archive-file');
    Route::delete('/archive-files/{id}', [FacultyController::class, 'deleteArchiveFile'])->name('delete-archive-file');
    Route::get('/view-file/{id}', [FacultyController::class, 'viewFile'])->name('view-file');
    
    
    // Document upload
    Route::post('/upload-document', [FacultyController::class, 'uploadDocument'])->name('upload.document');
    Route::get('/archived-files', [FacultyController::class, 'archivedFiles'])->name('archived_files');
    
    // Notification routes
    Route::get('/notifications', [FacultyController::class, 'notifications'])->name('notifications');
    Route::get('/fetch-notifications', [FacultyController::class, 'fetchNotifications'])->name('notifications.fetch');
    Route::post('/notifications/{id}/read', [FacultyController::class, 'markNotificationRead'])->name('notification.read');
    Route::post('/notifications/{id}/unread', [FacultyController::class, 'markNotificationUnread'])->name('notification.unread');
    Route::post('/notifications/mark-all-read', [FacultyController::class, 'markAllNotificationsRead'])->name('notification.mark_all_read');
    Route::delete('/notifications/{id}', [FacultyController::class, 'deleteNotification'])->name('notification.delete');
    
    // Settings and Profile
    Route::get('/settings', [FacultyController::class, 'settings'])->name('settings');
    Route::post('/profile/update', [FacultyController::class, 'updateProfile'])->name('settings.profile.update');
});















//Admin Side User Management

Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [UploadArchiveController::class, 'dashboard'])->name('dashboard');
        Route::get('/upload-files', [UploadArchiveController::class, 'uploadFiles'])->name('upload_files');
        Route::resource('users', UserManagementController::class);

        // Reports 
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    
        // Export Reports
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
        // Alternative route with query parameters
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export.post');
          // Category management
          Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    
    // Other category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

     // Archive Files
    Route::get('/archive-files', [UploadArchiveController::class, 'archivedFiles'])->name('archive_files');
    Route::get('/view-file/{id}', [UploadArchiveController::class, 'viewFile'])->name('view-file');
    Route::get('/file-details/{id}', [UploadArchiveController::class, 'viewFileDetails'])->name('view-file-details');
    Route::get('/archive-files/{id}/download', [UploadArchiveController::class, 'downloadArchiveFile'])->name('download-archive-file');
    Route::delete('/archive-files/{id}', [UploadArchiveController::class, 'deleteArchiveFile'])->name('delete-archive-file');
    Route::patch('/restore-archive-file/{id}', [UploadArchiveController::class, 'restoreArchiveFile'])->name('restore-archive-file');


     // Add these routes for category fields
     // Category Field routes
    Route::get('category-fields/create/{category}', [CategoryFieldController::class, 'create'])
        ->name('category-fields.create');
    Route::post('category-fields/{category}', [CategoryFieldController::class, 'store'])
        ->name('category-fields.store');
    Route::get('category-fields/{categoryField}/edit', [CategoryFieldController::class, 'edit'])
        ->name('category-fields.edit');
    Route::put('category-fields/{categoryField}', [CategoryFieldController::class, 'update'])
        ->name('category-fields.update');
    Route::delete('category-fields/{categoryField}', [CategoryFieldController::class, 'destroy'])
        ->name('category-fields.destroy');

    // Document upload
    Route::post('/upload-document', [UploadArchiveController::class, 'uploadDocument'])->name('upload.document');
    Route::get('/archived-files', [UploadArchiveController::class, 'archivedFiles'])->name('archived_files');

     // Archive Files
    Route::get('/archive-files', [UploadArchiveController::class, 'archivedFiles'])->name('archive_files');
    Route::get('/file-details/{id}', [UploadArchiveController::class, 'viewFileDetails'])->name('view-file-details');
    Route::get('/archive-files/{id}/download', [UploadArchiveController::class, 'downloadArchiveFile'])->name('download-archive-file');
    Route::delete('/archive-files/{id}', [UploadArchiveController::class, 'deleteArchiveFile'])->name('delete-archive-file');
    Route::patch('/restore-archive-file/{id}', [UploadArchiveController::class, 'restoreArchiveFile'])->name('restore-archive-file');
    
    // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        // User listing
        Route::get('/', [UserManagementController::class, 'index'])
            ->name('index');
        
        // Create user
        Route::get('/create', [UserManagementController::class, 'create'])
            ->name('create');
        Route::post('/', [UserManagementController::class, 'store'])
            ->name('store');
        
        // Show user profile with documents
        Route::get('/{user}', [UserManagementController::class, 'show'])
            ->name('show');
        
        // Edit user
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])
            ->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])
            ->name('update');
        
        // Delete user (optional - if you want to add it)
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])
            ->name('destroy');

});

    
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{log}', [AuditLogController::class, 'show'])->name('audit-logs.show');
     });
// Admin Notifications
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
    Route::post('/notifications/{id}/mark-read', [AdminNotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::get('/admin/notifications/fetch', [AdminNotificationController::class, 'fetch'])->name('notifications.fetch');

});
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
     Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('admin.settings.profile.update');
});

// Audit Logs Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');
    Route::get('/audit-logs/{log}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::delete('/admin/audit-logs/{id}', [AuditLogController::class, 'destroy'])->name('audit-logs.destroy');
});
















// Dean Routes

Route::prefix('dean')->name('dean.')->group(function () {
    Route::get('/dashboard', [DeanController::class, 'dashboard'])->name('dashboard');
    Route::get('/upload-files', [DeanController::class, 'uploadFiles'])->name('upload_files');
    Route::get('/archived-files', [DeanController::class, 'archivedFiles'])->name('archived_files');

    // Category routes
    Route::post('/categories', [DeanController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}', [DeanController::class, 'showCategory'])->name('categories.show');
    Route::put('/categories/{category}', [DeanController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [DeanController::class, 'deleteCategory'])->name('categories.destroy');
    Route::get('/categories/{category}/fields', [DeanController::class, 'getCategoryFields'])->name('categories.fields');
    
    // Field routes
    Route::delete('/fields/{field}', [DeanController::class, 'deleteField'])->name('fields.destroy');

    // Archive Files
    Route::get('/archive-files', [DeanController::class, 'archivedFiles'])->name('archive_files');
    Route::get('/archive-files/{id}', [DeanController::class, 'viewFileDetails'])->name('view-file-details');
    Route::get('/archive-files/{id}/download', [DeanController::class, 'downloadArchiveFile'])->name('download-archive-file');
    Route::delete('/archive-files/{id}', [DeanController::class, 'deleteArchiveFile'])->name('delete-archive-file');
    Route::get('/view-file/{id}', [DeanController::class, 'viewFile'])->name('view-file');
    
    
    // Document upload
    Route::post('/upload-document', [DeanController::class, 'uploadDocument'])->name('upload.document');
    Route::get('/archived-files', [DeanController::class, 'archivedFiles'])->name('archived_files');

    
    // Profile & Password
     Route::get('/settings', [DeanController::class, 'settings'])->name('settings');;
    Route::post('/profile', [DeanController::class,'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [DeanController::class,'updatePassword'])->name('profile.password');

    // Notifications - ADD THE NOTIFICATIONS PREFIX
    Route::get('/notifications', [DeanController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/store', [DeanController::class, 'storeNotification'])->name('notifications.store');
    Route::post('/notifications/{id}/read', [DeanController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [DeanController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    Route::get('/notifications/fetch', [DeanController::class, 'fetchNotifications'])->name('notifications.fetch');


});
    























// Chairperson Routes
Route::middleware(['auth', 'role:chairperson'])->prefix('chairperson')->name('chairperson.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [ChairpersonController::class, 'dashboard'])->name('dashboard');
    Route::get('/upload-files', [ChairpersonController::class, 'uploadFiles'])->name('upload_files');
   
    // Notifications
    Route::get('/notifications', [ChairpersonController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/store', [ChairpersonController::class, 'storeNotification'])->name('notifications.store');
    Route::post('/notifications/{id}/read', [ChairpersonController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [ChairpersonController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    Route::get('/notifications/fetch', [ChairpersonController::class, 'fetchNotifications'])->name('notifications.fetch');

    // Profile
    Route::get('/profile', [ChairpersonController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [ChairpersonController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ChairpersonController::class, 'updatePassword'])->name('profile.password');

    // Document upload
    Route::post('/upload-document', [ChairpersonController::class, 'uploadDocument'])->name('upload.document');
    Route::get('/archived-files', [ChairpersonController::class, 'archivedFiles'])->name('archived_files');

     // Category routes
    Route::post('/categories', [ChairpersonController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}', [ChairpersonController::class, 'showCategory'])->name('categories.show');
    Route::put('/categories/{category}', [ChairpersonController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [ChairpersonController::class, 'deleteCategory'])->name('categories.destroy');
    Route::get('/categories/{category}/fields', [ChairpersonController::class, 'getCategoryFields'])->name('categories.fields');
    
    // Field routes
    Route::delete('/fields/{field}', [ChairpersonController::class, 'deleteField'])->name('fields.destroy');

    // Archive Files
    Route::get('/archive-files', [ChairpersonController::class, 'archivedFiles'])->name('archive_files');
    Route::get('/file-details/{id}', [ChairpersonController::class, 'viewFileDetails'])->name('view-file-details');
    Route::get('/archive-files/{id}/download', [ChairpersonController::class, 'downloadArchiveFile'])->name('download-archive-file');
    Route::delete('/archive-files/{id}', [ChairpersonController::class, 'deleteArchiveFile'])->name('delete-archive-file');
    Route::patch('/restore-archive-file/{id}', [ChairpersonController::class, 'restoreArchiveFile'])->name('restore-archive-file');
    Route::get('/view-file/{id}', [ChairpersonController::class, 'viewFile'])->name('view-file');

    // Settings and Profile
    Route::get('/settings', [ChairpersonController::class, 'settings'])->name('settings');
    Route::post('/profile/update', [ChairpersonController::class, 'updateProfile'])->name('profile.update');
});