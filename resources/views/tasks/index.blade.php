@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/task-index.css') }}">
@endpush

@section('title', 'My Tasks')
@section('header', 'Task Management')

@section('content')

<div class="task-management-container">

    {{-- Top Actions Bar --}}
    <div class="top-actions-bar">
        <div class="view-toggle">
            <button class="view-btn active" data-view="grid" title="Grid View">
                <i class="fas fa-th-large"></i>
            </button>
            <button class="view-btn" data-view="list" title="List View">
                <i class="fas fa-list"></i>
            </button>
        </div>

        <div class="filters-search">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="search-input" placeholder="Search tasks...">
            </div>

            <select id="filter-status" class="filter-select">
                <option value="">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
            </select>

            <select id="filter-priority" class="filter-select">
                <option value="">All Priorities</option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select>

            <select id="filter-category" class="filter-select">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('tasks.create') }}" class="add-task-btn">
            <i class="fas fa-plus"></i> New Task
        </a>
    </div>

    {{-- Task Stats --}}
    <div class="task-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;">
                <i class="fas fa-list-check"></i>
            </div>
            <div class="stat-info">
                <h4>Total Tasks</h4>
                <p class="stat-number">{{ $tasks->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h4>Pending</h4>
                <p class="stat-number">{{ $tasks->where('status', 'Pending')->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #ddd6fe; color: #8b5cf6;">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="stat-info">
                <h4>Ongoing</h4>
                <p class="stat-number">{{ $tasks->where('status', 'Ongoing')->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Completed</h4>
                <p class="stat-number">{{ $tasks->where('status', 'Completed')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Tasks Grid View --}}
    <div class="tasks-container grid-view" id="tasks-container">
        @forelse($tasks as $task)
            <div class="task-card" 
                 data-status="{{ $task->status }}" 
                 data-priority="{{ $task->priority }}"
                 data-category="{{ $task->category_id ?? '' }}"
                 data-title="{{ strtolower($task->title) }}"
                 data-description="{{ strtolower($task->description ?? '') }}">
                
                {{-- Card Header --}}
                <div class="task-card-header">
                    <div class="task-priority priority-{{ strtolower($task->priority) }}">
                        <i class="fas fa-flag"></i> {{ $task->priority }}
                    </div>
                    <div class="task-actions-dropdown">
                        <button class="dropdown-trigger">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="{{ route('tasks.edit', $task->id) }}">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if($task->status !== 'Completed')
                                <form action="{{ route('tasks.markAsDone', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit">
                                        <i class="fas fa-check"></i> Mark Complete
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" 
                                  onsubmit="return confirm('Delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-action">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="task-card-body">
                    <h3 class="task-title">{{ $task->title }}</h3>
                    <p class="task-description">
                        {{ $task->description ? Str::limit($task->description, 80) : 'No description' }}
                    </p>

                    @if($task->category)
                        <div class="task-category" style="background-color: {{ $task->category->color_code }}20; color: {{ $task->category->color_code }};">
                            <i class="fas fa-tag"></i> {{ $task->category->name }}
                        </div>
                    @endif
                </div>

                {{-- Card Footer --}}
                <div class="task-card-footer">
                    <div class="task-meta">
                        @if($task->due_date)
                            <span class="task-due-date {{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'Completed' ? 'overdue' : '' }}">
                                <i class="fas fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                            </span>
                        @else
                            <span class="task-due-date">
                                <i class="fas fa-calendar"></i> No due date
                            </span>
                        @endif
                    </div>
                    <div class="task-status status-{{ strtolower($task->status) }}">
                        {{ $task->status }}
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-tasks"></i>
                <h3>No tasks yet</h3>
                <p>Create your first task to get started!</p>
                <a href="{{ route('tasks.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Create Task
                </a>
            </div>
        @endforelse
    </div>

    {{-- Empty State for Filtered Results --}}
    <div class="empty-state" id="no-results" style="display: none;">
        <i class="fas fa-search"></i>
        <h3>No tasks found</h3>
        <p>Try adjusting your filters or search terms</p>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tasksContainer = document.getElementById('tasks-container');
    const noResults = document.getElementById('no-results');
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('filter-status');
    const priorityFilter = document.getElementById('filter-priority');
    const categoryFilter = document.getElementById('filter-category');
    const viewBtns = document.querySelectorAll('.view-btn');

    // View toggle
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (this.dataset.view === 'grid') {
                tasksContainer.classList.remove('list-view');
                tasksContainer.classList.add('grid-view');
            } else {
                tasksContainer.classList.remove('grid-view');
                tasksContainer.classList.add('list-view');
            }
        });
    });

    // Filter function
    function filterTasks() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const priorityValue = priorityFilter.value;
        const categoryValue = categoryFilter.value;
        
        const taskCards = document.querySelectorAll('.task-card');
        let visibleCount = 0;

        taskCards.forEach(card => {
            const title = card.dataset.title;
            const description = card.dataset.description;
            const status = card.dataset.status;
            const priority = card.dataset.priority;
            const category = card.dataset.category;

            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;
            const matchesPriority = !priorityValue || priority === priorityValue;
            const matchesCategory = !categoryValue || category === categoryValue;

            if (matchesSearch && matchesStatus && matchesPriority && matchesCategory) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0 && taskCards.length > 0) {
            tasksContainer.style.display = 'none';
            noResults.style.display = 'flex';
        } else {
            tasksContainer.style.display = '';
            noResults.style.display = 'none';
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterTasks);
    statusFilter.addEventListener('change', filterTasks);
    priorityFilter.addEventListener('change', filterTasks);
    categoryFilter.addEventListener('change', filterTasks);

    // Dropdown toggle
    document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-content').forEach(d => {
                if (d !== dropdown) d.classList.remove('show');
            });
            
            dropdown.classList.toggle('show');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-content').forEach(d => {
            d.classList.remove('show');
        });
    });
});
</script>
@endpush

@endsection
