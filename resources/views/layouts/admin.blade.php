<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Admin Dashboard') - Personal Task Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<body>
    <div class="sidebar admin-sidebar" id="sidebar">
        <!-- Admin Sidebar Header -->
        <div class="sidebar-header admin-header">
            <button class="logo-toggle-btn" id="toggle-btn" title="Toggle Sidebar">
                <div class="logo-icon admin-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="logo-text">
                    <h2>Admin Panel</h2>
                    <span>Control Center</span>
                </div>
            </button>
        </div>

        <!-- Admin Navigation Links -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <span class="nav-section-title">Admin Dashboard</span>
                <ul class="nav-links">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                           title="Dashboard">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}" 
                           class="nav-link {{ request()->routeIs('admin.users') || request()->routeIs('admin.user-details') ? 'active' : '' }}" 
                           title="User Management">
                            <i class="fas fa-users"></i>
                            <span>User Management</span>
                            @php
                                $totalUsers = \App\Models\User::count();
                            @endphp
                            <span class="nav-badge">{{ $totalUsers }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.statistics') }}" 
                           class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}" 
                           title="Statistics">
                            <i class="fas fa-chart-bar"></i>
                            <span>Statistics</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Admin Sidebar Footer -->
      <div class="sidebar-footer">
            <a href="{{ route('admin.settings') }}" 
               class="footer-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" 
               title="Admin Settings">
                <i class="fas fa-cog" aria-hidden="true"></i>
                <span>Settings</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="footer-link logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <header class="dashboard-header admin-header-bar">
            <div class="header-left">
                <button class="mobile-toggle" id="mobile-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>@yield('header')</h1>
            </div>
            <div class="header-right">
                <!-- Notification Button -->
                <div class="notification-container">
                    <button class="notification-btn" id="notification-btn" title="Notifications" onclick="toggleNotificationDropdown()">
                        <i class="fas fa-bell"></i>
                        @php
                            $unreadCount = Auth::user()->unreadNotifications->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="notification-badge" id="notification-count">{{ $unreadCount }}</span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div class="notification-dropdown" id="notification-dropdown">
                        <div class="dropdown-header-notif">
                            <h3>Notifications</h3>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-mark-read-small">
                                        Mark all read
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="dropdown-body-notif">
                            @forelse(Auth::user()->notifications()->take(5)->get() as $notification)
                                <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="notification-form">
                                    @csrf
                                    <button type="submit" class="notification-dropdown-item {{ $notification->read_at ? 'read' : 'unread' }}">
                                        <div class="dropdown-icon-notif {{ $notification->data['color'] ?? 'blue' }}">
                                            <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                                        </div>
                                        <div class="dropdown-content-notif">
                                            <h4>{{ $notification->data['title'] ?? 'Notification' }}</h4>
                                            <p>{{ Str::limit($notification->data['message'] ?? '', 60) }}</p>
                                            <span class="dropdown-time-notif">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                    </button>
                                </form>
                            @empty
                                <div class="dropdown-empty-notif">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>No notifications</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="dropdown-footer-notif">
                            <a href="{{ route('notifications.index') }}" class="btn-view-all-notif">
                                View All Notifications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown">
                    <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}" 
                         alt="Profile Picture" class="profile-picture" id="profile-btn">
                    <div class="dropdown-menu" id="profile-menu">
                        <div class="dropdown-header">
                            <strong>{{ Auth::user()->name }}</strong>
                            <small>{{ Auth::user()->email }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-shield-alt"></i> My Dashboard</a>
                        <a href="{{ route('admin.settings') }}"><i class="fas fa-cog"></i> Admin Settings</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Success/Error Messages -->
        <div class="alert-container">
            @if(session('success'))
                <div class="alert alert-success" id="alert-message">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button class="alert-close" onclick="closeAlert()">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" id="alert-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                    <button class="alert-close" onclick="closeAlert()">&times;</button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info" id="alert-message">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('info') }}</span>
                    <button class="alert-close" onclick="closeAlert()">&times;</button>
                </div>
            @endif
        </div>

        <section>
            @yield('content')
        </section>
    </div>

    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const mobileToggle = document.getElementById('mobile-toggle');
    const overlay = document.getElementById('sidebar-overlay');

    // Desktop toggle
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('adminSidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // Mobile toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
        });
    }

    // Close mobile sidebar
    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }

    // Remember sidebar state
    if (localStorage.getItem('adminSidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }

    // Profile dropdown
    const profileBtn = document.getElementById('profile-btn');
    const profileMenu = document.getElementById('profile-menu');

    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            profileMenu.classList.toggle('show');
        });

        document.addEventListener('click', function () {
            profileMenu.classList.remove('show');
        });
    }

    // Auto-hide alert after 5 seconds
    const alertMessage = document.getElementById('alert-message');
    if (alertMessage) {
        setTimeout(function() {
            alertMessage.style.opacity = '0';
            setTimeout(function() {
                alertMessage.style.display = 'none';
            }, 300);
        }, 5000);
    }

    // Auto-refresh notifications every 60 seconds
    setInterval(refreshNotifications, 60000);
});

// Close alert manually
function closeAlert() {
    const alertMessage = document.getElementById('alert-message');
    if (alertMessage) {
        alertMessage.style.opacity = '0';
        setTimeout(function() {
            alertMessage.style.display = 'none';
        }, 300);
    }
}

// Toggle notification dropdown
function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    dropdown.classList.toggle('active');
    
    if (dropdown.classList.contains('active')) {
        refreshNotifications();
    }
}

// Close notification dropdown when clicking outside
document.addEventListener('click', function(event) {
    const notifBtn = document.getElementById('notification-btn');
    const dropdown = document.getElementById('notification-dropdown');
    
    if (notifBtn && dropdown) {
        if (!notifBtn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove('active');
        }
    }
});

// Refresh notifications via AJAX
function refreshNotifications() {
    fetch('{{ route("notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-count');
            
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count;
                } else {
                    const notifBtn = document.getElementById('notification-btn');
                    const newBadge = document.createElement('span');
                    newBadge.className = 'notification-badge';
                    newBadge.id = 'notification-count';
                    newBadge.textContent = data.count;
                    notifBtn.appendChild(newBadge);
                }
            } else {
                if (badge) badge.remove();
            }
        })
        .catch(error => console.error('Error refreshing notifications:', error));
}
    </script>

    @stack('scripts')
</body>
</html>

<style>
/* Admin-specific sidebar styling */
.admin-sidebar {
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
}

.admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-bottom: 3px solid #8b5cf6;
}

.admin-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.admin-header-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.admin-header-bar h1 {
    color: white;
}

.admin-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    margin-bottom: 1rem;
}

.admin-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.admin-details {
    display: flex;
    flex-direction: column;
}

.admin-details strong {
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
}

.admin-details span {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.75rem;
}

/* Override nav link colors for admin sidebar */
.admin-sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
}

.admin-sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.admin-sidebar .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.admin-sidebar .nav-section-title {
    color: rgba(255, 255, 255, 0.5);
}

.admin-sidebar .footer-link {
    color: rgba(255, 255, 255, 0.8);
}

.admin-sidebar .footer-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.admin-sidebar .nav-badge {
    background: #8b5cf6;
}
</style>