{{--
    Homepage. The hero is always shown; every other section is a partial in
    resources/views/site/home/, shown in the order and with the headings set
    in admin → Website → Homepage (App\Services\HomePageContent). Each
    partial renders nothing when it has no content, so an empty section
    never leaves a gap.

    Standard sections alternate between plain and tinted ("band")
    backgrounds whatever order they are in, so the page keeps its rhythm.
--}}
@php
    $fullWidthSections = ['highlights', 'stats', 'cta'];
    $nextIsBand = false;
@endphp

<x-layouts.app>
    @include('site.home.hero')

    @foreach ($sections as $section)
        @php
            $section['band'] = false;

            if (! in_array($section['key'], $fullWidthSections, true)) {
                $section['band'] = $nextIsBand;
                $nextIsBand = ! $nextIsBand;
            }
        @endphp

        @include("site.home.{$section['key']}", ['section' => $section])
    @endforeach
</x-layouts.app>
