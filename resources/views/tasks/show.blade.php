@extends('layouts.app')

@section('header', 'Task Details')

@section('content')
<div class="task-details-container">
    <div class="task-card">
        <div class="task-header">
            <h2>{{ $task->title }}</h2>
            <span class="status-badge status-{{ strtolower($task->status) }}">
                {{ $task->status }}
            </span>
        </div>

        <div class="task-body">
            <div class="task-info">
                <label>Description:</label>
                <p>{{ $task->description ?? 'No description provided' }}</p>
            </div>

            <div class="task-info">
                <label>Priority:</label>
                <span class="priority-badge priority-{{ strtolower($task->priority) }}">
                    {{ $task->priority }}
                </span>
            </div>

            <div class="task-info">
                <label>Category:</label>
                <p>{{ $task->category->name ?? 'No category' }}</p>
            </div>

            @if($task->due_date)
            <div class="task-info">
                <label>Due Date:</label>
                <p>{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</p>
            </div>
            @endif

            <div class="task-info">
                <label>Created:</label>
                <p>{{ $task->created_at->format('M d, Y h:i A') }}</p>
            </div>

            @if($task->completed_at)
            <div class="task-info">
                <label>Completed:</label>
                <p>{{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y h:i A') }}</p>
            </div>
            @endif
        </div>

        <div class="task-actions">
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tasks
            </a>
        </div>
    </div>
</div>

<style>
.task-details-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.task-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.task-header h2 {
    margin: 0;
    color: #1e293b;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-ongoing { background: #dbeafe; color: #1e40af; }
.status-completed { background: #d1fae5; color: #065f46; }

.task-body {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.task-info label {
    display: block;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.task-info p {
    margin: 0;
    color: #1e293b;
}

.priority-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
}

.priority-high { background: #fee2e2; color: #991b1b; }
.priority-medium { background: #fef3c7; color: #92400e; }
.priority-low { background: #dbeafe; color: #1e40af; }

.task-actions {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #e2e8f0;
    color: #475569;
}

.btn-secondary:hover {
    background: #cbd5e1;
}
</style>
@endsection