<?php
// app/Notifications/GoalAchieved.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Goal;

class GoalAchieved extends Notification
{
    use Queueable;

    protected $goal;

    public function __construct(Goal $goal)
    {
        $this->goal = $goal;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Goal Achieved! 🎯',
            'message' => 'Congratulations! You achieved your goal: ' . $this->goal->title,
            'goal_id' => $this->goal->id,
            'goal_title' => $this->goal->title,
            'progress' => $this->goal->progress,
            'icon' => 'fa-trophy',
            'color' => 'gold',
            'url' => route('goals.show', $this->goal->id)
        ];
    }
}