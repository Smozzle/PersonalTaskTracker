<?php
// app/Notifications/TaskDueSoon.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use Carbon\Carbon;

class TaskDueSoon extends Notification
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
        $dueDate = Carbon::parse($this->task->due_date);
        $hoursLeft = now()->diffInHours($dueDate);

        return [
            'title' => 'Task Due Soon! ⏰',
            'message' => 'Task "' . $this->task->title . '" is due in ' . $hoursLeft . ' hours',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->due_date,
            'hours_left' => $hoursLeft,
            'priority' => $this->task->priority,
            'icon' => 'fa-clock',
            'color' => 'orange',
            'url' => route('tasks.show', $this->task->id)
        ];
    }
}