<?php

namespace Modules\Loans\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Books\Models\Book;
use Modules\Loans\Http\Requests\LoanRequest;
use Modules\Loans\Models\Loan;
use RuntimeException;
use Throwable;

class LoansController extends Controller
{
    public function index(): View
    {
        $loans = Loan::query()
            ->with(['book', 'user'])
            ->when(! auth()->user()->isAdmin(), fn ($query) => $query->where('user_id', auth()->id()))
            ->latest()
            ->get();
        $books = Book::query()->orderBy('title')->get();
        $users = auth()->user()->isAdmin()
            ? \App\Models\User::query()->orderBy('name')->get()
            : collect();

        return view('loans::index', compact('loans', 'books', 'users'));
    }

    public function create(): JsonResponse
    {
        abort(404);
    }

    public function store(LoanRequest $request): JsonResponse
    {
        $data = $request->validated();
        $proofPath = $request->file('proof')?->store('loan-proofs', 'public');
        if ($request->hasFile('proof') && $proofPath === false) {
            throw new RuntimeException('Bukti peminjaman gagal disimpan. Silakan coba lagi.');
        }

        try {
            $loan = DB::transaction(function () use ($data, $proofPath) {
                $book = Book::query()->lockForUpdate()->findOrFail($data['book_id']);
                if ($book->stock < 1) {
                    throw ValidationException::withMessages([
                        'book_id' => 'Stok buku sudah habis.',
                    ]);
                }

                $book->decrement('stock');

                return Loan::create([
                    'user_id' => auth()->user()->isAdmin() ? $data['user_id'] : auth()->id(),
                    'book_id' => $book->id,
                    'loan_date' => $data['loan_date'],
                    'due_date' => $data['due_date'],
                    'status' => 'borrowed',
                    'proof_path' => $proofPath,
                ])->load(['book', 'user']);
            });
        } catch (Throwable $exception) {
            if ($proofPath) {
                Storage::disk('public')->delete($proofPath);
            }

            throw $exception;
        }

        return $this->successResponse($loan, 'Peminjaman berhasil ditambahkan.');
    }

    public function edit(Loan $loan): JsonResponse
    {
        abort(404);
    }

    public function update(LoanRequest $request, Loan $loan): JsonResponse
    {
        $this->authorizeLoan($loan);
        $data = $request->validated();
        $newProofPath = $request->file('proof')?->store('loan-proofs', 'public');
        if ($request->hasFile('proof') && $newProofPath === false) {
            throw new RuntimeException('Bukti peminjaman gagal disimpan. Silakan coba lagi.');
        }
        $oldProofPath = $loan->proof_path;

        try {
            $loan = DB::transaction(function () use ($data, $loan, $newProofPath) {
                $lockedLoan = Loan::query()->lockForUpdate()->findOrFail($loan->id);
                $oldBookId = $lockedLoan->book_id;
                $newBookId = (int) $data['book_id'];
                $wasBorrowed = $lockedLoan->status === 'borrowed';
                $willBeBorrowed = $data['status'] === 'borrowed';
                $bookIds = array_values(array_unique([$oldBookId, $newBookId]));
                $books = Book::query()
                    ->whereIn('id', $bookIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                $oldBook = $books->get($oldBookId);
                $newBook = $books->get($newBookId);

                if (! $newBook) {
                    throw ValidationException::withMessages([
                        'book_id' => 'Buku yang dipilih tidak ditemukan.',
                    ]);
                }

                if ($wasBorrowed && ($oldBookId !== $newBookId || ! $willBeBorrowed)) {
                    $oldBook->increment('stock');
                }

                if ($willBeBorrowed && (! $wasBorrowed || $oldBookId !== $newBookId)) {
                    if ($newBook->stock < 1) {
                        throw ValidationException::withMessages([
                            'book_id' => 'Stok buku sudah habis.',
                        ]);
                    }
                    $newBook->decrement('stock');
                }

                $lockedLoan->fill([
                    'user_id' => auth()->user()->isAdmin() ? $data['user_id'] : $lockedLoan->user_id,
                    'book_id' => $newBookId,
                    'loan_date' => $data['loan_date'],
                    'due_date' => $data['due_date'],
                    'status' => $data['status'],
                    'returned_at' => $willBeBorrowed ? null : now()->toDateString(),
                    'proof_path' => $newProofPath ?? $lockedLoan->proof_path,
                ])->save();

                return $lockedLoan->load(['book', 'user']);
            });
        } catch (Throwable $exception) {
            if ($newProofPath) {
                Storage::disk('public')->delete($newProofPath);
            }

            throw $exception;
        }

        if ($newProofPath && $oldProofPath) {
            if (Storage::disk('public')->exists($oldProofPath)
                && ! Storage::disk('public')->delete($oldProofPath)) {
                throw new RuntimeException('Bukti lama gagal dihapus dari penyimpanan.');
            }
        }

        return $this->successResponse($loan, 'Peminjaman berhasil diperbarui.');
    }

    public function destroy(Loan $loan): JsonResponse
    {
        $this->authorizeLoan($loan);
        $proofPath = $loan->proof_path;

        DB::transaction(function () use ($loan) {
            $lockedLoan = Loan::query()->lockForUpdate()->findOrFail($loan->id);
            if ($lockedLoan->status === 'borrowed') {
                Book::query()->whereKey($lockedLoan->book_id)->lockForUpdate()->firstOrFail()->increment('stock');
            }
            $lockedLoan->delete();
        });

        if ($proofPath) {
            if (Storage::disk('public')->exists($proofPath)
                && ! Storage::disk('public')->delete($proofPath)) {
                throw new RuntimeException('Bukti peminjaman gagal dihapus dari penyimpanan.');
            }
        }

        return response()->json(['message' => 'Peminjaman berhasil dihapus.']);
    }

    private function authorizeLoan(Loan $loan): void
    {
        abort_unless(auth()->user()->isAdmin() || $loan->user_id === auth()->id(), 403);
    }

    private function successResponse(Loan $loan, string $message): JsonResponse
    {
        return response()->json([
            'id' => $loan->id,
            'message' => $message,
            'row_html' => view('loans::partials.row', compact('loan'))->render(),
            'stocks' => Book::query()->pluck('stock', 'id'),
        ]);
    }
}
