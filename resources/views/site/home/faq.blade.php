@if ($faqs->isNotEmpty())
    <x-site.section :title="$section['title']" :subtitle="$section['subtitle']">
        <x-site.faq-list :faqs="$faqs" class="max-w-3xl" />
    </x-site.section>
@endif
