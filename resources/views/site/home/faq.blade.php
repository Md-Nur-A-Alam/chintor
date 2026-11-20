@if ($faqs->isNotEmpty())
    <x-site.section :title="$section['title']" :subtitle="$section['subtitle']">
        <div data-reveal>
            <x-site.faq-list :faqs="$faqs" class="max-w-3xl" />
        </div>
    </x-site.section>
@endif
