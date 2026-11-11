@extends('layouts.app')

@section('title', 'Edit buku')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">KATALOG BUKU</p>
            <h1>Edit buku</h1>
            <p class="muted">Perbarui informasi {{ $book->title }}.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('books.index') }}">Kembali ke katalog</a>
    </div>

    <form class="card form-card" action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('books::form', ['book' => $book])
        <div class="form-actions">
            <a class="btn btn-outline-secondary" href="{{ route('books.index') }}">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan perubahan</button>
        </div>
    </form>
@endsection
