@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/goals.css') }}">
@endpush

@section('title', 'My Goals')
@section('header', 'Goal Tracking')

@section('content')
<div class="goals-container">

    {{-- Page Header --}}
    <div class="goals-header">
        <div class="header-content">
            <h2><i class="fas fa-bullseye"></i> My Goals</h2>
            <p>Track your progress and achieve your objectives</p>
        </div>
        <a href="{{ route('goals.create') }}" class="btn-add-goal">
            <i class="fas fa-plus"></i> New Goal
        </a>
    </div>

    {{-- Goals Statistics --}}
    <div class="goals-stats">
        @php
            $totalGoals = $goals->count();
            $completedGoals = $goals->where('status', 'Completed')->count();
            $ongoingGoals = $goals->where('status', 'Ongoing')->count();
            $avgProgress = $totalGoals > 0 ? round($goals->avg('progress'), 1) : 0;
        @endphp

        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;">
                <i class="fas fa-list-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalGoals }}</h3>
                <p>Total Goals</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $completedGoals }}</h3>
                <p>Completed</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $ongoingGoals }}</h3>
                <p>In Progress</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #e0e7ff; color: #667eea;">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $avgProgress }}%</h3>
                <p>Avg Progress</p>
            </div>
        </div>
    </div>

    {{-- Goals List --}}
    <div class="goals-grid">
        @forelse($goals as $goal)
            @php
                $daysUntilTarget = null;
                $isOverdue = false;
                if ($goal->target_date) {
                    $targetDate = \Carbon\Carbon::parse($goal->target_date);
                    $now = \Carbon\Carbon::now();
                    
                    if ($targetDate->isFuture()) {
                        // Days remaining (positive for future dates)
                        $daysUntilTarget = (int) ceil($now->diffInDays($targetDate, false));
                        $isOverdue = false;
                    } else {
                        // Days overdue (positive for past dates)
                        $daysUntilTarget = (int) ceil($targetDate->diffInDays($now, false));
                        $isOverdue = $goal->status !== 'Completed';
                    }
                }
            @endphp

            <div class="goal-card {{ $goal->status === 'Completed' ? 'completed' : '' }}" data-goal-id="{{ $goal->id }}">
                {{-- Card Header --}}
                <div class="goal-header">
                    <div class="goal-title-section">
                        <h3>{{ $goal->title }}</h3>
                        <span class="status-badge status-{{ strtolower($goal->status) }}">
                            @if($goal->status === 'Completed')
                                <i class="fas fa-check-circle"></i> Completed
                            @else
                                <i class="fas fa-spinner"></i> Ongoing
                            @endif
                        </span>
                    </div>
                    <div class="goal-actions">
                        <button class="action-btn" onclick="toggleGoalMenu({{ $goal->id }})">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="goal-menu" id="goal-menu-{{ $goal->id }}">
                            <a href="{{ route('goals.edit', $goal->id) }}">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('goals.destroy', $goal->id) }}" method="POST" 
                                  onsubmit="return confirm('Delete this goal? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-action">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                @if($goal->description)
                    <p class="goal-description">{{ Str::limit($goal->description, 120) }}</p>
                @endif

                {{-- Target Date --}}
                <div class="goal-meta">
                    @if($goal->target_date)
                        <span class="meta-item {{ $isOverdue ? 'overdue' : '' }}">
                            <i class="fas fa-calendar"></i>
                            @if($isOverdue)
                                Overdue by {{ $daysUntilTarget }} {{ Str::plural('day', $daysUntilTarget) }}
                            @else
                                {{ $daysUntilTarget }} {{ Str::plural('day', $daysUntilTarget) }} remaining
                            @endif
                        </span>
                    @else
                        <span class="meta-item">
                            <i class="fas fa-calendar"></i> No target date
                        </span>
                    @endif
                </div>

                {{-- Progress Section --}}
                <div class="progress-section">
                    <div class="progress-header">
                        <span class="progress-label">Progress</span>
                        <span class="progress-value" id="progress-value-{{ $goal->id }}">{{ $goal->progress }}%</span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="progress-track">
                        <div class="progress-fill progress-{{ $goal->progress < 40 ? 'low' : ($goal->progress < 80 ? 'medium' : 'high') }}" 
                             id="progress-fill-{{ $goal->id }}"
                             style="width: {{ $goal->progress }}%">
                        </div>
                    </div>

                    {{-- Progress Slider (only if no milestones) --}}
                    @if($goal->milestones->isEmpty())
                    <div class="progress-controls">
                        <input 
                            type="range" 
                            class="progress-slider" 
                            min="0" 
                            max="100" 
                            value="{{ $goal->progress }}"
                            data-goal-id="{{ $goal->id }}"
                            id="slider-{{ $goal->id }}">
                    </div>
                    @endif
                </div>

                {{-- Milestones Section --}}
                <div class="milestones-section">
                    <div class="milestones-header">
                        <span class="milestones-title">
                            <i class="fas fa-flag-checkered"></i>
                            Milestones ({{ $goal->milestones->where('is_completed', true)->count() }}/{{ $goal->milestones->count() }})
                        </span>
                        <button class="btn-add-milestone" onclick="openAddMilestoneModal({{ $goal->id }})">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>

                    <div class="milestones-list" id="milestones-list-{{ $goal->id }}">
                        @forelse($goal->milestones as $milestone)
                            <div class="milestone-item {{ $milestone->is_completed ? 'completed' : '' }}" data-milestone-id="{{ $milestone->id }}">
                                <div class="milestone-checkbox" onclick="toggleMilestone({{ $milestone->id }}, {{ $goal->id }})"></div>
                                <div class="milestone-content">
                                    <div class="milestone-header">
                                        <span class="milestone-title">{{ $milestone->title }}</span>
                                        <span class="milestone-percentage">{{ $milestone->target_percentage }}%</span>
                                    </div>
                                    @if($milestone->description)
                                        <p class="milestone-description">{{ $milestone->description }}</p>
                                    @endif
                                    @if($milestone->is_completed && $milestone->completed_at)
                                        <div class="milestone-meta">
                                            <span class="milestone-completed-date">
                                                <i class="fas fa-check"></i>
                                                Completed {{ $milestone->completed_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="milestone-actions">
                                    <button class="milestone-action-btn" onclick="deleteMilestone({{ $milestone->id }}, {{ $goal->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-milestones">
                                <i class="fas fa-flag-checkered"></i>
                                <p>No milestones yet. Break down your goal into smaller steps!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="goal-footer">
                    <form action="{{ route('goals.update', $goal->id) }}" method="POST" class="quick-update-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="progress" id="progress-input-{{ $goal->id }}" value="{{ $goal->progress }}">
                        <select name="status" class="status-select">
                            <option value="Ongoing" {{ $goal->status === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="Completed" {{ $goal->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <button type="submit" class="btn-update">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-bullseye"></i>
                <h3>No goals yet</h3>
                <p>Set your first goal and start tracking your progress!</p>
                <a href="{{ route('goals.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Create Your First Goal
                </a>
            </div>
        @endforelse
    </div>

</div>

{{-- Add Milestone Modal --}}
<div class="modal-overlay" id="milestone-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Milestone</h3>
            <button class="modal-close" onclick="closeMilestoneModal()">&times;</button>
        </div>
        <form id="milestone-form" class="modal-form" onsubmit="submitMilestone(event)">
            <input type="hidden" id="milestone-goal-id">
            
            <div class="form-group">
                <label for="milestone-title">Milestone Title</label>
                <input type="text" id="milestone-title" required placeholder="e.g., Complete research phase">
            </div>

            <div class="form-group">
                <label for="milestone-description">Description (optional)</label>
                <textarea id="milestone-description" rows="3" placeholder="Describe what needs to be done..."></textarea>
            </div>

            <div class="form-group">
                <label for="milestone-percentage">Target Percentage</label>
                <input type="number" id="milestone-percentage" required min="1" max="100" value="25" placeholder="25">
                <small style="color: #6b7280; font-size: 0.75rem;">This milestone contributes to what % of the goal?</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-modal-primary">
                    <i class="fas fa-save"></i> Add Milestone
                </button>
                <button type="button" class="btn-modal-secondary" onclick="closeMilestoneModal()">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Success Message --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#667eea',
        timer: 3000
    });
</script>
@endif

<script>
// CSRF Token
const csrfToken = '{{ csrf_token() }}';

// Progress slider functionality (only for goals without milestones)
document.querySelectorAll('.progress-slider').forEach(slider => {
    slider.addEventListener('input', function() {
        const goalId = this.dataset.goalId;
        const progress = this.value;
        
        // Update display
        document.getElementById(`progress-value-${goalId}`).textContent = progress + '%';
        document.getElementById(`progress-input-${goalId}`).value = progress;
        
        // Update progress bar
        const progressFill = document.getElementById(`progress-fill-${goalId}`);
        progressFill.style.width = progress + '%';
        
        // Update color class
        progressFill.className = 'progress-fill';
        if (progress < 40) {
            progressFill.classList.add('progress-low');
        } else if (progress < 80) {
            progressFill.classList.add('progress-medium');
        } else {
            progressFill.classList.add('progress-high');
        }
    });
});

// Toggle goal menu
function toggleGoalMenu(goalId) {
    const menu = document.getElementById(`goal-menu-${goalId}`);
    
    // Close other menus
    document.querySelectorAll('.goal-menu').forEach(m => {
        if (m.id !== `goal-menu-${goalId}`) {
            m.classList.remove('show');
        }
    });
    
    menu.classList.toggle('show');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.goal-actions')) {
        document.querySelectorAll('.goal-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Milestone Modal Functions
function openAddMilestoneModal(goalId) {
    document.getElementById('milestone-goal-id').value = goalId;
    document.getElementById('milestone-form').reset();
    document.getElementById('milestone-modal').classList.add('active');
}

function closeMilestoneModal() {
    document.getElementById('milestone-modal').classList.remove('active');
}

// Submit milestone
async function submitMilestone(event) {
    event.preventDefault();
    
    const goalId = document.getElementById('milestone-goal-id').value;
    const title = document.getElementById('milestone-title').value;
    const description = document.getElementById('milestone-description').value;
    const percentage = document.getElementById('milestone-percentage').value;
    
    try {
        const response = await fetch(`/goals/${goalId}/milestones`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: title,
                description: description,
                target_percentage: parseInt(percentage)
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Milestone Added!',
                text: data.message,
                timer: 2000
            });
            
            closeMilestoneModal();
            location.reload(); // Refresh to show new milestone
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to add milestone'
        });
    }
}

// Toggle milestone completion
async function toggleMilestone(milestoneId, goalId) {
    try {
        const response = await fetch(`/milestones/${milestoneId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: data.is_completed ? 'Milestone Completed! 🎉' : 'Marked as Incomplete',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // Update progress bar
            const progressFill = document.getElementById(`progress-fill-${goalId}`);
            const progressValue = document.getElementById(`progress-value-${goalId}`);
            
            progressFill.style.width = data.goal_progress + '%';
            progressValue.textContent = data.goal_progress + '%';
            
            // Reload to update milestone display
            location.reload();
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to update milestone'
        });
    }
}

// Delete milestone
async function deleteMilestone(milestoneId, goalId) {
    const result = await Swal.fire({
        title: 'Delete Milestone?',
        text: 'This action cannot be undone',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/milestones/${milestoneId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: data.message,
                    timer: 2000
                });
                
                location.reload();
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete milestone'
            });
        }
    }
}

// Close modal when clicking outside
document.getElementById('milestone-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMilestoneModal();
    }
});
</script>
@endpush

@endsection