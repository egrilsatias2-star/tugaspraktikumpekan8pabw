@extends('layouts.app')

@section('title', 'Ubah akun pengguna')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">AKSES APLIKASI</p><h1>Edit pengguna</h1><p class="muted">Perbarui akun {{ $user->name }}.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Kembali</a>
    </div>
    <form class="card form-card" action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        @include('usermanagement::form', ['user' => $user])
        <div class="form-actions"><a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Batal</a><button class="btn btn-primary" type="submit">Simpan perubahan</button></div>
    </form>
@endsection
