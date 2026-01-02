<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Personal Task Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="welcome-page">
    <div class="landing-container">
        <h1>Welcome to Personal Task Tracker</h1>
        <p>Stay organized, track your goals, and achieve more every day!</p>
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
    </div>
</body>
</html>
