@use('App\Support\Localization')

@if ($books->isNotEmpty())
    <x-site.section :eyebrow="$section['eyebrow']" :title="$section['title']" :subtitle="$section['subtitle']" :band="$section['band']" :action-url="Localization::route('books.index')">
        {{-- Centred rows of up to four, so three books never leave an empty column. --}}
        <div class="flex flex-wrap justify-center gap-6">
            @foreach ($books as $book)
                <x-reveal :index="$loop->index" class="w-[calc(50%-0.75rem)] lg:w-[calc(25%-1.125rem)]"><x-site.book-card :book="$book" /></x-reveal>
            @endforeach
        </div>
    </x-site.section>
@endif
