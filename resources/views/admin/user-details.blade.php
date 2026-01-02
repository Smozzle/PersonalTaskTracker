@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-user-details.css') }}">
@endpush

@section('header', 'User Details')

@section('content')
<div class="user-details-page">
    <!-- Back Button -->
    <a href="{{ route('admin.users') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>

    <!-- User Profile Card -->
    <div class="profile-card">
        <div class="profile-header">
            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}" 
                 alt="{{ $user->name }}" class="profile-avatar">
            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <p class="email">{{ $user->email }}</p>
                <div class="profile-badges">
                    @if($user->is_admin)
                        <span class="badge admin">
                            <i class="fas fa-shield-alt"></i> Admin
                        </span>
                    @else
                        <span class="badge user">
                            <i class="fas fa-user"></i> User
                        </span>
                    @endif
                    @if($user->id === Auth::id())
                        <span class="badge you">You</span>
                    @endif
                </div>
                <p class="joined-date">
                    <i class="fas fa-calendar"></i> 
                    Joined {{ $user->created_at->format('F d, Y') }}
                </p>
            </div>
        </div>

        @if($user->id !== Auth::id())
            <div class="profile-actions">
                <form action="{{ route('admin.toggle-admin', $user) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn {{ $user->is_admin ? 'btn-warning' : 'btn-primary' }}">
                        <i class="fas {{ $user->is_admin ? 'fa-user-minus' : 'fa-user-plus' }}"></i>
                        {{ $user->is_admin ? 'Revoke Admin' : 'Grant Admin' }}
                    </button>
                </form>
                
                <form action="{{ route('admin.delete-user', $user) }}" method="POST" 
                      style="display: inline;"
                      onsubmit="return confirm('Are you sure? This will delete all user data!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete User
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $taskStats['total'] }}</h3>
                <p>Total Tasks</p>
                <span class="stat-detail">{{ $taskStats['completed'] }} completed</span>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $taskStats['completed'] }}</h3>
                <p>Completed</p>
                <span class="stat-detail">
                    {{ $taskStats['total'] > 0 ? round(($taskStats['completed'] / $taskStats['total']) * 100, 1) : 0 }}% rate
                </span>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $user->goals_count }}</h3>
                <p>Goals</p>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $user->achievements_count }}</h3>
                <p>Achievements</p>
            </div>
        </div>
    </div>

    <!-- Activity Timeline Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h3>📊 Activity Timeline (Last 30 Days)</h3>
        </div>
        <canvas id="activityChart"></canvas>
    </div>

    <!-- Task Status Breakdown -->
    <div class="charts-row">
        <div class="chart-card">
            <div class="chart-header">
                <h3>📈 Task Status</h3>
            </div>
            <canvas id="taskStatusChart"></canvas>
        </div>

        <div class="stats-summary">
            <h3>Task Breakdown</h3>
            <div class="summary-item">
                <span class="label">
                    <span class="dot completed"></span>
                    Completed
                </span>
                <span class="value">{{ $taskStats['completed'] }}</span>
            </div>
            <div class="summary-item">
                <span class="label">
                    <span class="dot ongoing"></span>
                    Ongoing
                </span>
                <span class="value">{{ $taskStats['ongoing'] }}</span>
            </div>
            <div class="summary-item">
                <span class="label">
                    <span class="dot pending"></span>
                    Pending
                </span>
                <span class="value">{{ $taskStats['pending'] }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Tasks -->
    <div class="data-section">
        <h3>📝 Recent Tasks</h3>
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($task->status) }}">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td>
                                <span class="priority-badge priority-{{ strtolower($task->priority) }}">
                                    {{ $task->priority }}
                                </span>
                            </td>
                            <td>{{ $task->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty">No tasks found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Goals -->
    <div class="data-section">
        <h3>🎯 Goals</h3>
        <div class="goals-grid">
            @forelse($goals as $goal)
                <div class="goal-card">
                    <div class="goal-header">
                        <h4>{{ $goal->title }}</h4>
                        <span class="goal-status {{ strtolower($goal->status) }}">
                            {{ $goal->status }}
                        </span>
                    </div>
                    <div class="goal-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $goal->progress }}%"></div>
                        </div>
                        <span class="progress-text">{{ $goal->progress }}%</span>
                    </div>
                    @if($goal->target_date)
                        <p class="goal-date">
                            <i class="fas fa-calendar"></i>
                            Due: {{ \Carbon\Carbon::parse($goal->target_date)->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            @empty
                <p class="empty-message">No goals set</p>
            @endforelse
        </div>
    </div>

    <!-- Achievements -->
    <div class="data-section">
        <h3>🏆 Achievements</h3>
        <div class="achievements-grid">
            @forelse($achievements as $achievement)
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas {{ $achievement->icon ?? 'fa-trophy' }}"></i>
                    </div>
                    <h4>{{ $achievement->title }}</h4>
                    <p>{{ $achievement->description }}</p>
                    <span class="achievement-date">
                        {{ $achievement->created_at->format('M d, Y') }}
                    </span>
                </div>
            @empty
                <p class="empty-message">No achievements unlocked yet</p>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const colors = {
    blue: '#3b82f6',
    green: '#10b981',
    orange: '#f59e0b',
};

// Activity Timeline Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($activityTimeline, 'date')) !!},
        datasets: [
            {
                label: 'Tasks Created',
                data: {!! json_encode(array_column($activityTimeline, 'tasks_created')) !!},
                borderColor: colors.blue,
                backgroundColor: colors.blue + '20',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Tasks Completed',
                data: {!! json_encode(array_column($activityTimeline, 'tasks_completed')) !!},
                borderColor: colors.green,
                backgroundColor: colors.green + '20',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// Task Status Chart
const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Ongoing', 'Pending'],
        datasets: [{
            data: [{{ $taskStats['completed'] }}, {{ $taskStats['ongoing'] }}, {{ $taskStats['pending'] }}],
            backgroundColor: [colors.green, colors.blue, colors.orange],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        }
    }
});
</script>
@endsection