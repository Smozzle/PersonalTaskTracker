<?php
// app/Notifications/TaskOverdue.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use Carbon\Carbon;

class TaskOverdue extends Notification
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
        $daysOverdue = now()->diffInDays($dueDate);

        return [
            'title' => 'Task Overdue! ⚠️',
            'message' => 'Task "' . $this->task->title . '" is overdue by ' . $daysOverdue . ' days',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->due_date,
            'days_overdue' => $daysOverdue,
            'priority' => $this->task->priority,
            'icon' => 'fa-exclamation-triangle',
            'color' => 'red',
            'url' => route('tasks.show', $this->task->id)
        ];
    }
}