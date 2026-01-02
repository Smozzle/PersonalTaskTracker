@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/visualization.css') }}">
@endpush

@section('title', 'Data Visualization')
@section('header', 'Analytics & Insights')

@section('content')
<div class="visualization-container">

    {{-- Page Header --}}
    <div class="viz-header">
        <div class="header-content">
            <h2><i class="fas fa-chart-line"></i> Analytics Dashboard</h2>
            <p>Visualize your productivity and task patterns</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card completion-rate">
            <div class="stat-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                @php
                    $totalTasks = $completed + $ongoing + $pending;
                    $completionRate = $totalTasks > 0 ? round(($completed / $totalTasks) * 100, 1) : 0;
                @endphp
                <h3>{{ $completionRate }}%</h3>
                <p>Completion Rate</p>
                <span class="percentage">
                    @if($completionRate >= 80) Excellent 🎉
                    @elseif($completionRate >= 50) Good Progress
                    @else Needs Focus
                    @endif
                </span>
            </div>
        </div>

        <div class="stat-card productivity">
            <div class="stat-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <div class="stat-info">
                @php
                    $productivityScore = min(100, $completionRate + ($totalTasks > 0 ? 20 : 0));
                @endphp
                <h3>{{ $productivityScore }}</h3>
                <p>Productivity Score</p>
                <span class="percentage">Out of 100</span>
            </div>
        </div>

        <div class="stat-card categories-count">
            <div class="stat-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $categoryNames->count() }}</h3>
                <p>Active Categories</p>
                <span class="percentage">{{ $categoryNames->count() > 0 ? 'Organized' : 'Create Some' }}</span>
            </div>
        </div>

        <div class="stat-card most-active">
            <div class="stat-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-info">
                @php
                    $maxCount = $categoryCounts->max() ?? 0;
                    $maxIndex = $categoryCounts->search($maxCount);
                    $mostActive = $maxIndex !== false ? $categoryNames[$maxIndex] : 'None';
                @endphp
                <h3>{{ $maxCount }}</h3>
                <p>{{ Str::limit($mostActive, 15) }}</p>
                <span class="percentage">Most Active</span>
            </div>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="charts-grid">
        
        {{-- Task Status Pie Chart --}}
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-pie"></i> Task Status Distribution</h3>
                <p>Overview of your task completion</p>
            </div>
            <div class="chart-body">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>
        {{-- Category Bar Chart --}}
        <div class="chart-card full-width">
            <div class="chart-header">
                <h3><i class="fas fa-chart-bar"></i> Tasks by Category</h3>
                <p>Distribution across categories</p>
            </div>
            <div class="chart-body">
                <canvas id="categoryBarChart"></canvas>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart.js default configuration
Chart.defaults.font.family = 'Poppins, sans-serif';
Chart.defaults.color = '#64748b';

// Color schemes
const colors = {
    completed: '#10b981',
    ongoing: '#f59e0b',
    pending: '#ef4444',
    gradient1: ['#667eea', '#764ba2'],
    gradient2: ['#f093fb', '#f5576c'],
    categories: [
        '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', 
        '#10b981', '#06b6d4', '#6366f1', '#ef4444',
        '#84cc16', '#f97316', '#14b8a6', '#a855f7'
    ]
};

// 1. Status Pie Chart
const statusPieCtx = document.getElementById('statusPieChart');
new Chart(statusPieCtx, {
    type: 'pie',
    data: {
        labels: ['Completed', 'Ongoing', 'Pending'],
        datasets: [{
            data: [{{ $completed }}, {{ $ongoing }}, {{ $pending }}],
            backgroundColor: [colors.completed, colors.ongoing, colors.pending],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 13, weight: '600' },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${label}: ${value} tasks (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// 2. Category Bar Chart
const categoryBarCtx = document.getElementById('categoryBarChart');
new Chart(categoryBarCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($categoryNames) !!},
        datasets: [{
            label: 'Number of Tasks',
            data: {!! json_encode($categoryCounts) !!},
            backgroundColor: colors.categories,
            borderRadius: 8,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `Tasks: ${context.parsed.y}`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { 
                    precision: 0,
                    font: { size: 12 }
                },
                grid: {
                    color: '#f1f5f9'
                }
            },
            x: {
                ticks: {
                    font: { size: 12 }
                },
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>
@endpush