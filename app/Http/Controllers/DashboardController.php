<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Count tasks
        $totalTasks = Task::where('user_id', $userId)->count();
        $completedTasks = Task::where('user_id', $userId)->where('status', 'Completed')->count();
        $pendingTasks = Task::where('user_id', $userId)->where('status', 'Pending')->count();

        return view('dashboard', compact('totalTasks', 'completedTasks', 'pendingTasks'));
    }
}
