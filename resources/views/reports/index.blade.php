@extends('layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
@endpush

@section('title', 'Reports & Analytics')
@section('header', 'Performance Reports')

@section('content')
<div class="reports-container">
    <!-- Header Section -->
    <div class="reports-header">
        <div class="header-content">
            <h1>📊 Reports & Analytics</h1>
            <p class="subtitle">Track your productivity and progress</p>
        </div>
        
        <div class="header-actions">
            <button onclick="window.print()" class="btn-secondary">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('reports.export-pdf', ['period' => $period]) }}" class="btn-primary">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Filters & Controls -->
    <div class="report-controls">
        <form method="GET" action="{{ route('reports.index') }}" class="filters-form">
            <!-- Period Selector -->
            <div class="filter-group">
                <label>Time Period</label>
                <select name="period" onchange="this.form.submit()" class="form-select">
                    <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div class="filter-group">
                <label>Category</label>
                <select name="category" onchange="this.form.submit()" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $categoryFilter == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div class="filter-group">
                <label>Priority</label>
                <select name="priority" onchange="this.form.submit()" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="High" {{ $priorityFilter == 'High' ? 'selected' : '' }}>High</option>
                    <option value="Medium" {{ $priorityFilter == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="Low" {{ $priorityFilter == 'Low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>

            @if($categoryFilter || $priorityFilter)
                <a href="{{ route('reports.index', ['period' => $period]) }}" class="btn-clear">
                    Clear Filters
                </a>
            @endif
        </form>

        <div class="date-range">
            <i class="fas fa-calendar"></i>
            {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
        </div>
    </div>

    <!-- Report Type Tabs -->
    <div class="report-tabs">
        <button class="tab-btn active" onclick="showReport('overview')">Overview</button>
        <button class="tab-btn" onclick="showReport('productivity')">Productivity</button>
        <button class="tab-btn" onclick="showReport('categories')">Categories</button>
        <button class="tab-btn" onclick="showReport('goals')">Goals</button>
        <button class="tab-btn" onclick="showReport('achievements')">Achievements</button>
    </div>

    <!-- Overview Report -->
    <div id="overview-report" class="report-section active">
        <!-- Key Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ $totalTasks }}</h3>
                    <p>Total Tasks</p>
                    @if($tasksChange != 0)
                        <span class="metric-change {{ $tasksChange > 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-{{ $tasksChange > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                            {{ abs($tasksChange) }}%
                        </span>
                    @endif
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ $completedTasks }}</h3>
                    <p>Completed</p>
                    <span class="metric-detail">{{ $completionRate }}% completion rate</span>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ $productivityScore }}</h3>
                    <p>Productivity Score</p>
                    @if($productivityScore - $previousProductivityScore != 0)
                        <span class="metric-change {{ $productivityScore > $previousProductivityScore ? 'positive' : 'negative' }}">
                            <i class="fas fa-{{ $productivityScore > $previousProductivityScore ? 'arrow-up' : 'arrow-down' }}"></i>
                            {{ abs($productivityScore - $previousProductivityScore) }} points
                        </span>
                    @endif
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ $avgCompletionTime }}h</h3>
                    <p>Avg. Completion Time</p>
                    <span class="metric-detail">Per task</span>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <!-- Daily Trend Chart -->
            <div class="chart-card large">
                <div class="chart-header">
                    <h3>📈 Daily Task Trend</h3>
                    <div class="chart-legend">
                        <span><span class="legend-dot" style="background: #667eea;"></span> Created</span>
                        <span><span class="legend-dot" style="background: #10b981;"></span> Completed</span>
                    </div>
                </div>
                <canvas id="dailyTrendChart"></canvas>
            </div>

            <!-- Completion Rate Gauge -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>🎯 Completion Rate</h3>
                </div>
                <div class="gauge-container">
                    <canvas id="completionGauge"></canvas>
                    <div class="gauge-value">
                        <h2>{{ $completionRate }}%</h2>
                        <p>of tasks completed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Breakdown & Priority -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>📊 Task Status</h3>
                </div>
                <canvas id="statusChart"></canvas>
                <div class="status-summary">
                    <div class="status-item">
                        <span class="status-dot completed"></span>
                        <span>Completed: {{ $completedTasks }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-dot ongoing"></span>
                        <span>Ongoing: {{ $ongoingTasks }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-dot pending"></span>
                        <span>Pending: {{ $pendingTasks }}</span>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>⚡ Priority Distribution</h3>
                </div>
                <canvas id="priorityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Productivity Report -->
    <div id="productivity-report" class="report-section">
        <div class="charts-row">
            <!-- Peak Hours -->
            <div class="chart-card large">
                <div class="chart-header">
                    <h3>⏰ Peak Productivity Hours</h3>
                    <p class="chart-subtitle">When you complete most tasks</p>
                </div>
                <canvas id="peakHoursChart"></canvas>
            </div>

            <!-- Weekly Comparison -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>📅 Weekly Performance</h3>
                </div>
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>

        <!-- Activity Heatmap -->
        <div class="chart-card full-width">
            <div class="chart-header">
                <h3>🔥 Activity Heatmap</h3>
                <p class="chart-subtitle">Your daily task completion pattern</p>
            </div>
            <div class="heatmap-container">
                @foreach($heatmapData as $day)
                    <div class="heatmap-cell intensity-{{ $day['intensity'] }}" 
                         title="{{ $day['date'] }}: {{ $day['count'] }} tasks"
                         data-count="{{ $day['count'] }}">
                        <span class="day-label">{{ $day['day'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="heatmap-legend">
                <span>Less</span>
                <div class="legend-scale">
                    <span class="intensity-0"></span>
                    <span class="intensity-1"></span>
                    <span class="intensity-2"></span>
                    <span class="intensity-3"></span>
                    <span class="intensity-4"></span>
                </div>
                <span>More</span>
            </div>
        </div>

        <!-- Streak Info -->
        <div class="charts-row">
            <div class="stat-card">
                <div class="stat-icon" style="background: #ff6b6b;">
                    <i class="fas fa-fire"></i>
                </div>
                <h3>{{ $streakData['current'] }} Days</h3>
                <p>Current Streak</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #ffa726;">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>{{ $streakData['longest'] }} Days</h3>
                <p>Longest Streak</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #66bb6a;">
                    <i class="fas fa-check-double"></i>
                </div>
                <h3>{{ $streakData['consistency'] }}%</h3>
                <p>Consistency Score</p>
            </div>
        </div>
    </div>

    <!-- Categories Report -->
    <div id="categories-report" class="report-section">
        <div class="charts-row">
            <div class="chart-card large">
                <div class="chart-header">
                    <h3>📁 Category Distribution</h3>
                </div>
                <canvas id="categoryPieChart"></canvas>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>🏆 Top Categories</h3>
                </div>
                <div class="category-list">
                    @foreach($categoryData->take(5) as $cat)
                        <div class="category-item">
                            <div class="category-info">
                                <span class="category-color" style="background: {{ $cat['color'] }};"></span>
                                <span class="category-name">{{ $cat['name'] }}</span>
                            </div>
                            <div class="category-stats">
                                <span class="task-count">{{ $cat['count'] }} tasks</span>
                                <span class="completion-badge">{{ $cat['completionRate'] }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $cat['completionRate'] }}%; background: {{ $cat['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Category Performance Table -->
        <div class="chart-card full-width">
            <div class="chart-header">
                <h3>📊 Category Performance Details</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total Tasks</th>
                        <th>Completed</th>
                        <th>Completion Rate</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryData as $cat)
                        <tr>
                            <td>
                                <span class="category-badge" style="background: {{ $cat['color'] }};">
                                    {{ $cat['name'] }}
                                </span>
                            </td>
                            <td>{{ $cat['count'] }}</td>
                            <td>{{ $cat['completed'] }}</td>
                            <td>{{ $cat['completionRate'] }}%</td>
                            <td>
                                <div class="performance-bar">
                                    <div class="bar-fill" style="width: {{ $cat['completionRate'] }}%; background: {{ $cat['color'] }};"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Goals Report -->
    <div id="goals-report" class="report-section">
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ $goals->count() }}</h3>
                    <p>Total Goals</p>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ $goalsCompleted }}</h3>
                    <p>Completed</p>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ $goalsOngoing }}</h3>
                    <p>In Progress</p>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ $avgGoalProgress }}%</h3>
                    <p>Avg. Progress</p>
                </div>
            </div>
        </div>

        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>🎯 Goal Status</h3>
                </div>
                <canvas id="goalStatusChart"></canvas>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>📈 Goal Progress</h3>
                </div>
                <div class="goal-progress-stats">
                    <div class="progress-stat">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <div>
                            <h4>{{ $goalsOnTrack }}</h4>
                            <p>On Track</p>
                        </div>
                    </div>
                    <div class="progress-stat">
                        <i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>
                        <div>
                            <h4>{{ $goalsBehind }}</h4>
                            <p>Behind Schedule</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goals List -->
        <div class="chart-card full-width">
            <div class="chart-header">
                <h3>📋 Goals Overview</h3>
            </div>
            <div class="goals-list">
                @foreach($goals->take(10) as $goal)
                    <div class="goal-item">
                        <div class="goal-header">
                            <h4>{{ $goal->title }}</h4>
                            <span class="goal-status {{ strtolower($goal->status) }}">{{ $goal->status }}</span>
                        </div>
                        <div class="goal-progress">
                            <div class="progress-info">
                                <span>Progress: {{ $goal->progress }}%</span>
                                @if($goal->target_date)
                                    <span class="goal-date">Due: {{ Carbon\Carbon::parse($goal->target_date)->format('M d, Y') }}</span>
                                @endif
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $goal->progress }}%;"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Achievements Report -->
    <div id="achievements-report" class="report-section">
        <div class="achievement-summary">
            <div class="achievement-hero">
                <i class="fas fa-trophy"></i>
                <h2>{{ $achievements }}</h2>
                <p>Total Achievements Unlocked</p>
            </div>
        </div>

        <div class="charts-row">
            <div class="stat-card">
                <div class="stat-icon" style="background: #ffd700;">
                    <i class="fas fa-medal"></i>
                </div>
                <h3>{{ $goalsCompleted }}</h3>
                <p>Goals Completed</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #ff6b6b;">
                    <i class="fas fa-fire"></i>
                </div>
                <h3>{{ $streakData['longest'] }}</h3>
                <p>Best Streak</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #4facfe;">
                    <i class="fas fa-star"></i>
                </div>
                <h3>{{ $completedTasks }}</h3>
                <p>Tasks Completed</p>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        @if($upcomingDeadlines->count() > 0)
        <div class="chart-card full-width">
            <div class="chart-header">
                <h3>⏰ Upcoming Deadlines</h3>
            </div>
            <div class="deadlines-list">
                @foreach($upcomingDeadlines as $task)
                    <div class="deadline-item">
                        <div class="deadline-info">
                            <h4>{{ $task->title }}</h4>
                            <span class="priority-badge {{ strtolower($task->priority) }}">{{ $task->priority }}</span>
                        </div>
                        <div class="deadline-date">
                            <i class="fas fa-calendar"></i>
                            {{ Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                            <span class="days-left">({{ Carbon\Carbon::parse($task->due_date)->diffForHumans() }})</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Report Tab Switching
function showReport(reportType) {
    document.querySelectorAll('.report-section').forEach(section => {
        section.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.getElementById(reportType + '-report').classList.add('active');
    event.target.classList.add('active');
}

// Chart Colors
const colors = {
    primary: '#667eea',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#3b82f6',
    purple: '#8b5cf6'
};

// Daily Trend Chart
const dailyTrendCtx = document.getElementById('dailyTrendChart').getContext('2d');
new Chart(dailyTrendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($dailyData, 'date')) !!},
        datasets: [
            {
                label: 'Created',
                data: {!! json_encode(array_column($dailyData, 'created')) !!},
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Completed',
                data: {!! json_encode(array_column($dailyData, 'completed')) !!},
                borderColor: colors.success,
                backgroundColor: colors.success + '20',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

// Completion Rate Gauge
const gaugeCtx = document.getElementById('completionGauge').getContext('2d');
new Chart(gaugeCtx, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $completionRate }}, {{ 100 - $completionRate }}],
            backgroundColor: [colors.success, '#e5e7eb'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: { display: false },
            tooltip: { enabled: false }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Ongoing', 'Pending'],
        datasets: [{
            data: [{{ $completedTasks }}, {{ $ongoingTasks }}, {{ $pendingTasks }}],
            backgroundColor: [colors.success, colors.info, colors.warning],
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

// Priority Chart
const priorityCtx = document.getElementById('priorityChart').getContext('2d');
new Chart(priorityCtx, {
    type: 'bar',
    data: {
        labels: ['High', 'Medium', 'Low'],
        datasets: [{
            data: [{{ $priorityData['High'] }}, {{ $priorityData['Medium'] }}, {{ $priorityData['Low'] }}],
            backgroundColor: [colors.danger, colors.warning, colors.info],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

// Peak Hours Chart
const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
new Chart(peakHoursCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($peakHours, 'label')) !!},
        datasets: [{
            label: 'Tasks Completed',
            data: {!! json_encode(array_column($peakHours, 'count')) !!},
            backgroundColor: colors.purple,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

// Weekly Chart
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($weeklyComparison, 'week')) !!},
        datasets: [{
            label: 'Completed',
            data: {!! json_encode(array_column($weeklyComparison, 'completed')) !!},
            borderColor: colors.success,
            backgroundColor: colors.success + '40',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

// Category Pie Chart
const categoryPieCtx = document.getElementById('categoryPieChart').getContext('2d');
new Chart(categoryPieCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($categoryData->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($categoryData->pluck('count')) !!},
            backgroundColor: {!! json_encode($categoryData->pluck('color')) !!},
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Goal Status Chart
const goalStatusCtx = document.getElementById('goalStatusChart').getContext('2d');
new Chart(goalStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Ongoing', 'Pending'],
        datasets: [{
            data: [{{ $goalsCompleted }}, {{ $goalsOngoing }}, {{ $goalsPending }}],
            backgroundColor: [colors.success, colors.info, colors.warning],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection