@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('title', 'Dashboard')
@section('header', '👨‍💼 Admin Dashboard')


@section('content')
<div class="admin-dashboard">

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalUsers }}</h3>
                <p>Total Users</p>
                <span class="stat-change positive">+{{ $newUsersThisMonth }} this month</span>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalTasks }}</h3>
                <p>Total Tasks</p>
                <span class="stat-detail">{{ $completedTasks }} completed</span>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalGoals }}</h3>
                <p>Total Goals</p>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalAchievements }}</h3>
                <p>Achievements Unlocked</p>
            </div>
        </div>
    </div>

    <!-- Task Status Overview -->
    <div class="charts-row">
        <div class="chart-card large">
            <div class="chart-header">
                <h3>📊 Monthly Growth</h3>
                <p>User and task creation over last 6 months</p>
            </div>
            <canvas id="monthlyGrowthChart"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3>📈 Task Status</h3>
            </div>
            <canvas id="taskStatusChart"></canvas>
            <div class="task-status-legend">
                <div class="legend-item">
                    <span class="dot completed"></span>
                    <span>Completed: {{ $completedTasks }}</span>
                </div>
                <div class="legend-item">
                    <span class="dot ongoing"></span>
                    <span>Ongoing: {{ $ongoingTasks }}</span>
                </div>
                <div class="legend-item">
                    <span class="dot pending"></span>
                    <span>Pending: {{ $pendingTasks }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity -->
    <div class="charts-row">
        <div class="chart-card large">
            <div class="chart-header">
                <h3>📅 Recent Activity (Last 7 Days)</h3>
            </div>
            <canvas id="userActivityChart"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3>🏆 Top 5 Active Users</h3>
            </div>
            <div class="top-users-list">
                @foreach($topUsers->take(5) as $user)
                    <div class="user-item">
                        <div class="user-info">
                            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}" 
                                 alt="{{ $user->name }}" class="user-avatar">
                            <div>
                                <h4>{{ $user->name }}</h4>
                                <span>{{ $user->email }}</span>
                            </div>
                        </div>
                        <div class="user-stats">
                            <span class="task-count">{{ $user->tasks_count }} tasks</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.users') }}" class="view-all-btn">View All Users →</a>
        </div>
    </div>

    <!-- Recent Users & Completion Rates -->
    <div class="charts-row">
        <div class="chart-card">
            <div class="chart-header">
                <h3>👥 Recent Users</h3>
            </div>
            <div class="recent-users-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers->take(8) as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.user-details', $user->id) }}" class="user-link">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($user->is_admin)
                                        <span class="badge admin">Admin</span>
                                    @else
                                        <span class="badge user">User</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3>📊 User Completion Rates</h3>
            </div>
            <div class="completion-rates">
                @foreach($userCompletionRates->take(5) as $user)
                    <div class="completion-item">
                        <div class="completion-header">
                            <span class="user-name">{{ Str::limit($user['name'], 20) }}</span>
                            <span class="completion-percent">{{ $user['completion_rate'] }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $user['completion_rate'] }}%"></div>
                        </div>
                        <div class="completion-stats">
                            <span>{{ $user['completed_tasks'] }}/{{ $user['total_tasks'] }} tasks</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="actions-grid">
            <a href="{{ route('admin.users') }}" class="action-card">
                <i class="fas fa-users"></i>
                <span>Manage Users</span>
            </a>
            <a href="{{ route('admin.statistics') }}" class="action-card">
                <i class="fas fa-chart-bar"></i>
                <span>View Statistics</span>
            </a>
            <a href="{{ route('dashboard') }}" class="action-card">
                <i class="fas fa-home"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart colors
const colors = {
    blue: '#3b82f6',
    purple: '#8b5cf6',
    green: '#10b981',
    orange: '#f59e0b',
    red: '#ef4444',
};

// Monthly Growth Chart
const monthlyGrowthCtx = document.getElementById('monthlyGrowthChart').getContext('2d');
new Chart(monthlyGrowthCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyGrowth, 'month')) !!},
        datasets: [
            {
                label: 'Users',
                data: {!! json_encode(array_column($monthlyGrowth, 'users')) !!},
                borderColor: colors.blue,
                backgroundColor: colors.blue + '20',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Tasks',
                data: {!! json_encode(array_column($monthlyGrowth, 'tasks')) !!},
                borderColor: colors.purple,
                backgroundColor: colors.purple + '20',
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
const taskStatusCtx = document.getElementById('taskStatusChart').getContext('2d');
new Chart(taskStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Ongoing', 'Pending'],
        datasets: [{
            data: [{{ $completedTasks }}, {{ $ongoingTasks }}, {{ $pendingTasks }}],
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

// User Activity Chart
const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
new Chart(userActivityCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($userActivity, 'date')) !!},
        datasets: [
            {
                label: 'New Users',
                data: {!! json_encode(array_column($userActivity, 'users')) !!},
                backgroundColor: colors.blue,
                borderRadius: 6
            },
            {
                label: 'Tasks Created',
                data: {!! json_encode(array_column($userActivity, 'tasks')) !!},
                backgroundColor: colors.purple,
                borderRadius: 6
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
</script>
@endsection