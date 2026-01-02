<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reminder;

class TaskReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;

    /**
     * Create a new message instance.
     */
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Task Reminder: ' . ($this->reminder->task->title ?? 'Upcoming Task'))
            ->markdown('emails.reminder')
            ->with([
                'user' => $this->reminder->user,
                'messageText' => $this->reminder->message,
                'taskTitle' => $this->reminder->task->title ?? 'General Reminder',
                'reminderTime' => $this->reminder->reminder_time,
            ]);
    }
}
