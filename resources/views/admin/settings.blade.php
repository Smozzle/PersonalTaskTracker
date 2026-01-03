@extends('layouts.admin')

@section('title', 'Admin Settings')
@section('header', '⚙️ Admin Settings')

@section('content')
<div class="settings-container">
    <div class="settings-grid">
        
        <!-- Profile Settings Card -->
        <div class="settings-card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-user-circle"></i>
                    Profile Information
                </h3>
                <p>Update your admin profile details</p>
            </div>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Profile Picture Upload -->
                <div class="form-group profile-picture-group">
                    <label>Profile Picture</label>
                    <div class="profile-picture-upload">
                        <div class="current-picture">
                            <img id="preview-image" 
                                 src="{{ $admin->profile_picture ? asset('storage/' . $admin->profile_picture) : asset('images/default-profile.png') }}" 
                                 alt="Profile Picture"
                                 loading="lazy">
                        </div>
                        <div class="upload-controls">
                            <input type="file" 
                                   name="profile_picture" 
                                   id="profile_picture" 
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            <label for="profile_picture" class="btn btn-secondary">
                                <i class="fas fa-camera"></i>
                                Choose Photo
                            </label>
                            @if($admin->profile_picture)
                                <button type="button" class="btn btn-danger-outline" onclick="removePicture()">
                                    <i class="fas fa-trash"></i>
                                    Remove
                                </button>
                            @endif
                            <small class="form-text">JPG, PNG or GIF (Max: 2MB)</small>
                        </div>
                    </div>
                    @error('profile_picture')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Name -->
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $admin->name) }}" 
                           required
                           placeholder="Enter your full name">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $admin->email) }}" 
                           required
                           placeholder="your.email@example.com">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Admin Badge -->
                <div class="form-group">
                    <label>Account Type</label>
                    <div class="badge-display">
                        <span class="badge admin-badge">
                            <i class="fas fa-shield-alt"></i>
                            Administrator
                        </span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Settings Card -->
        <div class="settings-card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-lock"></i>
                    Change Password
                </h3>
                <p>Update your password to keep your account secure</p>
            </div>
            
            <form action="{{ route('admin.settings.password') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div class="form-group">
                    <label for="current_password">
                        <i class="fas fa-key"></i>
                        Current Password
                    </label>
                    <div class="password-input-wrapper">
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               required
                               placeholder="Enter current password">
                        <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        New Password
                    </label>
                    <div class="password-input-wrapper">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               required
                               placeholder="Enter new password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text">Minimum 8 characters</small>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="fas fa-lock"></i>
                        Confirm New Password
                    </label>
                    <div class="password-input-wrapper">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-control" 
                               required
                               placeholder="Confirm new password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-shield-alt"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('styles')
<style>
.settings-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.settings-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.settings-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.card-header h3 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.card-header p {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.error-message {
    display: block;
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.form-text {
    display: block;
    color: #6b7280;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* Profile Picture Upload */
.profile-picture-group {
    margin-bottom: 2rem;
}

.profile-picture-upload {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.current-picture {
    position: relative;
}

.current-picture img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #f3f4f6;
    transition: border-color 0.3s ease;
}

.current-picture:hover img {
    border-color: #8b5cf6;
}

.upload-controls {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.upload-controls input[type="file"] {
    display: none;
}

.upload-controls label {
    cursor: pointer;
    margin: 0;
}

/* Password Input Wrapper */
.password-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input-wrapper .form-control {
    padding-right: 3rem;
}

.toggle-password {
    position: absolute;
    right: 0.75rem;
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
    transition: color 0.2s ease;
}

.toggle-password:hover {
    color: #8b5cf6;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger-outline {
    background: white;
    color: #ef4444;
    border: 2px solid #ef4444;
}

.btn-danger-outline:hover {
    background: #ef4444;
    color: white;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #f3f4f6;
}

/* Badge Display */
.badge-display {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
}

.admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .settings-container {
        padding: 1rem;
    }

    .settings-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .profile-picture-upload {
        flex-direction: column;
        align-items: flex-start;
    }

    .current-picture img {
        width: 100px;
        height: 100px;
    }

    .upload-controls {
        width: 100%;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* Animation */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.settings-card {
    animation: slideIn 0.3s ease-out;
}

.settings-card:nth-child(1) { animation-delay: 0s; }
.settings-card:nth-child(2) { animation-delay: 0.1s; }
</style>
@endpush

@push('scripts')
<script>
// Preview image before upload
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Remove profile picture
function removePicture() {
    if (confirm('Are you sure you want to remove your profile picture?')) {
        fetch('{{ route("admin.settings.remove-picture") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to remove profile picture. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.parentElement.querySelector('.toggle-password');
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection