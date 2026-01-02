<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Personal Task Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-box">

        <h2>Login</h2>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 10px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- ERROR MESSAGES --}}
        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 10px;">
                <ul style="margin:0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <input 
                type="email" 
                name="email" 
                placeholder="Email" 
                value="{{ old('email') }}" 
                required
            >

            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required
            >

            <button type="submit">Login</button>
        </form>

        <p style="text-align:center;margin-top:1rem;">
            Don’t have an account? 
            <a href="{{ route('register') }}">Register</a>
        </p>

    </div>
</body>
</html>
