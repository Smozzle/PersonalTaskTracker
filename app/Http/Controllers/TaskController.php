<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use App\Models\Achievement;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskCompleted;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display all tasks belonging to the authenticated user.
     */
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())->get();

        $today = now()->startOfDay();

        $tasks = Task::where('user_id', Auth::id())
            ->orderByRaw("
            CASE
                WHEN status = 'Completed' THEN 3
                WHEN due_date IS NOT NULL AND due_date < ? THEN 0
                WHEN due_date IS NOT NULL THEN 1
                ELSE 2
            END
        ", [$today])
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tasks.index', compact('tasks', 'categories'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        return view('tasks.create', compact('categories'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:High,Medium,Low',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'Pending';

        // Create the task
        $task = Task::create($validated);

        // 🔔 SEND NOTIFICATION - NEW CODE
        try {
            Auth::user()->notify(new TaskAssigned($task));
        } catch (\Exception $e) {
            \Log::error('Failed to send TaskAssigned notification: ' . $e->getMessage());
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Display a specific task.
     */
    public function show(Task $task)
    {
        // Check authorization
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing a task.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $categories = Category::where('user_id', Auth::id())->get();

        return view('tasks.edit', compact('task', 'categories'));
    }

    /**
     * ✅ Unified Update Method (with validation + achievements)
     */
    public function update(Request $request, Task $task)
    {
        // Check authorization
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 🔔 SAVE OLD STATUS - NEW CODE
        $oldStatus = $task->status;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:High,Medium,Low',
            'status' => 'required|in:Pending,Ongoing,Completed',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Update the task
        $task->title = $validated['title'];
        $task->description = $validated['description'] ?? null;
        $task->due_date = $validated['due_date'] ?? null;
        $task->priority = $validated['priority'];
        $task->status = $validated['status'];
        $task->category_id = $validated['category_id'] ?? null;

        // 🔔 SET COMPLETED_AT IF STATUS CHANGED TO COMPLETED - NEW CODE
        if ($validated['status'] === 'Completed' && $oldStatus !== 'Completed') {
            $task->completed_at = now();
        }

        $task->save();

        // 🔔 SEND NOTIFICATION WHEN COMPLETED - NEW CODE
        if ($validated['status'] === 'Completed' && $oldStatus !== 'Completed') {
            try {
                $task->user->notify(new TaskCompleted($task));
            } catch (\Exception $e) {
                \Log::error('Failed to send TaskCompleted notification: ' . $e->getMessage());
            }
        }

        // 🏆 Achievement logic - only when task is completed
        if ($validated['status'] === 'Completed') {
            $user = auth()->user();
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();

            // ✅ Check streak (2 consecutive completion days)
            $completedYesterday = $user->tasks()
                ->whereDate('updated_at', $yesterday)
                ->where('status', 'Completed')
                ->exists();

            if ($completedYesterday) {
                Achievement::firstOrCreate([
                    'user_id' => $user->id,
                    'type' => 'streak',
                    'title' => '2-Day Streak',
                ], [
                    'description' => 'Completed tasks for 2 consecutive days!',
                    'icon' => 'fa-fire',
                ]);
            }

            // ✅ Check total completed tasks
            $completedCount = $user->tasks()->where('status', 'Completed')->count();

            if ($completedCount == 10) {
                Achievement::firstOrCreate([
                    'user_id' => $user->id,
                    'type' => 'milestone',
                    'title' => 'Task Master (10 Tasks)',
                ], [
                    'description' => 'Completed 10 tasks in total!',
                    'icon' => 'fa-medal',
                ]);
            }
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    /**
     * Mark a task as completed.
     */
    public function markAsDone(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 🔔 SAVE OLD STATUS - NEW CODE
        $oldStatus = $task->status;

        // Update status and completed_at
        $task->status = 'Completed';
        $task->completed_at = now();
        $task->save();

        // 🔔 SEND NOTIFICATION - NEW CODE
        if ($oldStatus !== 'Completed') {
            try {
                $task->user->notify(new TaskCompleted($task));
            } catch (\Exception $e) {
                \Log::error('Failed to send TaskCompleted notification: ' . $e->getMessage());
            }
        }

        // 🏆 Achievement logic
        $user = auth()->user();
        $yesterday = Carbon::yesterday();

        // ✅ Check streak (2 consecutive completion days)
        $completedYesterday = $user->tasks()
            ->whereDate('updated_at', $yesterday)
            ->where('status', 'Completed')
            ->exists();

        if ($completedYesterday) {
            Achievement::firstOrCreate([
                'user_id' => $user->id,
                'type' => 'streak',
                'title' => '2-Day Streak',
            ], [
                'description' => 'Completed tasks for 2 consecutive days!',
                'icon' => 'fa-fire',
            ]);
        }

        // ✅ Check total completed tasks
        $completedCount = $user->tasks()->where('status', 'Completed')->count();

        if ($completedCount == 10) {
            Achievement::firstOrCreate([
                'user_id' => $user->id,
                'type' => 'milestone',
                'title' => 'Task Master (10 Tasks)',
            ], [
                'description' => 'Completed 10 tasks in total!',
                'icon' => 'fa-medal',
            ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task marked as completed!');
    }

    /**
     * Remove a task from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }

    /**
     * Data visualization for tasks.
     */
    public function visualization()
    {
        // ✅ Fetch task counts by status for the authenticated user
        $completed = Task::where('user_id', Auth::id())->where('status', 'Completed')->count();
        $ongoing = Task::where('user_id', Auth::id())->where('status', 'Ongoing')->count();
        $pending = Task::where('user_id', Auth::id())->where('status', 'Pending')->count();

        // ✅ Fetch number of tasks per category for the authenticated user
        $categories = Category::where('user_id', Auth::id())
            ->withCount([
                'tasks' => function ($query) {
                    $query->where('user_id', Auth::id());
                }
            ])
            ->get();

        $categoryNames = $categories->pluck('name');
        $categoryCounts = $categories->pluck('tasks_count');

        // ✅ Send all data to the view
        return view('visualization.index', compact(
            'completed',
            'ongoing',
            'pending',
            'categoryNames',
            'categoryCounts'
        ));
    }

    public function reports()
    {
        $tasks = Task::where('user_id', Auth::id())->get();

        return view('reports.index', compact('tasks'));
    }

}