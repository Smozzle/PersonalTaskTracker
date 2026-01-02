<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reminder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskReminder;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send pending reminders';

    public function handle()
    {
        $reminders = Reminder::where('reminder_time', '<=', now())
            ->where('is_sent', false)
            ->get();

        foreach ($reminders as $reminder) {
            // Send notification (here we use Laravel notification system)
            $reminder->user->notify(new TaskReminder($reminder));

            $reminder->is_sent = true;
            $reminder->save();
        }

        $this->info('Reminders checked & sent.');
    }
}
