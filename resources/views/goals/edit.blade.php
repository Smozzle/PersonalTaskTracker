@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/goal-form.css') }}">
@endpush

@section('title', 'Edit Goal')
@section('header', 'Edit Goal')

@section('content')
<div class="goal-form-wrapper">
    
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        <span class="separator">/</span>
        <a href="{{ route('goals.index') }}"><i class="fas fa-bullseye"></i> Goals</a>
        <span class="separator">/</span>
        <span class="current">Edit</span>
    </div>

    <div class="goal-form-container">
        
        {{-- Form Header --}}
        <div class="form-header edit">
            <div class="form-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Edit Goal</h2>
                <p>Update "{{ $goal->title }}"</p>
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

        {{-- ============================= --}}
        {{--       UPDATE GOAL FORM        --}}
        {{-- ============================= --}}
        <form action="{{ route('goals.update', $goal->id) }}" method="POST" class="goal-form">
            @csrf
            @method('PUT')

            {{-- Basic Information Section --}}
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i> Goal Details
                </h3>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="title">Goal Title <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="title"
                            name="title" 
                            value="{{ old('title', $goal->title) }}" 
                            required 
                            class="form-control @error('title') is-invalid @enderror"
                            maxlength="255">

                        @error('title')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea 
                            id="description"
                            name="description" 
                            rows="5" 
                            class="form-control @error('description') is-invalid @enderror">{{ old('description', $goal->description) }}</textarea>

                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Goal Settings --}}
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-sliders-h"></i> Goal Settings
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="target_date"><i class="fas fa-calendar"></i> Target Date</label>
                        <input 
                            type="date" 
                            id="target_date"
                            name="target_date" 
                            value="{{ old('target_date', $goal->target_date) }}" 
                            class="form-control @error('target_date') is-invalid @enderror">
                        @error('target_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status"><i class="fas fa-flag"></i> Status</label>
                        <select 
                            id="status"
                            name="status" 
                            class="form-control @error('status') is-invalid @enderror"
                            required>
                            <option value="Ongoing" {{ old('status', $goal->status) === 'Ongoing' ? 'selected' : '' }}>🔄 Ongoing</option>
                            <option value="Completed" {{ old('status', $goal->status) === 'Completed' ? 'selected' : '' }}>✅ Completed</option>
                        </select>

                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="progress">
                            <i class="fas fa-chart-line"></i> Progress: 
                            <span id="progressDisplay">{{ old('progress', $goal->progress) }}%</span>
                        </label>

                        <div class="progress-preview">
                            <div class="progress-preview-bar">
                                <div class="progress-preview-fill" id="progressPreview" style="width: {{ old('progress', $goal->progress) }}%"></div>
                            </div>
                        </div>

                        <input 
                            type="range" 
                            id="progress"
                            name="progress" 
                            value="{{ old('progress', $goal->progress) }}" 
                            min="0"
                            max="100"
                            class="progress-range"
                            oninput="updateProgressDisplay(this.value)"
                            required>
                        
                        @error('progress')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Goal Stats --}}
            <div class="goal-stats-box">
                <h4><i class="fas fa-chart-bar"></i> Goal Statistics</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-label">Created</span>
                        <span class="stat-value">{{ $goal->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Last Updated</span>
                        <span class="stat-value">{{ $goal->updated_at->diffForHumans() }}</span>
                    </div>
                    @if($goal->target_date)
    @php
        $daysRemaining = \Carbon\Carbon::parse($goal->target_date)->diffInDays(now(), false);
        $daysRemaining = ($daysRemaining < 0) ? ceil(abs($daysRemaining)) * -1 : ceil($daysRemaining);
    @endphp
    <div class="stat-item">
        <span class="stat-label">Time Remaining</span>
        <span class="stat-value {{ $daysRemaining > 0 ? 'overdue' : '' }}">
            @if($daysRemaining > 0)
                {{ $daysRemaining }} days overdue
            @else
                {{ abs($daysRemaining) }} days left
            @endif
        </span>
    </div>
@endif
                    <div class="stat-item">
                        <span class="stat-label">Current Progress</span>
                        <span class="stat-value">{{ $goal->progress }}%</span>
                    </div>
                </div>
            </div>

            {{-- Update Button --}}
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Update Goal
                </button>

                <a href="{{ route('goals.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
function updateProgressDisplay(value) {
    document.getElementById('progressDisplay').textContent = value + '%';
    const bar = document.getElementById('progressPreview');
    bar.style.width = value + '%';

    if (value < 40) bar.style.background = 'linear-gradient(90deg, #f87171, #ef4444)';
    else if (value < 80) bar.style.background = 'linear-gradient(90deg, #facc15, #eab308)';
    else bar.style.background = 'linear-gradient(90deg, #34d399, #059669)';
}
</script>
@endpush

@endsection
