<?php
// app/Notifications/NewAchievement.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Achievement;

class NewAchievement extends Notification
{
    use Queueable;

    protected $achievement;
    protected $achievementType;

    public function __construct(Achievement $achievement, $achievementType)
    {
        $this->achievement = $achievement;
        $this->achievementType = $achievementType;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $messages = [
            'first_task' => 'You completed your first task!',
            'task_master' => 'You completed 10 tasks!',
            'task_legend' => 'You completed 50 tasks!',
            'streak_3' => 'You maintained a 3-day streak!',
            'streak_7' => 'You maintained a 7-day streak!',
            'streak_30' => 'You maintained a 30-day streak!',
            'goal_achiever' => 'You completed your first goal!',
            'productivity_hero' => 'You reached 100 productivity score!',
        ];

        return [
            'title' => 'New Achievement Unlocked! 🏆',
            'message' => $messages[$this->achievementType] ?? 'You unlocked a new achievement!',
            'achievement_id' => $this->achievement->id,
            'achievement_type' => $this->achievementType,
            'icon' => 'fa-medal',
            'color' => 'purple',
            'url' => route('achievements.index')
        ];
    }
}