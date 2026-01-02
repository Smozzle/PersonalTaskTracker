@component('mail::message')
# Task Reminder

Hello {{ $user->name }},

This is a friendly reminder for your task:

**Task:** {{ $taskTitle }}

**Reminder Time:** {{ \Carbon\Carbon::parse($reminderTime)->format('d M Y, h:i A') }}

**Message:**
> {{ $messageText }}

@component('mail::button', ['url' => url('/tasks/' . ($reminder->task_id ?? ''))])
View Task
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
