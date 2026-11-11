@extends('layouts.app')

@section('title', 'Katalog buku')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">KOLEKSI SIPUSTAKA</p>
            <h1>Katalog buku</h1>
            <p class="muted">Temukan koleksi dan cek ketersediaan sebelum meminjam.</p>
        </div>
        @if (auth()->user()->isAdmin())
            <a class="btn btn-primary" href="{{ route('books.create') }}"><i class="bi bi-plus-lg"></i> Tambah buku</a>
        @endif
    </div>

    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <div class="card table-card">
        <form class="toolbar" method="GET" action="{{ route('books.index') }}">
            <div class="search-input">
                <i class="bi bi-search"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, atau ISBN">
            </div>
            <button class="btn btn-outline-secondary" type="submit">Cari buku</button>
        </form>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Buku</th><th>Penulis</th><th>Penerbit / tahun</th><th>ISBN</th><th>Stok</th>@if (auth()->user()->isAdmin())<th>Aksi</th>@endif</tr></thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td>
                                <div class="book-cell">
                                    @if ($book->cover_path)
                                        <img class="book-cover" src="{{ asset('storage/'.$book->cover_path) }}" alt="">
                                    @else
                                        <div class="book-cover book-cover-placeholder"><i class="bi bi-book"></i></div>
                                    @endif
                                    <strong>{{ $book->title }}</strong>
                                </div>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->publisher }}<span class="d-block muted small">{{ $book->published_year }}</span></td>
                            <td>{{ $book->isbn }}</td>
                            <td><span class="stock-badge {{ $book->stock ? '' : 'stock-empty' }}">{{ $book->stock }} tersedia</span></td>
                            @if (auth()->user()->isAdmin())
                                <td>
                                    <div class="action-group">
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('books.edit', $book) }}">Edit</a>
                                        <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Hapus buku ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}">Belum ada buku yang cocok dengan pencarian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $books->links() }}</div>
    </div>
@endsection
