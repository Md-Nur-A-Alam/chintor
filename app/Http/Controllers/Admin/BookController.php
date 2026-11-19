<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookController extends Controller
{
    use HandlesImageInput;

    public function index(): View
    {
        return view('admin.books.index', [
            'books' => Book::query()->with('authors')->orderBy('sort_order')->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('admin.books.form', [
            'book' => new Book(['status' => PublishStatus::Draft, 'stock_quantity' => 0, 'language' => 'bn']),
            'programs' => Program::orderBy('sort_order')->get(),
        ]);
    }

    public function store(SaveBookRequest $request): RedirectResponse
    {
        $book = Book::create([
            ...$request->attributesForModel(),
            'cover_url' => $this->resolveImageUrl($request, 'cover'),
            'published_at' => $request->validated('status') === PublishStatus::Published->value ? now() : null,
        ]);

        $this->syncAuthors($book, $request->authorNames());

        return to_route('admin.books.index')->with('status', 'বই যোগ হয়েছে।');
    }

    public function edit(Book $book): View
    {
        return view('admin.books.form', [
            'book' => $book->load('authors'),
            'programs' => Program::orderBy('sort_order')->get(),
        ]);
    }

    public function update(SaveBookRequest $request, Book $book): RedirectResponse
    {
        $book->update([
            ...$request->attributesForModel(),
            'cover_url' => $this->resolveImageUrl($request, 'cover', $book->cover_url),
            'published_at' => $book->published_at ?? ($request->validated('status') === PublishStatus::Published->value ? now() : null),
        ]);

        $this->syncAuthors($book, $request->authorNames());

        return back()->with('status', 'বইয়ের তথ্য আপডেট হয়েছে।');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return to_route('admin.books.index')->with('status', 'বইটি মুছে ফেলা হয়েছে।');
    }

    /**
     * Authors are typed as names; unknown names become new authors.
     *
     * @param  list<string>  $names
     */
    private function syncAuthors(Book $book, array $names): void
    {
        $authorIds = collect($names)->map(function (string $name): int {
            $author = Author::query()->where('name->bn', $name)->first()
                ?? Author::create([
                    'name' => ['bn' => $name, 'en' => null],
                    'slug' => Str::slug($name) ?: 'author-'.Str::lower(Str::random(8)),
                ]);

            return $author->id;
        });

        $book->authors()->sync($authorIds);
    }
}
