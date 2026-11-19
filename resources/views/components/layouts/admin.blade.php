@use('App\Support\AdminNavigation')

@props([
    'title' => 'অ্যাডমিন',
])

<!DOCTYPE html>
<html lang="bn" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <x-theme-script />

        <title>{{ $title }} | চিন্তর অ্যাডমিন</title>
        <meta name="theme-color" content="#fffbf6">

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="preload" href="{{ asset('fonts/anek-bangla-bengali.woff2') }}" as="font" type="font/woff2" crossorigin>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-dvh bg-canvas">
        @auth
            @php($navigation = AdminNavigation::for(auth()->user()))

            <div x-data="{ sidebarOpen: false }" class="lg:grid lg:grid-cols-[16rem_1fr]">
                {{-- Sidebar --}}
                <aside
                    x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                    class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-line bg-surface transition-transform duration-200 lg:sticky lg:top-0 lg:h-dvh lg:translate-x-0"
                >
                    <a href="{{ route('admin.dashboard') }}" class="flex h-16 shrink-0 items-center gap-3 border-b border-line px-5">
                        <x-logo variant="icon" class="h-8 w-auto" />
                        <span class="font-semibold text-ink">চিন্তর অ্যাডমিন</span>
                    </a>

                    <nav aria-label="অ্যাডমিন মেনু" class="flex-1 overflow-y-auto px-3 py-4">
                        @foreach ($navigation as $section)
                            <p class="mt-4 mb-1 px-3 text-xs font-semibold tracking-wide text-ink-subtle first:mt-0">{{ $section['group'] }}</p>
                            <ul class="grid gap-0.5">
                                @foreach ($section['items'] as $item)
                                    <li>
                                        <a
                                            href="{{ $item['url'] }}"
                                            @if ($item['active']) aria-current="page" @endif
                                            @class([
                                                'flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition-colors',
                                                'bg-accent-soft text-accent-ink' => $item['active'],
                                                'text-ink-muted hover:bg-surface-muted hover:text-ink' => ! $item['active'],
                                            ])
                                        >
                                            <x-icon :name="$item['icon']" class="size-4" />
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </nav>

                    <div class="border-t border-line p-4">
                        <p class="truncate text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-ink-subtle">{{ auth()->user()->roles->pluck('label')->join(', ') }}</p>
                    </div>
                </aside>

                <div x-show="sidebarOpen" x-cloak x-on:click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

                <div class="min-w-0">
                    <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-3 border-b border-line bg-canvas/85 px-4 backdrop-blur sm:px-6">
                        <button type="button" x-on:click="sidebarOpen = true" class="grid size-10 place-items-center rounded-full ring-1 ring-line lg:hidden">
                            <x-icon name="menu" class="size-5" />
                            <span class="sr-only">মেনু খুলুন</span>
                        </button>

                        <a href="{{ \App\Support\Localization::route('home') }}" target="_blank" class="hidden items-center gap-1.5 text-sm text-ink-muted hover:text-ink sm:inline-flex">
                            ওয়েবসাইট দেখুন <x-icon name="external" class="size-4" />
                        </a>

                        <div class="flex items-center gap-3">
                            <x-theme-switcher />
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <x-button type="submit" variant="secondary" size="sm">
                                    <x-icon name="logout" class="size-4" /> লগআউট
                                </x-button>
                            </form>
                        </div>
                    </header>

                    <main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:py-8">
                        <x-admin.flash />
                        {{ $slot }}
                    </main>
                </div>
            </div>
        @else
            <main>{{ $slot }}</main>
        @endauth
    </body>
</html>
