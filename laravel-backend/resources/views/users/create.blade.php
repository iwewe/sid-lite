@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1a202c;">Tambah User Baru</h1>
    <p style="color: #718096;">Buat akun user baru untuk sistem</p>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap *</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="{{ old('name') }}"
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
                value="{{ old('email') }}"
                required
            >
            @error('email')
                <span style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password *</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                required
            >
            <small style="color: #718096;">Minimal 6 karakter</small>
            @error('password')
                <span style="color: #e53e3e; font-size: 0.875rem; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Password *</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
                required
            >
        </div>

        <div class="form-group">
            <label for="role" class="form-label">Role *</label>
            <select id="role" name="role" class="form-control" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>👑 Admin (Full Access)</option>
                <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>⚙️ Operator (Input & Edit Data)</option>
                <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>👁️ Viewer (Read Only)</option>
            </select>
            @error('role')
                <span style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-top: 30px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary">
                Simpan User
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
