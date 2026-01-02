@extends('layouts.app')

@section('title', 'Edit Profile')
@section('header', 'Edit Profile')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">

<div class="profile-container">
    
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-grid">
        
        <!-- Profile Picture Section -->
        <div class="profile-picture-section">
            <div class="picture-card">
                <h3><i class="fas fa-user-circle"></i> Profile Picture</h3>
                <div class="current-picture">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" id="preview-image">
                    @else
                        <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile" id="preview-image">
                    @endif
                </div>
                <p class="picture-info">
                    <i class="fas fa-info-circle"></i> JPG, JPEG, PNG - Max 2MB
                </p>
            </div>
        </div>

        <!-- Profile Information Form -->
        <div class="profile-form-section">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information Card -->
                <div class="form-card">
                    <h3><i class="fas fa-user-edit"></i> Basic Information</h3>
                    
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user"></i> Username <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name"
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            class="@error('name') is-invalid @enderror"
                            required
                        >
                        @error('name')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            value="{{ old('email', $user->email) }}" 
                            class="@error('email') is-invalid @enderror"
                            required
                        >
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="profile_picture">
                            <i class="fas fa-camera"></i> Change Profile Picture
                        </label>
                        <input 
                            type="file" 
                            id="profile_picture"
                            name="profile_picture" 
                            accept="image/jpeg,image/png,image/jpg"
                            class="@error('profile_picture') is-invalid @enderror"
                            onchange="previewImage(event)"
                        >
                        @error('profile_picture')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Change Password Card -->
                <div class="form-card">
                    <h3><i class="fas fa-lock"></i> Change Password</h3>
                    <p class="card-description">Leave blank if you don't want to change your password</p>
                    
                    <div class="form-group">
                        <label for="current_password">
                            <i class="fas fa-key"></i> Current Password
                        </label>
                        <input 
                            type="password" 
                            id="current_password"
                            name="current_password" 
                            class="@error('current_password') is-invalid @enderror"
                            placeholder="Enter current password"
                        >
                        @error('current_password')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password">
                            <i class="fas fa-lock"></i> New Password
                        </label>
                        <input 
                            type="password" 
                            id="new_password"
                            name="new_password" 
                            class="@error('new_password') is-invalid @enderror"
                            placeholder="Enter new password (min. 6 characters)"
                        >
                        @error('new_password')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">
                            <i class="fas fa-lock"></i> Confirm New Password
                        </label>
                        <input 
                            type="password" 
                            id="new_password_confirmation"
                            name="new_password_confirmation" 
                            placeholder="Confirm new password"
                        >
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
// Preview image before upload
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('preview-image');
        preview.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

@endsection