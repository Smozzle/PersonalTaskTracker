@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/achievements.css') }}">
@endpush

@section('title', 'Achievements')
@section('header', 'Achievements & Badges')

@section('content')
<div class="achievements-wrapper">

    {{-- Page Header --}}
    <div class="achievements-header">
        <div class="header-content">
            <h2><i class="fas fa-trophy"></i> Your Achievements</h2>
            <p>Unlock badges by completing tasks and reaching milestones</p>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="achievements-stats">
        @php
            $totalAchievements = $achievements->count();
            $unlockedAchievements = $achievements->filter(fn($a) => !$a->locked)->count();
            $overallProgress = $totalAchievements > 0 ? round(($unlockedAchievements / $totalAchievements) * 100) : 0;
        @endphp

        <div class="stat-card">
            <div class="stat-icon unlocked">
                <i class="fas fa-unlock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $unlockedAchievements }}/{{ $totalAchievements }}</h3>
                <p>Unlocked</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon locked">
                <i class="fas fa-lock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalAchievements - $unlockedAchievements }}</h3>
                <p>Locked</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon progress">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $overallProgress }}%</h3>
                <p>Completion</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon tasks">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $completedTasks }}</h3>
                <p>Tasks Done</p>
            </div>
        </div>
    </div>

    {{-- Overall Progress Bar --}}
    <div class="overall-progress-section">
        <div class="progress-header">
            <h3><i class="fas fa-star"></i> Overall Progress</h3>
            <span class="progress-percentage">{{ $overallProgress }}%</span>
        </div>
        <div class="overall-progress-bar">
            <div class="overall-progress-fill" style="width: {{ $overallProgress }}%">
                <span class="progress-glow"></span>
            </div>
        </div>
        <p class="progress-message">
            @if($overallProgress == 100)
                🎉 Amazing! You've unlocked all achievements!
            @elseif($overallProgress >= 75)
                🔥 You're on fire! Almost there!
            @elseif($overallProgress >= 50)
                💪 Great progress! Keep going!
            @elseif($overallProgress >= 25)
                ⭐ Good start! Keep completing tasks!
            @else
                🚀 Start completing tasks to unlock achievements!
            @endif
        </p>
    </div>

    {{-- Achievements Grid --}}
    <div class="achievements-grid">
        @foreach($achievements as $index => $achievement)
            <div class="achievement-card {{ $achievement->locked ? 'locked' : 'unlocked' }}" 
                 data-achievement="{{ $index }}">
                
                {{-- Achievement Badge --}}
                <div class="achievement-badge">
                    <div class="badge-circle">
                        <i class="fas {{ $achievement->icon }}"></i>
                        @if(!$achievement->locked)
                            <div class="unlock-shine"></div>
                        @endif
                    </div>
                    @if($achievement->locked)
                        <div class="lock-overlay">
                            <i class="fas fa-lock"></i>
                        </div>
                    @endif
                </div>

                {{-- Achievement Info --}}
                <div class="achievement-info">
                    <h4>{{ $achievement->title }}</h4>
                    <p>{{ $achievement->description }}</p>
                </div>

                {{-- Progress Bar --}}
                @if($achievement->locked)
                    <div class="achievement-progress">
                        <div class="progress-bar-small">
                            <div class="progress-fill-small {{ $achievement->progress >= 100 ? 'complete' : '' }}" 
                                 style="width: {{ $achievement->progress }}%">
                            </div>
                        </div>
                        <span class="progress-text">{{ $achievement->progress }}% Complete</span>
                    </div>
                @else
                    <div class="achievement-unlocked-badge">
                        <i class="fas fa-check-circle"></i> Unlocked!
                    </div>
                @endif

                {{-- Rarity Badge --}}
                @php
                    $rarities = ['common', 'common', 'rare', 'epic', 'legendary'];
                    $rarity = $rarities[$index] ?? 'common';
                @endphp
                <div class="rarity-badge rarity-{{ $rarity }}">
                    {{ ucfirst($rarity) }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Motivational Section --}}
    @if($achievements->filter(fn($a) => $a->locked)->count() > 0)
    <div class="motivation-section">
        <div class="motivation-card">
            <div class="motivation-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="motivation-content">
                <h3>Keep Going! 💪</h3>
                <p>You have {{ $achievements->filter(fn($a) => $a->locked)->count() }} more achievements to unlock. 
                   Complete more tasks to earn badges and climb the ranks!</p>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
// Add unlock animation when hovering unlocked achievements
document.querySelectorAll('.achievement-card.unlocked').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.querySelector('.badge-circle').style.transform = 'scale(1.1) rotate(10deg)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.querySelector('.badge-circle').style.transform = 'scale(1) rotate(0deg)';
    });
});
</script>
@endpush

@endsection