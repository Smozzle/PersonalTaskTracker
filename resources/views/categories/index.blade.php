@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/category-index.css') }}">
@endpush

@section('title', 'Task Categorization')
@section('header', 'Task Categorization')

@section('content')
<div class="category-management-container">

    {{-- Top Action Bar --}}
    <div class="top-action-bar">
        <div class="page-info">
            <h2><i class="fas fa-layer-group"></i> Manage Categories</h2>
            <p>Organize your tasks with custom categories and colors</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn-add-category">
            <i class="fas fa-plus"></i> New Category
        </a>
    </div>

    {{-- Category Stats --}}
    <div class="category-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <h4>Total Categories</h4>
                <p>{{ $categories->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-info">
                <h4>Total Tasks</h4>
                <p>{{ $tasks->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                <i class="fas fa-tag"></i>
            </div>
            <div class="stat-info">
                <h4>Categorized</h4>
                <p>{{ $tasks->whereNotNull('category_id')->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #e0e7ff; color: #6366f1;">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="stat-info">
                <h4>Uncategorized</h4>
                <p>{{ $tasks->whereNull('category_id')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Categories Grid --}}
    <div class="categories-section">
        <h3 class="section-title">
            <i class="fas fa-palette"></i> Your Categories
        </h3>

        <div class="categories-grid">
            @forelse($categories as $category)
                @php
                    $taskCount = $category->tasks()->where('user_id', Auth::id())->count();
                @endphp
                <div class="category-card" style="border-left: 4px solid {{ $category->color_code }};">
                    <div class="category-header">
                        <div class="category-color" style="background: {{ $category->color_code }};"></div>
                        <div class="category-info">
                            <h4>{{ $category->name }}</h4>
                            <p>{{ $taskCount }} {{ Str::plural('task', $taskCount) }}</p>
                        </div>
                        <div class="category-actions">
                            <button class="action-btn" onclick="toggleDropdown({{ $category->id }})">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu" id="dropdown-{{ $category->id }}">
                                <a href="{{ route('categories.edit', $category->id) }}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" 
                                      onsubmit="return confirm('Delete this category? Tasks will not be deleted.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="category-footer">
                        <span class="color-badge" style="background: {{ $category->color_code }}20; color: {{ $category->color_code }};">
                            <i class="fas fa-circle"></i> {{ $category->color_name }}
                        </span>
                        <span class="created-date">
                            <i class="fas fa-calendar"></i> {{ $category->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-layer-group"></i>
                    <h3>No categories yet</h3>
                    <p>Create your first category to organize your tasks</p>
                    <a href="{{ route('categories.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Create Category
                    </a>
                </div>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script>
function toggleDropdown(id) {
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `dropdown-${id}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    const dropdown = document.getElementById(`dropdown-${id}`);
    dropdown.classList.toggle('show');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.category-actions')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});
</script>
@endpush

@endsection