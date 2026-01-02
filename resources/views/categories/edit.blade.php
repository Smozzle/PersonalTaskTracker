@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/category-form.css') }}">
@endpush

@section('title', 'Edit Category')
@section('header', 'Edit Category')

@section('content')
<div class="category-form-wrapper">
    
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        <span class="separator">/</span>
        <a href="{{ route('categories.index') }}"><i class="fas fa-layer-group"></i> Categories</a>
        <span class="separator">/</span>
        <span class="current">Edit</span>
    </div>

    <div class="category-form-container">
        
        {{-- Form Header --}}
        <div class="form-header">
            <div class="form-icon" style="background: rgba(255, 255, 255, 0.25);">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Edit Category</h2>
                <p>Update "{{ $category->name }}" details</p>
            </div>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Oops! Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="category-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i> Category Details
                </h3>

                <div class="form-group">
                    <label for="name">
                        Category Name <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        value="{{ old('name', $category->name) }}" 
                        required 
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="e.g., Work, Personal, Fitness, Study"
                        maxlength="255">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-hint">Choose a descriptive name for your category</small>
                </div>

                <div class="form-group">
                    <label for="color_code">
                        Category Color <span class="required">*</span>
                    </label>
                    <div class="color-picker-container">
                        <div class="color-preview-large" id="color-preview" style="background: {{ old('color_code', $category->color_code ?? '#667eea') }};"></div>
                        <div class="color-input-group">
                            <input 
                                type="color" 
                                id="color_code"
                                name="color_code" 
                                value="{{ old('color_code', $category->color_code ?? '#667eea') }}" 
                                class="color-input"
                                onchange="updateColorPreview(this.value)">
                            <input 
                                type="text" 
                                id="color_hex"
                                value="{{ old('color_code', $category->color_code ?? '#667eea') }}" 
                                class="hex-input"
                                placeholder="#667eea"
                                maxlength="7"
                                oninput="updateColorFromHex(this.value)">
                        </div>
                    </div>
                    @error('color_code')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-hint">Pick a color to visually identify this category</small>
                </div>

                <div class="color-presets">
                    <label>Quick Color Presets:</label>
                    <div class="preset-colors">
                        <button type="button" class="preset-btn" style="background: #ef4444;" onclick="setColor('#ef4444')" title="Red"></button>
                        <button type="button" class="preset-btn" style="background: #f97316;" onclick="setColor('#f97316')" title="Orange"></button>
                        <button type="button" class="preset-btn" style="background: #f59e0b;" onclick="setColor('#f59e0b')" title="Amber"></button>
                        <button type="button" class="preset-btn" style="background: #84cc16;" onclick="setColor('#84cc16')" title="Lime"></button>
                        <button type="button" class="preset-btn" style="background: #10b981;" onclick="setColor('#10b981')" title="Green"></button>
                        <button type="button" class="preset-btn" style="background: #06b6d4;" onclick="setColor('#06b6d4')" title="Cyan"></button>
                        <button type="button" class="preset-btn" style="background: #3b82f6;" onclick="setColor('#3b82f6')" title="Blue"></button>
                        <button type="button" class="preset-btn" style="background: #667eea;" onclick="setColor('#667eea')" title="Indigo"></button>
                        <button type="button" class="preset-btn" style="background: #8b5cf6;" onclick="setColor('#8b5cf6')" title="Purple"></button>
                        <button type="button" class="preset-btn" style="background: #ec4899;" onclick="setColor('#ec4899')" title="Pink"></button>
                        <button type="button" class="preset-btn" style="background: #64748b;" onclick="setColor('#64748b')" title="Slate"></button>
                        <button type="button" class="preset-btn" style="background: #1e293b;" onclick="setColor('#1e293b')" title="Dark"></button>
                    </div>
                </div>

                {{-- Category Info --}}
                <div class="category-meta">
                    <div class="meta-item">
                        <i class="fas fa-tasks"></i>
                        <span><strong>{{ $category->tasks()->where('user_id', Auth::id())->count() }}</strong> tasks in this category</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Created on <strong>{{ $category->created_at->format('M d, Y') }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Update Category
                </button>
                <a href="{{ route('categories.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category? Tasks will not be deleted, just uncategorized.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash"></i> Delete Category
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateColorPreview(color) {
    document.getElementById('color-preview').style.background = color;
    document.getElementById('color-hex').value = color.toUpperCase();
}

function updateColorFromHex(hex) {
    if (hex.match(/^#[0-9A-F]{6}$/i)) {
        document.getElementById('color_code').value = hex;
        document.getElementById('color-preview').style.background = hex;
    }
}

function setColor(color) {
    document.getElementById('color_code').value = color;
    document.getElementById('color-hex').value = color;
    document.getElementById('color-preview').style.background = color;
}
</script>
@endpush

@endsection