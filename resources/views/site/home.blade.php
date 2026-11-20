{{--
    Homepage. The hero is always shown; every other section is a partial in
    resources/views/site/home/, shown in the order and with the headings set
    in admin → Website → Homepage (App\Services\HomePageContent). Each
    partial renders nothing when it has no content, so an empty section
    never leaves a gap.
--}}
<x-layouts.app>
    @include('site.home.hero')

    @foreach ($sections as $section)
        @include("site.home.{$section['key']}", ['section' => $section])
    @endforeach
</x-layouts.app>
