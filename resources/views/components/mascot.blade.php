@props([
    'mood' => 'happy',
])

@php
    $gradientId = 'chintu-'.\Illuminate\Support\Str::random(6);
@endphp

{{--
    "চিন্তু" — Chintor's mascot, grown from the four-point spark in the logo.
    Moods: happy (default), wave, think. Decorative unless a label is passed.
--}}
<svg
    viewBox="0 0 200 200"
    xmlns="http://www.w3.org/2000/svg"
    {{ $attributes->merge(['aria-hidden' => 'true'])->class(['shrink-0']) }}
>
    <defs>
        <linearGradient id="{{ $gradientId }}" x1="30" y1="20" x2="170" y2="180" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#ffa48b" />
            <stop offset="0.55" stop-color="#fe704d" />
            <stop offset="1" stop-color="#e25439" />
        </linearGradient>
    </defs>

    {{-- Soft shadow --}}
    <ellipse cx="100" cy="186" rx="44" ry="7" fill="currentColor" opacity="0.12" />

    {{-- Body: the logo spark with rounded concave sides --}}
    <path
        fill="url(#{{ $gradientId }})"
        d="M100 12 C106 62 138 94 188 100 C138 106 106 138 100 188 C94 138 62 106 12 100 C62 94 94 62 100 12 Z"
    />
    <path fill="#fff" opacity="0.28" d="M100 22 C103 52 118 72 140 82 C116 78 102 62 100 22 Z" />

    {{-- Arms --}}
    @if ($mood === 'wave')
        <path d="M150 92 C162 78 170 70 176 58" fill="none" stroke="#e25439" stroke-width="7" stroke-linecap="round" />
        <circle cx="178" cy="54" r="7" fill="#ffc145" />
    @endif

    {{-- Eyes --}}
    @if ($mood === 'think')
        <path d="M78 100 q7 -6 14 0" fill="none" stroke="#022626" stroke-width="5" stroke-linecap="round" />
        <path d="M108 100 q7 -6 14 0" fill="none" stroke="#022626" stroke-width="5" stroke-linecap="round" />
        <circle cx="150" cy="56" r="5" fill="#ffc145" />
        <circle cx="162" cy="40" r="7" fill="#ffc145" />
        <circle cx="178" cy="22" r="10" fill="#ffc145" />
    @else
        <ellipse cx="85" cy="100" rx="7" ry="9" fill="#022626" />
        <ellipse cx="115" cy="100" rx="7" ry="9" fill="#022626" />
        <circle cx="87.5" cy="96.5" r="2.5" fill="#fff" />
        <circle cx="117.5" cy="96.5" r="2.5" fill="#fff" />
    @endif

    {{-- Cheeks and smile --}}
    <ellipse cx="72" cy="114" rx="7" ry="4.5" fill="#ffc7b6" opacity="0.9" />
    <ellipse cx="128" cy="114" rx="7" ry="4.5" fill="#ffc7b6" opacity="0.9" />
    <path d="M90 116 q10 10 20 0" fill="none" stroke="#022626" stroke-width="4.5" stroke-linecap="round" />
</svg>
