@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <div class="auth-wrap">
        <div class="auth-intro">
            <p class="eyebrow">JADI BAGIAN SIPUSTAKA</p>
            <h1>Perjalanan membaca dimulai di sini.</h1>
            <p class="muted">Buat akun anggota untuk menjelajahi koleksi dan mengajukan peminjaman.</p>
            <div class="auth-illustration"><i class="bi bi-book-half"></i><span>Buka halaman, buka kemungkinan.</span></div>
        </div>
        <form class="card auth-card" action="{{ route('register.store') }}" method="POST">
            @csrf
            <h2>Buat akun anggota</h2>
            <p class="muted">Isi data berikut untuk memulai.</p>
            @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
            <div class="field">
                <label for="name">Nama lengkap</label>
                <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                @error('name') <small class="field-error">{{ $message }}</small> @enderror
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                @error('email') <small class="field-error">{{ $message }}</small> @enderror
            </div>
            <div class="field">
                <label for="password">Kata sandi</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
                <small class="muted">Minimal 8 karakter.</small>
                @error('password') <small class="field-error">{{ $message }}</small> @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Ulangi kata sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit">Daftar</button>
            <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
        </form>
    </div>
@endsection
