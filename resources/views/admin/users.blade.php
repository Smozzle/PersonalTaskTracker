@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-users.css') }}">
@endpush

@section('header', '👥 User Management')

@section('content')
<div class="users-management">
    <!-- Header with Search -->
    <div class="users-header">
        <div class="header-left">
            <h2>All Users</h2>
            <span class="user-count">{{ $users->total() }} total users</span>
        </div>
        
        <div class="header-actions">
            <form action="{{ route('admin.users') }}" method="GET" class="search-form">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search by name or email..." 
                           value="{{ $search }}" class="search-input">
                    <button type="submit" class="search-btn">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters & Sort -->
    <div class="filters-bar">
        <div class="filter-group">
            <label>Sort by:</label>
            <form action="{{ route('admin.users') }}" method="GET" id="sortForm">
                @if($search)
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
                <select name="sort_by" onchange="document.getElementById('sortForm').submit()" class="sort-select">
                    <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Join Date</option>
                    <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="email" {{ $sortBy == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="tasks_count" {{ $sortBy == 'tasks_count' ? 'selected' : '' }}>Task Count</option>
                </select>
                <select name="sort_order" onchange="document.getElementById('sortForm').submit()" class="sort-select">
                    <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </form>
        </div>

        @if($search)
            <a href="{{ route('admin.users') }}" class="clear-filter">
                <i class="fas fa-times"></i> Clear Search
            </a>
        @endif
    </div>

    <!-- Users Table -->
    <div class="users-table-container">
        <table class="users-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Tasks</th>
                    <th>Goals</th>
                    <th>Achievements</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}" 
                                     alt="{{ $user->name }}" class="user-avatar">
                                <div>
                                    <a href="{{ route('admin.user-details', $user->id) }}" class="user-name">
                                        {{ $user->name }}
                                    </a>
                                    @if($user->id === Auth::id())
                                        <span class="badge-you">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_admin)
                                <span class="role-badge admin">
                                    <i class="fas fa-shield-alt"></i> Admin
                                </span>
                            @else
                                <span class="role-badge user">
                                    <i class="fas fa-user"></i> User
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="stat-badge">
                                <i class="fas fa-tasks"></i> {{ $user->tasks_count }}
                            </span>
                        </td>
                        <td>
                            <span class="stat-badge">
                                <i class="fas fa-bullseye"></i> {{ $user->goals_count }}
                            </span>
                        </td>
                        <td>
                            <span class="stat-badge">
                                <i class="fas fa-trophy"></i> {{ $user->achievements_count }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.user-details', $user->id) }}" 
                                   class="btn-action btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('admin.toggle-admin', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-admin" 
                                                title="{{ $user->is_admin ? 'Revoke Admin' : 'Grant Admin' }}">
                                            <i class="fas {{ $user->is_admin ? 'fa-user-minus' : 'fa-user-plus' }}"></i>
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.delete-user', $user) }}" method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure? This will delete all user data!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="btn-action btn-disabled" title="Cannot modify yourself">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-content">
                                <i class="fas fa-users-slash"></i>
                                <p>No users found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="pagination-container">
            {{ $users->appends(['search' => $search, 'sort_by' => $sortBy, 'sort_order' => $sortOrder])->links() }}
        </div>
    @endif
</div>
@endsection