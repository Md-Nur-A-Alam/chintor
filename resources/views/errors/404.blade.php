<x-layouts.app :title="__('site.errors.not_found_title')" :noindex="true">
    <div class="container-page grid place-items-center py-20 text-center">
        <x-mascot mood="think" class="h-40 w-auto text-ink" />
        <p class="mt-6 font-mono text-sm font-semibold text-accent-ink">404</p>
        <h1 class="mt-2 text-3xl font-bold text-ink sm:text-4xl">{{ __('site.errors.not_found_title') }}</h1>
        <p class="mt-3 max-w-md text-ink-muted">{{ __('site.errors.not_found_body') }}</p>
        <x-button :href="App\Support\Localization::route('home')" class="mt-8">{{ __('site.errors.go_home') }}</x-button>
    </div>
</x-layouts.app>
