<?php

namespace Modules\Books\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Books\Http\Requests\BookRequest;
use Modules\Books\Models\Book;
use RuntimeException;
use Throwable;

class BooksController extends Controller
{
    public function index(): View
    {
        $books = Book::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('books::index', compact('books'));
    }

    public function create(): View
    {
        return view('books::create');
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('books', 'public');
            if ($coverPath === false) {
                throw new RuntimeException('Sampul gagal disimpan. Silakan coba lagi.');
            }
            $data['cover_path'] = $coverPath;
        }
        unset($data['cover']);

        try {
            Book::create($data);
        } catch (Throwable $exception) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            throw $exception;
        }

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        return view('books::edit', compact('book'));
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();
        $oldCover = $book->cover_path;
        $coverPath = null;

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('books', 'public');
            if ($coverPath === false) {
                throw new RuntimeException('Sampul gagal disimpan. Silakan coba lagi.');
            }
            $data['cover_path'] = $coverPath;
        }
        unset($data['cover']);

        try {
            $book->update($data);
        } catch (Throwable $exception) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            throw $exception;
        }

        if ($coverPath && $oldCover && Storage::disk('public')->exists($oldCover)
            && ! Storage::disk('public')->delete($oldCover)) {
            throw new RuntimeException('Sampul lama gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->loans()->exists()) {
            throw ValidationException::withMessages([
                'book' => 'Buku tidak dapat dihapus karena sudah memiliki riwayat peminjaman.',
            ]);
        }

        $coverPath = $book->cover_path;
        $book->delete();

        if ($coverPath && Storage::disk('public')->exists($coverPath)
            && ! Storage::disk('public')->delete($coverPath)) {
            throw new RuntimeException('Sampul buku gagal dihapus dari penyimpanan.');
        }

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
