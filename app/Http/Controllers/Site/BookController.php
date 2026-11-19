<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::query()
            ->published()
            ->with('authors')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return view('site.books.index', compact('books'));
    }

    public function show(Book $book): View
    {
        abort_unless($book->isPublished(), 404);

        $book->load(['authors', 'program']);

        return view('site.books.show', compact('book'));
    }
}
