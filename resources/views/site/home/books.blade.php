@use('App\Support\Localization')

@if ($books->isNotEmpty())
    <x-site.section :title="$section['title']" :subtitle="$section['subtitle']" :action-url="Localization::route('books.index')">
        <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
            @foreach ($books as $book)
                <x-site.book-card :book="$book" />
            @endforeach
        </div>
    </x-site.section>
@endif
