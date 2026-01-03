<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Goal;
use App\Models\Category;
use App\Models\Achievement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // Overall Statistics
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalTasks = Task::count();
        $totalGoals = Goal::count();
        $totalAchievements = Achievement::count();

        // Task Statistics
        $completedTasks = Task::where('status', 'Completed')->count();
        $pendingTasks = Task::where('status', 'Pending')->count();
        $ongoingTasks = Task::where('status', 'Ongoing')->count();

        // User Activity (Last 7 days)
        $userActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $userActivity[] = [
                'date' => $date->format('M d'),
                'users' => User::whereDate('created_at', $date)->count(),
                'tasks' => Task::whereDate('created_at', $date)->count(),
            ];
        }

        // Top Active Users (by task count)
        $topUsers = User::withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->take(10)
            ->get();

        // Recent Users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Category Distribution
        $categoryStats = Category::withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->take(10)
            ->get();

        // Task completion rate by user
        $userCompletionRates = User::withCount([
            'tasks as total_tasks',
            'tasks as completed_tasks' => function ($query) {
                $query->where('status', 'Completed');
            }
        ])
            ->having('total_tasks', '>', 0)
            ->orderByDesc('completed_tasks')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_tasks' => $user->total_tasks,
                    'completed_tasks' => $user->completed_tasks,
                    'completion_rate' => $user->total_tasks > 0
                        ? round(($user->completed_tasks / $user->total_tasks) * 100, 1)
                        : 0
                ];
            });

        // Monthly Growth
        $monthlyGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyGrowth[] = [
                'month' => $month->format('M Y'),
                'users' => User::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
                'tasks' => Task::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'newUsersThisMonth',
            'totalTasks',
            'totalGoals',
            'totalAchievements',
            'completedTasks',
            'pendingTasks',
            'ongoingTasks',
            'userActivity',
            'topUsers',
            'recentUsers',
            'categoryStats',
            'userCompletionRates',
            'monthlyGrowth'
        ));
    }

    /**
     * Display all users
     */
    public function users(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->withCount(['tasks', 'goals', 'achievements'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate(20);

        return view('admin.users', compact('users', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * Show specific user details
     */
    public function userDetails($id)
    {
        $user = User::withCount([
            'tasks',
            'tasks as completed_tasks' => function ($query) {
                $query->where('status', 'Completed');
            },
            'goals',
            'categories',
            'achievements'
        ])->findOrFail($id);

        // Get user's recent tasks
        $recentTasks = Task::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get user's goals
        $goals = Goal::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's achievements
        $achievements = Achievement::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Task statistics
        $taskStats = [
            'total' => $user->tasks_count,
            'completed' => $user->completed_tasks,
            'pending' => Task::where('user_id', $id)->where('status', 'Pending')->count(),
            'ongoing' => Task::where('user_id', $id)->where('status', 'Ongoing')->count(),
        ];

        // Activity timeline (last 30 days)
        $activityTimeline = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $activityTimeline[] = [
                'date' => $date->format('M d'),
                'tasks_created' => Task::where('user_id', $id)
                    ->whereDate('created_at', $date)
                    ->count(),
                'tasks_completed' => Task::where('user_id', $id)
                    ->whereDate('completed_at', $date)
                    ->count(),
            ];
        }

        return view('admin.user-details', compact(
            'user',
            'recentTasks',
            'goals',
            'achievements',
            'taskStats',
            'activityTimeline'
        ));
    }

    /**
     * Toggle user admin status
     */
    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot modify your own admin status.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'granted' : 'revoked';
        return redirect()->back()->with('success', "Admin access {$status} for {$user->name}.");
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Delete user's data
        $user->tasks()->delete();
        $user->goals()->delete();
        $user->categories()->delete();
        $user->achievements()->delete();
        $user->notifications()->delete();

        // Delete user
        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('success', "User {$userName} deleted successfully.");
    }

    /**
     * View system statistics
     */
    public function statistics()
    {
        // Database size and records
        $stats = [
            'users' => User::count(),
            'tasks' => Task::count(),
            'goals' => Goal::count(),
            'categories' => Category::count(),
            'achievements' => Achievement::count(),
        ];

        // Platform usage statistics
        $platformStats = [
            'avg_tasks_per_user' => round(Task::count() / max(User::count(), 1), 2),
            'avg_goals_per_user' => round(Goal::count() / max(User::count(), 1), 2),
            'completion_rate' => Task::count() > 0
                ? round((Task::where('status', 'Completed')->count() / Task::count()) * 100, 2)
                : 0,
            'active_users_today' => Task::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
        ];

        return view('admin.statistics', compact('stats', 'platformStats'));
    }

    /**
     * ----------------------------
     * Admin Settings Methods
     * ----------------------------
     */

    /**
     * Show admin settings page
     */
    public function settings()
    {
        $admin = auth()->user();
        return view('admin.settings', compact('admin'));
    }

    /**
     * Update admin profile info
     */
    public function updateSettings(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();

        return redirect()->route('admin.settings')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admin.settings')->with('success', 'Password updated successfully.');
    }

    /**
     * Remove profile picture
     */
    public function removeProfilePicture()
    {
        $admin = auth()->user();

        if ($admin->profile_picture) {
            Storage::delete($admin->profile_picture);
            $admin->profile_picture = null;
            $admin->save();
        }

        return redirect()->route('admin.settings')->with('success', 'Profile picture removed successfully.');
    }
}
