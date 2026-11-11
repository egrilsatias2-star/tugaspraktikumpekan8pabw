<?php

namespace Modules\Books\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Loans\Models\Loan;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'publisher',
        'published_year',
        'isbn',
        'stock',
        'cover_path',
    ];

    protected function casts(): array
    {
        return [
            'published_year' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
