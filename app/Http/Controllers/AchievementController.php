<?php

namespace App\Http\Controllers;

class AchievementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $completedTasks = $user->tasks()->where('status', 'Completed')->count();

        // Define badges dynamically
        $achievementList = collect([
            [
                'icon' => 'fa-flag',
                'title' => 'First Task',
                'description' => 'Created your first task. Way to start!',
                'goal' => 1,
            ],
            [
                'icon' => 'fa-fire',
                'title' => '2-Day Streak',
                'description' => 'Completed tasks for 2 consecutive days!',
                'goal' => 2,
            ],
            [
                'icon' => 'fa-medal',
                'title' => 'Task Master (10)',
                'description' => 'Completed 10 tasks in total!',
                'goal' => 10,
            ],
            [
                'icon' => 'fa-trophy',
                'title' => 'Task Champion (25)',
                'description' => 'Completed 25 tasks in total!',
                'goal' => 25,
            ],
            [
                'icon' => 'fa-crown',
                'title' => 'Task Legend (50)',
                'description' => 'Completed 50 tasks in total!',
                'goal' => 50,
            ],
        ]);

        // Map progress
        $achievements = $achievementList->map(function ($ach) use ($completedTasks) {
            $progress = min(100, ($completedTasks / $ach['goal']) * 100);

            return (object) [
                'icon' => $ach['icon'],
                'title' => $ach['title'],
                'description' => $ach['description'],
                'progress' => round($progress),
                'locked' => $progress < 100,
            ];
        });

        return view('achievements.index', compact('achievements', 'completedTasks'));
    }
}
