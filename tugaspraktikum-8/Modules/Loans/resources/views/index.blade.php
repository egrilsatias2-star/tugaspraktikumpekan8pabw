@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">TRANSAKSI PERPUSTAKAAN</p>
            <h1>Peminjaman</h1>
            <p class="muted">Kelola peminjaman tanpa memuat ulang halaman.</p>
        </div>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#loanModal" data-new-loan>
            <i class="bi bi-plus-lg"></i> Pinjam buku
        </button>
    </div>

    <div id="loan-alert" class="alert d-none" role="status"></div>
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        @if (auth()->user()->isAdmin()) <th>Peminjam</th> @endif
                        <th>Buku</th><th>Tanggal pinjam</th><th>Batas kembali</th><th>Status</th><th>Bukti</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="loans-table-body">
                    @forelse ($loans as $loan)
                        @include('loans::partials.row', ['loan' => $loan])
                    @empty
                        <tr class="empty-row"><td class="empty-state" colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}">Belum ada peminjaman. Mulai dengan menambahkan peminjaman baru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="loanModal" tabindex="-1" aria-labelledby="loanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="loan-form" class="modal-content" action="{{ route('loans.store') }}" data-store-url="{{ route('loans.store') }}" method="POST" enctype="multipart/form-data" data-ajax-form>
                @csrf
                <input type="hidden" name="_method" id="loan-method" value="POST">
                <div class="modal-header">
                    <div>
                        <p class="eyebrow mb-1">FORM PEMINJAMAN</p>
                        <h2 class="modal-title fs-5" id="loanModalLabel">Pinjam buku</h2>
                    </div>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body form-stack">
                    @if (auth()->user()->isAdmin())
                        <div class="field">
                            <label for="loan-user">Peminjam</label>
                            <select id="loan-user" name="user_id" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                                @endforeach
                            </select>
                            <small class="field-error" data-error="user_id"></small>
                        </div>
                    @endif
                    <div class="field">
                        <label for="loan-book">Buku</label>
                        <select id="loan-book" name="book_id" required>
                            <option value="">Pilih buku</option>
                            @foreach ($books as $book)
                                <option value="{{ $book->id }}" data-title="{{ $book->title }}">
                                    {{ $book->title }} — {{ $book->stock }} tersedia
                                </option>
                            @endforeach
                        </select>
                        <small class="field-error" data-error="book_id"></small>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="loan-date">Tanggal pinjam</label>
                            <input id="loan-date" type="date" name="loan_date" value="{{ now()->toDateString() }}" required>
                            <small class="field-error" data-error="loan_date"></small>
                        </div>
                        <div class="field">
                            <label for="due-date">Batas pengembalian</label>
                            <input id="due-date" type="date" name="due_date" value="{{ now()->addDays(7)->toDateString() }}" required>
                            <small class="field-error" data-error="due_date"></small>
                        </div>
                    </div>
                    <div class="field d-none" id="loan-status-field">
                        <label for="loan-status">Status</label>
                        <select id="loan-status" name="status">
                            <option value="borrowed">Dipinjam</option>
                            <option value="returned">Dikembalikan</option>
                        </select>
                        <small class="field-error" data-error="status"></small>
                    </div>
                    <div class="field">
                        <label for="loan-proof">Bukti peminjaman <span class="muted">(JPG, PNG, PDF; maks. 2 MB)</span></label>
                        <input id="loan-proof" type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="field-error" data-error="proof"></small>
                    </div>
                    <p class="muted small mb-0">Stok buku akan diperbarui otomatis saat peminjaman ditambah, diubah, atau dihapus.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit" id="loan-submit">Simpan peminjaman</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.loanConfig = { admin: @json(auth()->user()->isAdmin()) };
    </script>
@endpush
