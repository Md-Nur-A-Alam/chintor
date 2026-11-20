@props([
    'reverse' => false,
    'duration' => 45,
    'label' => null,
])

{{--
    Endless horizontal strip ("traffic lane"). The content is rendered twice
    and the track slides by exactly half its width, so the loop is seamless;
    the copy is hidden from screen readers and keyboard focus. Pauses on
    hover/focus. With reduced motion it becomes a normal scrollable row.
    Put two in a row with :reverse="true" on one for opposite lanes.
--}}
<div {{ $attributes->class(['marquee']) }} @if ($label) role="region" aria-label="{{ $label }}" @endif>
    <div @class(['marquee-track', 'marquee-reverse' => $reverse]) style="--marquee-duration: {{ (int) $duration }}s">
        <div class="flex shrink-0 gap-4 pr-4">{{ $slot }}</div>
        <div class="flex shrink-0 gap-4 pr-4" aria-hidden="true" inert data-marquee-clone>{{ $slot }}</div>
    </div>
</div>
