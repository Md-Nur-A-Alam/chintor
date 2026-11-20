@props([
    'index' => 0,
])

{{--
    Fades its content up when scrolled into view (resources/js/motion.js).
    In a grid, pass the item's index for a short stagger (capped so long
    lists never wait). It is a grid itself, so a card inside fills the cell.
--}}
<div data-reveal style="--reveal-index: {{ min((int) $index, 5) }}" {{ $attributes->class(['grid']) }}>
    {{ $slot }}
</div>
