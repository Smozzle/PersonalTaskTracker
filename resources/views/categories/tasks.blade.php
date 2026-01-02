@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/task-index.css') }}">
@endpush

@section('title', $category->name . ' Tasks')
@section('header', 'Tasks in ' . $category->name)

@section('content')

    <a href="{{ route('categories.index') }}" class="add-task-btn" style="background-color: #1e3a8a;">
        ← Back to Categories
    </a>

    @if ($tasks->isEmpty())
        <p style="margin-top: 1rem; color: #64748b;">No tasks found under this category.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $index => $task)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $task->title }}</td>
                        <td>{{ $task->description ?? '-' }}</td>
                        <td>{{ $task->due_date ?? '-' }}</td>
                        <td>
                            <span class="priority-tag {{ strtolower($task->priority) }}">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td>{{ $task->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
