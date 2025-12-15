@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 700; color: #1a202c;">Kelola User</h1>
        <p style="color: #718096;">Manajemen user sistem (Admin only)</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        + Tambah User Baru
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->role === 'admin')
                        <span style="background: #f0fff4; color: #38a169; padding: 4px 12px; border-radius: 999px; font-size: 0.875rem;">
                            👑 Admin
                        </span>
                    @elseif($user->role === 'operator')
                        <span style="background: #ebf8ff; color: #3182ce; padding: 4px 12px; border-radius: 999px; font-size: 0.875rem;">
                            ⚙️ Operator
                        </span>
                    @else
                        <span style="background: #f7fafc; color: #718096; padding: 4px 12px; border-radius: 999px; font-size: 0.875rem;">
                            👁️ Viewer
                        </span>
                    @endif
                </td>
                <td>
                    @if($user->is_active)
                        <span style="background: #f0fff4; color: #38a169; padding: 4px 12px; border-radius: 999px; font-size: 0.875rem;">
                            ✓ Aktif
                        </span>
                    @else
                        <span style="background: #fff5f5; color: #e53e3e; padding: 4px 12px; border-radius: 999px; font-size: 0.875rem;">
                            ✗ Nonaktif
                        </span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.875rem;">
                            Edit
                        </a>

                        <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button
                                type="submit"
                                class="btn {{ $user->is_active ? 'btn-secondary' : 'btn-success' }}"
                                style="padding: 6px 12px; font-size: 0.875rem;"
                                @if($user->id === auth()->id()) disabled title="Tidak bisa menonaktifkan diri sendiri" @endif
                            >
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn btn-danger"
                                style="padding: 6px 12px; font-size: 0.875rem;"
                                @if($user->id === auth()->id()) disabled title="Tidak bisa menghapus diri sendiri" @endif
                            >
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #718096; padding: 40px;">
                    Belum ada user
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $users->links() }}
    </div>
</div>

@endsection
