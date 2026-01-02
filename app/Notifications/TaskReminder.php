<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Reminder;

class TaskReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $reminder;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Get the notification delivery channels.
     */
    public function via($notifiable)
    {
        // store in database (for frontend popup)
        return ['database'];
    }

    /**
     * Store data for database notifications.
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->reminder->message,
            'task_id' => $this->reminder->task_id,
            'reminder_time' => $this->reminder->reminder_time,
        ];
    }
}
