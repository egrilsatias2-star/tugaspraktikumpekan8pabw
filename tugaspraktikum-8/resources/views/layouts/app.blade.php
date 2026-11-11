<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiPustaka') · SiPustaka</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg app-nav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="brand-mark"><i class="bi bi-book-half"></i></span>
                <span>SiPustaka<span class="brand-period">.</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                @auth
                    <div class="navbar-nav me-auto">
                        <a class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}" href="{{ route('books.index') }}">Katalog</a>
                        <a class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}" href="{{ route('loans.index') }}">Peminjaman</a>
                        @if (auth()->user()->isAdmin())
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Pengguna</a>
                        @endif
                    </div>
                    <div class="nav-account">
                        <span class="account-name">{{ auth()->user()->name }}</span>
                        <span class="role-pill">{{ auth()->user()->isAdmin() ? 'Admin' : 'Anggota' }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary" type="submit">Keluar</button>
                        </form>
                    </div>
                @else
                    <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                        <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Buat akun</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container app-main">
        @yield('content')
    </main>
    <footer class="app-footer">
        <div class="container d-flex justify-content-between flex-wrap gap-2">
            <span>© {{ now()->year }} SiPustaka</span>
            <span>Kelola koleksi. Nikmati membaca.</span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
