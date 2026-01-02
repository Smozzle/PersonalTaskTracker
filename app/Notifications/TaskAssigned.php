<?php
// app/Notifications/TaskAssigned.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskAssigned extends Notification
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
            'title' => 'New Task Created',
            'message' => 'You created a new task: ' . $this->task->title,
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'priority' => $this->task->priority,
            'icon' => 'fa-tasks',
            'color' => 'blue',
            'url' => route('tasks.show', $this->task->id)
        ];
    }
}