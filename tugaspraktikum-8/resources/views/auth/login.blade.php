@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <div class="auth-wrap">
        <div class="auth-intro">
            <p class="eyebrow">SELAMAT DATANG KEMBALI</p>
            <h1>Senang melihatmu lagi.</h1>
            <p class="muted">Masuk untuk melihat koleksi dan peminjamanmu.</p>
            <div class="auth-illustration"><i class="bi bi-book-half"></i><span>Setiap halaman punya cerita.</span></div>
        </div>
        <form class="card auth-card" action="{{ route('login.store') }}" method="POST">
            @csrf
            <h2>Masuk ke akun</h2>
            <p class="muted">Gunakan email yang terdaftar.</p>
            @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email') <small class="field-error">{{ $message }}</small> @enderror
            </div>
            <div class="field">
                <label for="password">Kata sandi</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                @error('password') <small class="field-error">{{ $message }}</small> @enderror
            </div>
            <label class="remember-label"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            <button class="btn btn-primary btn-lg w-100" type="submit">Masuk</button>
            <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
        </form>
    </div>
@endsection
