<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Personal Task Tracker')</title>

    {{-- Main CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    {{-- Additional page-specific CSS --}}
    @stack('styles')
</head>
<body>
    {{-- 🌐 Top Navigation Bar --}}
    <header>
        <nav style="background: #333; color: white; padding: 10px;">
            <a href="{{ url('/') }}" style="color: white; margin-right: 15px;">Home</a>
            <a href="{{ route('tasks.index') }}" style="color: white; margin-right: 15px;">My Tasks</a>
            <a href="{{ route('categories.index') }}" style="color: white; margin-right: 15px;">Categories</a>
            <a href="{{ route('achievements.index') }}" style="color: white; margin-right: 15px;">Achievements</a>

            @auth
                <span>Welcome, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:white; cursor:pointer;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" style="color: white; margin-right: 15px;">Login</a>
                <a href="{{ route('register') }}" style="color: white;">Register</a>
            @endauth
        </nav>
    </header>

    {{-- 🧩 Page Content --}}
    <main style="padding: 20px;">
        @yield('content')
    </main>

    {{-- ✅ Toast Notification (SweetAlert2) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                toast: true,
                icon: 'success',
                title: "{{ session('success') }}",
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                background: '#d4edda',
                color: '#155724',
            });
        </script>
    @elseif (session('error'))
        <script>
            Swal.fire({
                toast: true,
                icon: 'error',
                title: "{{ session('error') }}",
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                background: '#f8d7da',
                color: '#721c24',
            });
        </script>
    @elseif (session('info'))
        <script>
            Swal.fire({
                toast: true,
                icon: 'info',
                title: "{{ session('info') }}",
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                background: '#d1ecf1',
                color: '#0c5460',
            });
        </script>
    @endif

    {{-- 🔔 Reminder Popup Notifications --}}
    @auth
    <script>
        // Ask for browser notification permission
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }

        // Function to display popup
        function showNotification(title, message) {
            if (Notification.permission === "granted") {
                new Notification(title, { body: message, icon: '{{ asset("images/reminder-icon.png") }}' });
            }
        }

        // Fetch unread reminder notifications
        async function fetchReminders() {
            try {
                const response = await fetch("{{ url('/api/notifications') }}");
                const notifications = await response.json();

                notifications.forEach(n => {
                    if (n.data && n.data.message) {
                        showNotification('Task Reminder', n.data.message);
                    }
                });
            } catch (error) {
                console.error('Error fetching reminders:', error);
            }
        }

        // Check every 30 seconds
        setInterval(fetchReminders, 30000);
    </script>
    @endauth

    {{-- Page-specific scripts --}}
    @stack('scripts')
</body>
</html>
