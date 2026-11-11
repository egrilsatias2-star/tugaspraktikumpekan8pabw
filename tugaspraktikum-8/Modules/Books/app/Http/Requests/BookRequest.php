<?php

namespace Modules\Books\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $book = $this->route('book');
        $bookId = is_object($book) ? $book->id : $book;

        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'published_year' => ['required', 'integer', 'between:1000,'.now()->year],
            'isbn' => ['required', 'string', 'max:32', Rule::unique('books', 'isbn')->ignore($bookId)],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi.',
            'title.string' => 'Judul buku harus berupa teks.',
            'title.max' => 'Judul buku maksimal 255 karakter.',
            'author.required' => 'Nama penulis wajib diisi.',
            'author.string' => 'Nama penulis harus berupa teks.',
            'author.max' => 'Nama penulis maksimal 255 karakter.',
            'publisher.required' => 'Nama penerbit wajib diisi.',
            'publisher.string' => 'Nama penerbit harus berupa teks.',
            'publisher.max' => 'Nama penerbit maksimal 255 karakter.',
            'published_year.required' => 'Tahun terbit wajib diisi.',
            'published_year.integer' => 'Tahun terbit harus berupa angka.',
            'published_year.between' => 'Tahun terbit harus antara 1000 dan tahun ini.',
            'isbn.required' => 'ISBN wajib diisi.',
            'isbn.string' => 'ISBN harus berupa teks.',
            'isbn.max' => 'ISBN maksimal 32 karakter.',
            'isbn.unique' => 'ISBN sudah digunakan oleh buku lain.',
            'stock.required' => 'Stok buku wajib diisi.',
            'stock.integer' => 'Stok buku harus berupa angka.',
            'stock.min' => 'Stok tidak boleh kurang dari nol.',
            'stock.max' => 'Stok buku maksimal 100.000.',
            'cover.file' => 'Sampul gagal dibaca sebagai berkas.',
            'cover.image' => 'Sampul harus berupa gambar yang valid.',
            'cover.mimes' => 'Sampul harus berformat JPG, PNG, atau WebP.',
            'cover.max' => 'Ukuran sampul maksimal 2 MB.',
        ];
    }
}
