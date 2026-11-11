@extends('layouts.app')

@section('title', 'Tambah akun pengguna')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">AKSES APLIKASI</p><h1>Tambah pengguna</h1><p class="muted">Buat akun baru untuk admin atau anggota.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Kembali</a>
    </div>
    <form class="card form-card" action="{{ route('users.store') }}" method="POST">
        @csrf
        @include('usermanagement::form')
        <div class="form-actions"><a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Batal</a><button class="btn btn-primary" type="submit">Simpan pengguna</button></div>
    </form>
@endsection
