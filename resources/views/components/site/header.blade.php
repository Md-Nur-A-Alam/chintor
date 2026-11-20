@use('App\Support\Localization')
@use('App\Support\PhoneNumber')

@php
    $hotline = $siteContact['hotline'];

    $navigation = [
        ['label' => __('site.nav.courses'), 'url' => Localization::route('courses.index'), 'active' => 'courses.*'],
        ['label' => __('site.nav.learn'), 'url' => Localization::route('learn.index'), 'active' => 'learn.*'],
        ['label' => __('site.nav.arena'), 'url' => Localization::route('arena.index'), 'active' => 'arena.*'],
        ['label' => __('site.nav.books'), 'url' => Localization::route('books.index'), 'active' => 'books.*'],
        ['label' => __('site.nav.teachers'), 'url' => Localization::route('teachers.index'), 'active' => 'teachers.*'],
        ['label' => __('site.nav.about'), 'url' => Localization::route('pages.show', ['page' => 'about']), 'active' => null],
    ];

    $isActive = fn (?string $pattern): bool => $pattern !== null
        && (request()->routeIs($pattern) || request()->routeIs("en.{$pattern}"));

    $startUrl = Localization::route('start');
@endphp

<header
    x-data="{ menuOpen: false }"
    x-on:keydown.escape.window="menuOpen = false"
    class="sticky top-0 z-40 border-b border-line/70 bg-canvas/90 backdrop-blur supports-[backdrop-filter]:bg-canvas/75"
>
    <div class="container-page flex h-16 items-center justify-between gap-4 lg:h-20">
        <a href="{{ Localization::route('home') }}" class="-m-1 rounded-lg p-1" aria-label="{{ __('site.brand') }}">
            <x-logo class="h-9 w-auto lg:h-11" />
        </a>

        {{-- Seven menu items need about 1280px; below that the menu button is used. --}}
        <nav aria-label="{{ __('site.nav.label') }}" class="hidden xl:block">
            <ul class="flex items-center gap-1">
                {{-- Programs dropdown, built from the database --}}
                <li x-data="{ open: false }" x-on:click.outside="open = false" class="relative">
                    <button
                        type="button"
                        x-on:click="open = ! open"
                        x-bind:aria-expanded="open"
                        @class([
                            'inline-flex cursor-pointer items-center gap-1 rounded-full whitespace-nowrap px-3 py-2 text-[0.95rem] font-medium transition-colors hover:bg-surface-muted hover:text-ink',
                            'text-ink' => $isActive('programs.*'),
                            'text-ink-muted' => ! $isActive('programs.*'),
                        ])
                    >
                        {{ __('site.nav.programs') }}
                        <x-icon name="chevron-down" class="size-4" />
                    </button>
                    <div
                        x-show="open"
                        x-cloak
                        x-transition.opacity.duration.150ms
                        class="absolute top-full left-0 mt-2 w-72 rounded-2xl bg-surface-raised p-2 shadow-card ring-1 ring-line"
                    >
                        @foreach ($navPrograms as $program)
                            <a href="{{ Localization::route('programs.show', ['program' => $program]) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-ink hover:bg-surface-muted">
                                <span class="grid size-8 place-items-center rounded-lg bg-brand-soft text-brand"><x-icon :name="$program->icon ?: 'layers'" class="size-4" /></span>
                                {{ $program->translate('name') }}
                            </a>
                        @endforeach
                        <a href="{{ Localization::route('programs.index') }}" class="mt-1 flex items-center justify-between rounded-xl px-3 py-2 text-sm font-semibold text-accent-ink hover:bg-surface-muted">
                            {{ __('site.nav.all_programs') }} <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </div>
                </li>

                @foreach ($navigation as $item)
                    <li>
                        <a
                            href="{{ $item['url'] }}"
                            @if ($isActive($item['active'])) aria-current="page" @endif
                            @class([
                                'rounded-full whitespace-nowrap px-3 py-2 text-[0.95rem] font-medium transition-colors hover:bg-surface-muted hover:text-ink',
                                'text-ink' => $isActive($item['active']),
                                'text-ink-muted' => ! $isActive($item['active']),
                            ])
                        >{{ $item['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex items-center gap-2">
            @if ($hotline)
                <a href="tel:{{ PhoneNumber::forTelLink($hotline) }}" class="hidden items-center gap-2 text-sm 2xl:flex">
                    <span class="grid size-9 place-items-center rounded-full bg-brand-soft text-brand">
                        <x-icon name="phone" class="size-4" />
                    </span>
                    <span class="leading-tight">
                        <span class="block text-xs text-ink-subtle">{{ __('site.hotline') }}</span>
                        <span class="block font-semibold text-ink">{{ Localization::digits($hotline) }}</span>
                    </span>
                </a>
            @endif

            {{-- Hidden by a wrapper: the components' own display class would override "hidden". --}}
            <div class="hidden md:block"><x-theme-switcher /></div>
            <div class="hidden sm:block"><x-locale-switcher /></div>

            <div class="hidden md:block">
                <x-button :href="$startUrl" size="sm">
                    {{ __('site.cta.start_learning') }}
                    <x-icon name="arrow-right" class="size-4" />
                </x-button>
            </div>

            <button
                type="button"
                class="grid size-10 cursor-pointer place-items-center rounded-full text-ink ring-1 ring-line ring-inset xl:hidden"
                x-on:click="menuOpen = ! menuOpen"
                x-bind:aria-expanded="menuOpen"
                aria-controls="mobile-menu"
            >
                <span class="sr-only" x-text="menuOpen ? @js(__('site.nav.close_menu')) : @js(__('site.nav.open_menu'))">{{ __('site.nav.open_menu') }}</span>
                <x-icon name="menu" class="size-5" x-show="! menuOpen" />
                <x-icon name="close" class="size-5" x-show="menuOpen" x-cloak />
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" x-show="menuOpen" x-cloak x-transition.opacity.duration.150ms class="max-h-[calc(100dvh-4rem)] overflow-y-auto border-t border-line bg-canvas xl:hidden">
        <nav aria-label="{{ __('site.nav.label') }}" class="container-page py-4">
            <p class="px-3 pb-1 text-xs font-semibold tracking-wide text-ink-subtle">{{ __('site.nav.programs') }}</p>
            <ul class="grid gap-1">
                @foreach ($navPrograms as $program)
                    <li>
                        <a href="{{ Localization::route('programs.show', ['program' => $program]) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium text-ink hover:bg-surface-muted">
                            <x-icon :name="$program->icon ?: 'layers'" class="size-4 text-brand" />
                            {{ $program->translate('name') }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <ul class="mt-3 grid gap-1 border-t border-line pt-3">
                @foreach ($navigation as $item)
                    <li><a href="{{ $item['url'] }}" class="block rounded-xl px-3 py-3 text-lg font-medium text-ink hover:bg-surface-muted">{{ $item['label'] }}</a></li>
                @endforeach
                <li><a href="{{ Localization::route('orders.track') }}" class="block rounded-xl px-3 py-3 text-lg font-medium text-ink hover:bg-surface-muted">{{ __('site.nav.track_order') }}</a></li>
            </ul>

            <div class="mt-4 grid gap-4 border-t border-line pt-4">
                <x-button :href="$startUrl" size="lg" class="w-full">
                    {{ __('site.cta.start_learning') }}
                    <x-icon name="arrow-right" class="size-5" />
                </x-button>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <x-theme-switcher :with-labels="true" />
                    <x-locale-switcher />
                </div>
            </div>
        </nav>
    </div>
</header>
