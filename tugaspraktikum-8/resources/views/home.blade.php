@extends('layouts.app')

@section('title', 'Perpustakaan digital')

@section('content')
    <section class="hero">
        <div class="hero-copy">
            <span class="hero-label"><i class="bi bi-stars"></i> PERPUSTAKAAN DIGITAL</span>
            <h1>Temukan cerita,<br>mulai <span>petualangan baru.</span></h1>
            <p>Jelajahi koleksi buku, cek ketersediaan, dan kelola peminjaman dengan mudah di SiPustaka.</p>
            <div class="hero-actions">
                <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Mulai membaca <i class="bi bi-arrow-right"></i></a>
                <a class="btn btn-light btn-lg" href="{{ route('login') }}">Masuk akun</a>
            </div>
            <div class="hero-note"><i class="bi bi-check-circle-fill"></i> Koleksi rapi, peminjaman praktis</div>
        </div>
        <div class="hero-art">
            <div class="hero-circle"></div>
            <div class="floating-note"><i class="bi bi-bookmark-heart"></i><span><strong>Ruang untuk ide</strong><small>Dimulai dari satu buku</small></span></div>
            <div class="book-stack">
                <div class="hero-book book-one"><span>IDE &amp; CERITA</span><strong>Jelajah<br>Pengetahuan</strong><small>SIPUSTAKA COLLECTION</small></div>
                <div class="hero-book book-two"><span>EDISI PILIHAN</span><strong>Ruang<br>untuk Tumbuh</strong><small>MEMBACA • BELAJAR • BERBAGI</small></div>
                <div class="hero-book book-three"><i class="bi bi-book-half"></i></div>
            </div>
            <span class="art-sparkle sparkle-one">✳</span><span class="art-sparkle sparkle-two">✦</span>
        </div>
    </section>
    <section class="feature-strip">
        <div><i class="bi bi-collection"></i><span><strong>Katalog terorganisir</strong><small>Temukan buku favoritmu</small></span></div>
        <div><i class="bi bi-arrow-left-right"></i><span><strong>Peminjaman praktis</strong><small>Pantau status kapan saja</small></span></div>
        <div><i class="bi bi-shield-check"></i><span><strong>Akses terkelola</strong><small>Admin dan anggota</small></span></div>
    </section>
@endsection
