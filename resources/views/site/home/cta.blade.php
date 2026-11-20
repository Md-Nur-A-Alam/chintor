{{-- Call-back band: the lowest-effort next step for a hesitant parent. --}}
<section class="container-page pt-8 pb-4">
    <div data-reveal class="relative overflow-hidden rounded-[2rem] bg-teal-900 px-6 py-10 text-white shadow-card sm:px-10 lg:px-14 lg:py-14">
        <div aria-hidden="true" class="pointer-events-none absolute -top-20 -right-20 size-72 rounded-full bg-coral-500/25 blur-3xl"></div>
        <div class="relative grid gap-8 lg:grid-cols-[1fr_1.1fr] lg:items-center">
            <div class="flex items-start gap-4">
                <x-mascot mood="think" class="animate-float hidden h-24 w-auto text-black sm:block" />
                <div>
                    <h2 class="text-3xl font-bold">{{ $section['title'] }}</h2>
                    @if ($section['subtitle'])
                        <p class="mt-3 text-teal-100">{{ $section['subtitle'] }}</p>
                    @endif
                </div>
            </div>
            <div class="rounded-2xl bg-surface p-5 text-ink shadow-card">
                <x-site.callback-form />
            </div>
        </div>
    </div>
</section>
