<?php
// app/Notifications/TaskCompleted.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskCompleted extends Notification
{
    use Queueable;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Task Completed! 🎉',
            'message' => 'Congratulations! You completed: ' . $this->task->title,
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'icon' => 'fa-check-circle',
            'color' => 'green',
            'url' => route('tasks.show', $this->task->id)
        ];
    }
}