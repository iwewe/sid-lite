@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1a202c;">Edit User</h1>
    <p style="color: #718096;">Update informasi user</p>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap *</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
            >
            @error('name')
                <span style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email *</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="{{ old('email', $user->email) }}"
                required
            >
            @error('email')
                <span style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="role" class="form-label">Role *</label>
            <select id="role" name="role" class="form-control" required>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>👑 Admin</option>
                <option value="operator" {{ old('role', $user->role) === 'operator' ? 'selected' : '' }}>⚙️ Operator</option>
                <option value="viewer" {{ old('role', $user->role) === 'viewer' ? 'selected' : '' }}>👁️ Viewer</option>
            </select>
            @error('role')
                <span style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="is_active" class="form-label">Status *</label>
            <select id="is_active" name="is_active" class="form-control" required>
                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>✓ Aktif</option>
                <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>✗ Nonaktif</option>
            </select>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 2px solid #e2e8f0;">

        <h3 style="margin-bottom: 15px; font-size: 1.25rem;">Ganti Password (Opsional)</h3>
        <p style="color: #718096; font-size: 0.875rem; margin-bottom: 20px;">Kosongkan jika tidak ingin mengubah password</p>

        <div class="form-group">
            <label for="password" class="form-label">Password Baru</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
            >
            <small style="color: #718096;">Minimal 6 karakter</small>
            @error('password')
                <span style="color: #e53e3e; font-size: 0.875rem; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
            >
        </div>

        <div style="margin-top: 30px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary">
                Update User
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
