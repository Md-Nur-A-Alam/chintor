<x-layouts.app :title="$page->translate('title')" :description="$page->translate('seo_description')">
    <x-site.page-hero :title="$page->translate('title')" />

    <div class="container-page py-12">
        <article class="prose-chintor max-w-3xl">
            {!! $html !!}
        </article>

        @if ($page->slug === 'contact')
            <div class="mt-10 max-w-xl rounded-card bg-surface p-6 shadow-card ring-1 ring-line">
                <h2 class="text-xl font-bold text-ink">{{ __('site.lead.callback_title') }}</h2>
                <p class="mt-1 text-ink-muted">{{ __('site.lead.callback_body') }}</p>
                <x-site.callback-form form="contact" :compact="true" class="mt-4" />
            </div>
        @endif
    </div>
</x-layouts.app>
