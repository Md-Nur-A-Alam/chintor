<x-layouts.app :title="__('site.book.index_title')" :description="__('site.book.index_subtitle')">
    <x-site.page-hero :title="__('site.book.index_title')" :subtitle="__('site.book.index_subtitle')" />

    <div class="container-page py-12">
        @if ($books->isEmpty())
            <p class="py-16 text-center text-ink-subtle">{{ __('site.book.none') }}</p>
        @else
            <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($books as $book)
                    <x-site.book-card :book="$book" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
