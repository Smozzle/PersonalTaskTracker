<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MilestoneController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard - Auto redirect admins to admin dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return app(DashboardController::class)->index();
    })->name('dashboard');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Tasks (custom route BEFORE resource)
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'markAsDone'])
        ->name('tasks.markAsDone');

    Route::resource('tasks', TaskController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Achievements
    Route::get('/achievements', [AchievementController::class, 'index'])
        ->name('achievements.index');

    // Reminders
    Route::post('/reminders', [ReminderController::class, 'store']);

    // Goals
    Route::post('/goals/{goal}/ajax-update', [GoalController::class, 'ajaxUpdate'])
        ->name('goals.ajaxUpdate');

    Route::resource('goals', GoalController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.export-pdf');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');

    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');

    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
        ->name('notifications.unread-count');

    Route::post('/goals/{goal}/milestones', [MilestoneController::class, 'store'])->name('milestones.store');
    Route::post('/milestones/{milestone}/toggle', [MilestoneController::class, 'toggle'])->name('milestones.toggle');
    Route::put('/milestones/{milestone}', [MilestoneController::class, 'update'])->name('milestones.update');
    Route::delete('/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('milestones.destroy');
    Route::post('/goals/{goal}/milestones/reorder', [MilestoneController::class, 'reorder'])->name('milestones.reorder');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Visualization
|--------------------------------------------------------------------------
*/
Route::get('/visualization', [TaskController::class, 'visualization'])
    ->name('visualization.index')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| API Notifications (Protected)
|--------------------------------------------------------------------------
*/
Route::get('/api/notifications', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }

    return response()->json([
        'message' => 'Route is working!',
        'user' => auth()->user()->name,
        'notifications' => auth()->user()->unreadNotifications,
    ]);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Protected by auth + admin middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', [AdminController::class, 'users'])
            ->name('users');

        Route::get('/users/{id}', [AdminController::class, 'userDetails'])
            ->name('user-details');

        Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])
            ->name('toggle-admin');

        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])
            ->name('delete-user');

        Route::get('/statistics', [AdminController::class, 'statistics'])
            ->name('statistics');
    });