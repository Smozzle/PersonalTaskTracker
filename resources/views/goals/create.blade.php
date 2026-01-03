@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/goal-form.css') }}">
@endpush

@section('title', 'Create Goal')
@section('header', 'Create New Goal')

@section('content')
<div class="goal-form-wrapper">
    
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        <span class="separator">/</span>
        <a href="{{ route('goals.index') }}"><i class="fas fa-bullseye"></i> Goals</a>
        <span class="separator">/</span>
        <span class="current">Create</span>
    </div>

    <div class="goal-form-container">
        
        {{-- Form Header --}}
        <div class="form-header">
            <div class="form-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div>
                <h2>Create New Goal</h2>
                <p>Set a new objective and track your progress</p>
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

        <form action="{{ route('goals.store') }}" method="POST" class="goal-form">
            @csrf

            {{-- Basic Information Section --}}
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i> Goal Details
                </h3>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="title">
                            Goal Title <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="title"
                            name="title" 
                            value="{{ old('title') }}" 
                            required 
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="e.g., Learn a new language, Run a marathon, Read 12 books"
                            maxlength="255">
                        @error('title')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-hint">Choose a clear and specific goal title</small>
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
                            placeholder="Describe your goal in detail... Why is this important? How will you achieve it?">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-hint">Add details about your goal and your plan to achieve it</small>
                    </div>
                </div>
            </div>

            {{-- Goal Settings Section --}}
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-sliders-h"></i> Goal Settings
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="target_date">
                            <i class="fas fa-calendar"></i> Target Date
                        </label>
                        <input 
                            type="date" 
                            id="target_date"
                            name="target_date" 
                            value="{{ old('target_date') }}" 
                            class="form-control @error('target_date') is-invalid @enderror"
                            min="{{ date('Y-m-d') }}">
                        @error('target_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-hint">When do you want to achieve this goal?</small>
                    </div>
                </div>
            </div>

            {{-- Motivation Section --}}
            <div class="motivation-box">
                <div class="motivation-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="motivation-content">
                    <h4>Tips for Setting Effective Goals</h4>
                    <ul>
                        <li><strong>Be Specific:</strong> Clearly define what you want to achieve</li>
                        <li><strong>Set a Deadline:</strong> Having a target date increases motivation</li>
                        <li><strong>Make it Measurable:</strong> Track your progress with percentages</li>
                        <li><strong>Stay Realistic:</strong> Challenge yourself but keep it achievable</li>
                    </ul>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Create Goal
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
}
</script>
@endpush

@endsection