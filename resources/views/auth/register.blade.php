<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Personal Task Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-box">
        <h2>Create Account</h2>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- General Error Messages -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <input 
                    type="text" 
                    name="name" 
                    placeholder="Full Name" 
                    value="{{ old('name') }}" 
                    class="@error('name') is-invalid @enderror"
                    required
                >
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email" 
                    value="{{ old('email') }}" 
                    class="@error('email') is-invalid @enderror"
                    required
                >
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password (min. 6 characters)" 
                    class="@error('password') is-invalid @enderror"
                    required
                >
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Confirm Password" 
                    required
                >
            </div>

            <!-- Profile Picture -->
            <div class="form-group">
                <label for="profile_picture">Profile Picture (optional)</label>
                <input 
                    type="file" 
                    id="profile_picture"
                    name="profile_picture" 
                    accept="image/jpeg,image/png,image/jpg"
                    class="@error('profile_picture') is-invalid @enderror"
                >
                @error('profile_picture')
                    <span class="error">{{ $message }}</span>
                @enderror
                <small class="form-text">Accepted formats: JPG, JPEG, PNG. Max size: 2MB</small>
            </div>

            <button type="submit">Register</button>
        </form>

        <p class="auth-link">
            Already have an account? <a href="{{ route('login') }}">Login here</a>
        </p>
    </div>
</body>
</html>