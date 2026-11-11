<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Books\Models\Book;
use Modules\Loans\Models\Loan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@sipustaka.test'],
            ['name' => 'Admin SiPustaka', 'password' => 'password', 'role' => 'admin'],
        );
        $member = User::firstOrCreate(
            ['email' => 'anggota@sipustaka.test'],
            ['name' => 'Anggota SiPustaka', 'password' => 'password', 'role' => 'user'],
        );

        $books = [
            ['title' => 'Laut Bercerita', 'author' => 'Leila S. Chudori', 'publisher' => 'Kepustakaan Populer Gramedia', 'published_year' => 2017, 'isbn' => '9786024246945', 'stock' => 4],
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'publisher' => 'Lentera Dipantara', 'published_year' => 1980, 'isbn' => '9789799731234', 'stock' => 4],
            ['title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'publisher' => 'Penerbit Buku Kompas', 'published_year' => 2018, 'isbn' => '9786024125189', 'stock' => 4],
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'publisher' => 'Gramedia Pustaka Utama', 'published_year' => 2019, 'isbn' => '9786020633176', 'stock' => 4],
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'published_year' => 2005, 'isbn' => '9789793062792', 'stock' => 4],
        ];

        foreach ($books as $index => $attributes) {
            $book = Book::firstOrCreate(['isbn' => $attributes['isbn']], $attributes);
            $loan = Loan::firstOrCreate(
                ['user_id' => $member->id, 'book_id' => $book->id, 'status' => 'borrowed'],
                ['loan_date' => now()->subDays($index)->toDateString(), 'due_date' => now()->addDays(7)->toDateString()],
            );

            if ($loan->wasRecentlyCreated) {
                $book->decrement('stock');
            }
        }
    }
}
