@extends('layouts.app')

@section('title', 'Manajemen pengguna')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">AKSES APLIKASI</p>
            <h1>Manajemen pengguna</h1>
            <p class="muted">Kelola akun admin dan anggota SiPustaka.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('users.create') }}"><i class="bi bi-person-plus"></i> Tambah pengguna</a>
    </div>

    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Bergabung</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong>@if ($user->is(auth()->user())) <span class="muted small"> (akun Anda)</span> @endif</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="role-pill {{ $user->isAdmin() ? 'role-admin' : '' }}">{{ $user->isAdmin() ? 'Admin' : 'Anggota' }}</span></td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="action-group">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('users.edit', $user) }}">Edit</a>
                                    @if ($user->is(auth()->user()))
                                        <button class="btn btn-sm btn-outline-danger" type="button" disabled title="Akun yang sedang digunakan tidak dapat dihapus">Hapus</button>
                                    @else
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="5">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $users->links() }}</div>
    </div>
@endsection
