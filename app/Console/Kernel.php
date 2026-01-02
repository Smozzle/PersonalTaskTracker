<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            // Fetch reminders that are due and not yet sent
            $reminders = \App\Models\Reminder::where('is_sent', false)
                ->where('reminder_time', '<=', now())
                ->get();

            foreach ($reminders as $reminder) {
                // Mark as sent to prevent duplicates
                $reminder->update(['is_sent' => true]);

                // Handle notification type
                if ($reminder->type === 'email') {
                    // Optional email sending
                    \Mail::to($reminder->user->email)->send(
                        new \App\Mail\TaskReminderMail($reminder)
                    );
                } elseif ($reminder->type === 'popup') {
                    // You could also trigger a database or WebSocket notification here
                    $reminder->user->notify(new \App\Notifications\TaskReminder($reminder));
                }
            }
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
