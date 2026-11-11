<?php

namespace Modules\Loans\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $rules = [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'loan_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = ['required', Rule::in(['borrowed', 'returned'])];
        }

        if ($this->user()?->isAdmin()) {
            $rules['user_id'] = ['required', 'integer', 'exists:users,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'book_id.integer' => 'Buku yang dipilih tidak valid.',
            'book_id.required' => 'Pilih buku yang akan dipinjam.',
            'book_id.exists' => 'Buku yang dipilih tidak ditemukan.',
            'loan_date.required' => 'Tanggal pinjam wajib diisi.',
            'loan_date.date' => 'Tanggal pinjam tidak valid.',
            'due_date.required' => 'Tanggal pengembalian wajib diisi.',
            'due_date.date' => 'Tanggal pengembalian tidak valid.',
            'due_date.after_or_equal' => 'Tanggal pengembalian tidak boleh sebelum tanggal pinjam.',
            'user_id.required' => 'Pilih pengguna yang meminjam.',
            'user_id.exists' => 'Pengguna yang dipilih tidak ditemukan.',
            'status.required' => 'Pilih status peminjaman.',
            'status.in' => 'Status peminjaman tidak valid.',
            'proof.file' => 'Bukti peminjaman gagal dibaca sebagai berkas.',
            'proof.mimes' => 'Bukti harus berformat JPG, PNG, atau PDF.',
            'proof.max' => 'Ukuran bukti peminjaman maksimal 2 MB.',
        ];
    }
}
