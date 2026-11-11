<div class="form-grid">
    <div class="field">
        <label for="title">Judul buku</label>
        <input id="title" name="title" value="{{ old('title', $book->title ?? '') }}" required>
        @error('title') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="author">Penulis</label>
        <input id="author" name="author" value="{{ old('author', $book->author ?? '') }}" required>
        @error('author') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="publisher">Penerbit</label>
        <input id="publisher" name="publisher" value="{{ old('publisher', $book->publisher ?? '') }}" required>
        @error('publisher') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="published_year">Tahun terbit</label>
        <input id="published_year" name="published_year" type="number" min="1000" max="{{ now()->year }}" value="{{ old('published_year', $book->published_year ?? now()->year) }}" required>
        @error('published_year') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="isbn">ISBN</label>
        <input id="isbn" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}" required>
        @error('isbn') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="stock">Stok tersedia</label>
        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $book->stock ?? 0) }}" required>
        @error('stock') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field field-wide">
        <label for="cover">Sampul buku <span class="muted">(JPG, PNG, WebP; maks. 2 MB)</span></label>
        <input id="cover" name="cover" type="file" accept=".jpg,.jpeg,.png,.webp">
        @if (!empty($book?->cover_path))
            <img class="book-cover-preview" src="{{ asset('storage/'.$book->cover_path) }}" alt="Sampul {{ $book->title }}">
        @endif
        @error('cover') <small class="field-error">{{ $message }}</small> @enderror
    </div>
</div>
