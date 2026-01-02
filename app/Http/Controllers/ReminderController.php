<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    /**
     * Store a new reminder for a specific task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'remind_at' => 'required|date|after:now',
            'type' => 'required|in:popup,email',
        ]);

        $task = Task::findOrFail($validated['task_id']);

        // ✅ Ensure the task belongs to the logged-in user
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to set reminder for this task.');
        }

        $validated['user_id'] = Auth::id();

        Reminder::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Reminder set successfully!');
    }

    /**
     * Display a list of reminders for the authenticated user.
     */
    public function index()
    {
        $reminders = Reminder::where('user_id', Auth::id())
            ->with('task')
            ->orderBy('remind_at', 'asc')
            ->get();

        return view('reminders.index', compact('reminders'));
    }

    /**
     * Delete a specific reminder.
     */
    public function destroy(Reminder $reminder)
    {
        // ✅ Check ownership
        if ($reminder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $reminder->delete();

        return redirect()
            ->back()
            ->with('info', 'Reminder deleted successfully.');
    }
}
