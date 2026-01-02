@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-statistics.css') }}">
@endpush

@section('header', '📊 System Statistics')

@section('content')
<div class="statistics-page">
    <a href="{{ route('admin.dashboard') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="stats-overview">
        <h2>Platform Overview</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>{{ $stats['users'] }}</h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3>{{ $stats['tasks'] }}</h3>
                <p>Total Tasks</p>
            </div>
            <div class="stat-card">
                <h3>{{ $stats['goals'] }}</h3>
                <p>Total Goals</p>
            </div>
            <div class="stat-card">
                <h3>{{ $stats['categories'] }}</h3>
                <p>Total Categories</p>
            </div>
            <div class="stat-card">
                <h3>{{ $stats['achievements'] }}</h3>
                <p>Total Achievements</p>
            </div>
        </div>
    </div>

    <div class="platform-stats">
        <h2>Usage Statistics</h2>
        
        <div class="usage-grid">
            <div class="usage-card">
                <div class="usage-label">Average Tasks per User</div>
                <div class="usage-value">{{ $platformStats['avg_tasks_per_user'] }}</div>
            </div>
            <div class="usage-card">
                <div class="usage-label">Average Goals per User</div>
                <div class="usage-value">{{ $platformStats['avg_goals_per_user'] }}</div>
            </div>
            <div class="usage-card">
                <div class="usage-label">Overall Completion Rate</div>
                <div class="usage-value">{{ $platformStats['completion_rate'] }}%</div>
            </div>
            <div class="usage-card">
                <div class="usage-label">Active Users Today</div>
                <div class="usage-value">{{ $platformStats['active_users_today'] }}</div>
            </div>
        </div>
    </div>
</div>
@endsection