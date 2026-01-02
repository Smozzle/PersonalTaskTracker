@extends('layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endpush

@section('title', 'My Notifications')
@section('header', 'My Notifications')

@section('content')
<div class="notifications-container">
    <!-- Header -->
    <div class="notifications-header">
        <div class="header-content">
            <h1>🔔 Notifications</h1>
            <p class="subtitle">Stay updated with your tasks and goals</p>
        </div>
        
        @if($unreadCount > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn-mark-all">
                <i class="fas fa-check-double"></i>
                Mark All as Read ({{ $unreadCount }})
            </button>
        </form>
        @endif
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Notifications Stats -->
    <div class="notifications-stats">
        <div class="stat-box">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-bell"></i>
            </div>
            <div class="stat-content">
                <h3>{{ Auth::user()->notifications->count() }}</h3>
                <p>Total Notifications</p>
            </div>
        </div>
        
        <div class="stat-box">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $unreadCount }}</h3>
                <p>Unread</p>
            </div>
        </div>
        
        <div class="stat-box">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ Auth::user()->readNotifications->count() }}</h3>
                <p>Read</p>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list">
        @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
                <div class="notification-icon" style="background: var(--color-{{ $notification->data['color'] ?? 'blue' }});">
                    <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                </div>
                
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>{{ $notification->data['title'] ?? 'Notification' }}</h4>
                        <span class="notification-time">
                            <i class="fas fa-clock"></i>
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    <p class="notification-message">{{ $notification->data['message'] ?? '' }}</p>
                    
                    @if(isset($notification->data['task_title']))
                        <div class="notification-meta">
                            <span class="meta-tag">
                                <i class="fas fa-tasks"></i>
                                {{ $notification->data['task_title'] }}
                            </span>
                            @if(isset($notification->data['priority']))
                                <span class="priority-badge priority-{{ strtolower($notification->data['priority']) }}">
                                    {{ $notification->data['priority'] }}
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    @if(isset($notification->data['goal_title']))
                        <div class="notification-meta">
                            <span class="meta-tag">
                                <i class="fas fa-bullseye"></i>
                                {{ $notification->data['goal_title'] }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <div class="notification-actions">
                    @if(!$notification->read_at)
                        <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-read" title="Mark as read">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    @endif
                    
                    @if(isset($notification->data['url']))
                        <a href="{{ $notification->data['url'] }}" class="btn-action btn-view" title="View details">
                            <i class="fas fa-eye"></i>
                        </a>
                    @endif
                    
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete" title="Delete" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3>No notifications yet</h3>
                <p>You're all caught up! Notifications will appear here.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="pagination-container">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection