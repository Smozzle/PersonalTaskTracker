@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="dashboard-content">
    
    {{-- Welcome Section --}}
    <div class="welcome-section">
        <h2>Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p>Here's what's happening with your tasks today</p>
    </div>

    {{-- Stats Cards --}}
    <div class="cards-container">
        <div class="card total">
            <div class="card-header">
                <h3>Total Tasks</h3>
                <div class="card-icon">
                    <i class="fas fa-list-check"></i>
                </div>
            </div>
            <div class="card-value">{{ $totalTasks }}</div>
            <div class="card-footer">All your tasks</div>
        </div>

        <div class="card completed">
            <div class="card-header">
                <h3>Completed</h3>
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="card-value">{{ $completedTasks }}</div>
            <div class="card-footer">Tasks finished</div>
        </div>

        <div class="card pending">
            <div class="card-header">
                <h3>Pending</h3>
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="card-value">{{ $pendingTasks }}</div>
            <div class="card-footer">Tasks remaining</div>
        </div>
    </div>

    {{-- Progress Section --}}
    <div class="progress-section">
        <h3><i class="fas fa-chart-line"></i> Overall Progress</h3>
        <div class="progress-bar-container">
            <div class="progress-bar" style="width: {{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%">
                {{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}%
            </div>
        </div>
        <div class="progress-text">
            @if($totalTasks > 0)
                You've completed {{ $completedTasks }} out of {{ $totalTasks }} tasks
                @if($completedTasks == $totalTasks)
                    🎉 Awesome! All tasks completed!
                @elseif($completedTasks > 0)
                    - Keep going!
                @else
                    - Let's get started!
                @endif
            @else
                No tasks yet. Create your first task to get started!
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="quick-actions">
        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
        <div class="action-buttons">
            <a href="{{ route('tasks.create') }}" class="action-btn primary">
                <i class="fas fa-plus"></i> Create New Task
            </a>
            <a href="{{ route('tasks.index') }}" class="action-btn secondary">
                <i class="fas fa-tasks"></i> View All Tasks
            </a>
            <a href="{{ route('goals.index') }}" class="action-btn secondary">
                <i class="fas fa-bullseye"></i> View Goals
            </a>
            <a href="{{ route('achievements.index') }}" class="action-btn secondary">
                <i class="fas fa-award"></i> Achievements
            </a>
        </div>
    </div>

</div>
@endsection