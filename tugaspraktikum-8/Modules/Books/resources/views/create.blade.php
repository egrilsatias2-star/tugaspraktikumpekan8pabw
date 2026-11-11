@extends('layouts.app')

@section('title', 'Tambah buku')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">KATALOG BUKU</p>
            <h1>Tambah buku</h1>
            <p class="muted">Lengkapi informasi koleksi baru.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('books.index') }}">Kembali ke katalog</a>
    </div>

    <form class="card form-card" action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('books::form')
        <div class="form-actions">
            <a class="btn btn-outline-secondary" href="{{ route('books.index') }}">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan buku</button>
        </div>
    </form>
@endsection
