@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/task-form.css') }}">
@endpush

@section('title', 'Edit Task')
@section('header', 'Edit Task')

@section('content')
<div class="task-form-wrapper">
    
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        <span class="separator">/</span>
        <a href="{{ route('tasks.index') }}"><i class="fas fa-tasks"></i> Tasks</a>
        <span class="separator">/</span>
        <span class="current">Edit</span>
    </div>

    <div class="task-form-container">
        
        {{-- Form Header --}}
        <div class="form-header">
            <div class="form-icon edit">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Edit Task</h2>
                <p>Update the details of "{{ $task->title }}"</p>
            </div>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Oops! Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="task-form">
            @csrf
            @method('PUT')

            {{-- Basic Information Section --}}
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i> Basic Information
                </h3>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="title">
                            Task Title <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="title"
                            name="title" 
                            value="{{ old('title', $task->title) }}" 
                            required 
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="e.g., Complete project proposal"
                            maxlength="255">
                        @error('title')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-hint">Give your task a clear, descriptive title</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea 
                            id="description"
                            name="description" 
                            rows="5" 
                            class="form-control @error('description') is-invalid @enderror" 
                            placeholder="Add more details about this task...">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-hint">Optional: Add any additional notes or requirements</small>
                    </div>
                </div>
            </div>

            {{-- Task Details Section --}}
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-sliders-h"></i> Task Details
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="priority">
                            Priority <span class="required">*</span>
                        </label>
                        <div class="priority-selector">
                            <label class="priority-option priority-high">
                                <input type="radio" name="priority" value="High" {{ old('priority', $task->priority) == 'High' ? 'checked' : '' }} required>
                                <span class="priority-card">
                                    <i class="fas fa-flag"></i>
                                    <strong>High</strong>
                                    <small>Urgent & Important</small>
                                </span>
                            </label>
                            <label class="priority-option priority-medium">
                                <input type="radio" name="priority" value="Medium" {{ old('priority', $task->priority) == 'Medium' ? 'checked' : '' }}>
                                <span class="priority-card">
                                    <i class="fas fa-flag"></i>
                                    <strong>Medium</strong>
                                    <small>Important</small>
                                </span>
                            </label>
                            <label class="priority-option priority-low">
                                <input type="radio" name="priority" value="Low" {{ old('priority', $task->priority) == 'Low' ? 'checked' : '' }}>
                                <span class="priority-card">
                                    <i class="fas fa-flag"></i>
                                    <strong>Low</strong>
                                    <small>When possible</small>
                                </span>
                            </label>
                        </div>
                        @error('priority')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="due_date">
                            <i class="fas fa-calendar"></i> Due Date
                        </label>
                        <input 
                            type="date" 
                            id="due_date"
                            name="due_date" 
                            value="{{ old('due_date', $task->due_date) }}" 
                            class="form-control @error('due_date') is-invalid @enderror">
                        @error('due_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-hint">When should this task be completed?</small>
                    </div>

                    <div class="form-group">
                        <label for="category_id">
                            <i class="fas fa-tag"></i> Category
                        </label>
                        <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">Select a category...</option>
                            @foreach ($categories as $category)
                                <option 
                                    value="{{ $category->id }}" 
                                    {{ old('category_id', $task->category_id) == $category->id ? 'selected' : '' }}
                                    data-color="{{ $category->color_code ?? '#94a3b8' }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-hint">Organize your task with a category</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status">
                            <i class="fas fa-tasks"></i> Status <span class="required">*</span>
                        </label>
                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="Pending" {{ old('status', $task->status) == 'Pending' ? 'selected' : '' }}>
                                📝 Pending
                            </option>
                            <option value="Ongoing" {{ old('status', $task->status) == 'Ongoing' ? 'selected' : '' }}>
                                🔄 Ongoing
                            </option>
                            <option value="Completed" {{ old('status', $task->status) == 'Completed' ? 'selected' : '' }}>
                                ✅ Completed
                            </option>
                        </select>
                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-hint">Current progress of this task</small>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            {{-- Action Buttons --}}
<div class="form-actions">
    <button type="submit" class="btn-primary">
        <i class="fas fa-save"></i> Update Task
    </button>
    <a href="{{ route('tasks.index') }}" class="btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
    {{-- TEMPORARILY DISABLED FOR TESTING
    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">
            <i class="fas fa-trash"></i> Delete Task
        </button>
    </form>
    --}}
</div>
        </form>
    </div>
</div>
@endsection