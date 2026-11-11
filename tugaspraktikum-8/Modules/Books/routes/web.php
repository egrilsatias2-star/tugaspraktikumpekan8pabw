<?php

use Illuminate\Support\Facades\Route;
use Modules\Books\Http\Controllers\BooksController;

Route::middleware('auth')->group(function () {
    Route::get('/books', [BooksController::class, 'index'])->name('books.index');
    Route::middleware('role:admin')->resource('books', BooksController::class)->except(['index', 'show']);
});
