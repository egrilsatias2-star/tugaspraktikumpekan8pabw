<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Books\Models\Book;
use Modules\Loans\Models\Loan;
use Tests\TestCase;

class SipustakaTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_authentication_forms_render(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk ke akun');
        $this->get('/register')->assertOk()->assertSee('Buat akun anggota');
    }

    public function test_registration_always_creates_a_regular_member(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Member',
            'email' => 'member@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'member@example.test',
            'role' => 'user',
        ]);
        $this->assertAuthenticated();
    }

    public function test_members_cannot_access_admin_user_or_book_actions(): void
    {
        $member = User::factory()->create(['role' => 'user']);
        $this->actingAs($member);

        $this->get('/users')->assertForbidden();
        $this->post('/books', [])->assertForbidden();
    }

    public function test_admin_can_create_a_book_with_validated_catalog_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->post('/books', [
                'title' => 'Belajar Laravel',
                'author' => 'A. Penulis',
                'publisher' => 'Penerbit',
                'published_year' => now()->year,
                'isbn' => '9786021234567',
                'stock' => 3,
            ])
            ->assertRedirect(route('books.index'));

        $this->assertDatabaseHas('books', [
            'title' => 'Belajar Laravel',
            'isbn' => '9786021234567',
            'stock' => 3,
        ]);
    }

    public function test_admin_catalog_and_user_management_pages_render(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)->get('/books')->assertOk()->assertSee('Katalog buku');
        $this->get('/books/create')->assertOk()->assertSee('Tambah buku');
        $this->get('/users')->assertOk()->assertSee('Manajemen pengguna');
        $this->get('/users/create')->assertOk()->assertSee('Tambah akun pengguna');
        $this->get("/users/{$member->id}/edit")->assertOk()->assertSee('Ubah akun pengguna');
        $this->get('/loans')->assertOk()->assertSee('Peminjaman');
    }

    public function test_admin_can_update_an_existing_book(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $book = Book::create([
            'title' => 'Judul Lama',
            'author' => 'A. Penulis',
            'publisher' => 'Penerbit',
            'published_year' => 2020,
            'isbn' => '9786021234569',
            'stock' => 2,
        ]);

        $this->actingAs($admin)->get("/books/{$book->id}/edit")
            ->assertOk()
            ->assertSee('Judul Lama');

        $this->put("/books/{$book->id}", [
            'title' => 'Judul Baru',
            'author' => 'A. Penulis',
            'publisher' => 'Penerbit',
            'published_year' => 2020,
            'isbn' => '9786021234569',
            'stock' => 4,
        ])->assertRedirect(route('books.index'));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Judul Baru',
            'stock' => 4,
        ]);
    }

    public function test_ajax_loan_crud_tracks_stock_and_cleans_up_uploaded_proof(): void
    {
        Storage::fake('public');
        $member = User::factory()->create(['role' => 'user']);
        $book = Book::create([
            'title' => 'Atomic Habits',
            'author' => 'James Clear',
            'publisher' => 'Gramedia',
            'published_year' => 2019,
            'isbn' => '9786020633176',
            'stock' => 1,
        ]);

        $create = $this->actingAs($member)->post('/loans', [
            'book_id' => $book->id,
            'loan_date' => '2026-11-06',
            'due_date' => '2026-11-13',
            'proof' => UploadedFile::fake()->createWithContent('proof.pdf', "%PDF-1.4\nsample proof\n%%EOF"),
        ], ['Accept' => 'application/json']);
        $create->assertOk()->assertJsonPath('message', 'Peminjaman berhasil ditambahkan.');
        $loanId = $create->json('id');
        $proofPath = Loan::findOrFail($loanId)->proof_path;

        $this->assertSame(0, $book->fresh()->stock);
        Storage::disk('public')->assertExists($proofPath);
        $this->get('/loans')->assertSee('Atomic Habits');

        $this->putJson("/loans/{$loanId}", [
            'book_id' => $book->id,
            'loan_date' => '2026-11-06',
            'due_date' => '2026-11-13',
            'status' => 'returned',
        ])->assertOk()->assertJsonPath('message', 'Peminjaman berhasil diperbarui.');

        $this->assertSame(1, $book->fresh()->stock);
        $this->assertNotNull(Loan::findOrFail($loanId)->returned_at);
        Storage::disk('public')->assertExists($proofPath);

        $this->deleteJson("/loans/{$loanId}")
            ->assertOk()
            ->assertJsonPath('message', 'Peminjaman berhasil dihapus.');
        Storage::disk('public')->assertMissing($proofPath);
        $this->assertDatabaseMissing('loans', ['id' => $loanId]);
        $this->assertSame(1, $book->fresh()->stock);
    }

    public function test_members_cannot_update_another_members_loan(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $otherMember = User::factory()->create(['role' => 'user']);
        $book = Book::create([
            'title' => 'Bumi Manusia',
            'author' => 'Pramoedya Ananta Toer',
            'publisher' => 'Lentera Dipantara',
            'published_year' => 1980,
            'isbn' => '9789799731234',
            'stock' => 1,
        ]);
        $loan = Loan::create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
            'loan_date' => '2026-11-06',
            'due_date' => '2026-11-13',
            'status' => 'borrowed',
        ]);

        $this->actingAs($otherMember)->putJson("/loans/{$loan->id}", [
            'book_id' => $book->id,
            'loan_date' => '2026-11-06',
            'due_date' => '2026-11-13',
            'status' => 'returned',
        ])->assertForbidden();

        $this->assertSame('borrowed', $loan->fresh()->status);
        $this->assertSame(1, $book->fresh()->stock);
    }

    public function test_loan_validation_returns_field_level_errors_and_prevents_empty_stock_loans(): void
    {
        $member = User::factory()->create(['role' => 'user']);
        $book = Book::create([
            'title' => 'Stok Habis',
            'author' => 'A. Penulis',
            'publisher' => 'Penerbit',
            'published_year' => 2020,
            'isbn' => '9786021234568',
            'stock' => 0,
        ]);
        $this->actingAs($member);

        $this->postJson('/loans', [
            'book_id' => $book->id,
            'loan_date' => '2026-11-06',
            'due_date' => '2026-11-05',
        ])->assertUnprocessable()->assertJsonValidationErrors('due_date');

        $this->postJson('/loans', [
            'book_id' => $book->id,
            'loan_date' => '2026-11-06',
            'due_date' => '2026-11-13',
        ])->assertUnprocessable()->assertJsonValidationErrors('book_id');

        $this->assertDatabaseCount('loans', 0);
    }

    public function test_admin_cannot_delete_or_demote_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user',
        ])->assertSessionHasErrors('role');

        $this->delete("/users/{$admin->id}")->assertForbidden();
        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_database_seeder_creates_demo_catalog_and_loans(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('books', 5);
        $this->assertDatabaseCount('loans', 5);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@sipustaka.test',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'anggota@sipustaka.test',
            'role' => 'user',
        ]);
    }
}
