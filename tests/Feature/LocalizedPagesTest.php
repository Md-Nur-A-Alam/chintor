<?php

use App\Services\SiteSettings;
use App\Support\Localization;

test('the home page is served in Bangla at the root', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('<html lang="bn"', false)
        ->assertSee('চলো একসাথে শিখি');
});

test('the home page is served in English under the en prefix', function () {
    $this->get('/en')
        ->assertOk()
        ->assertSee('<html lang="en"', false)
        ->assertSee("Let's learn together");
});

test('pages advertise every language version for search engines', function () {
    $html = $this->get('/design-system')->getContent();

    expect($html)
        ->toMatch('#hreflang="bn-BD" href="https?://[^"/]+/design-system"#')
        ->toMatch('#hreflang="en" href="https?://[^"/]+/en/design-system"#')
        ->toMatch('#hreflang="x-default" href="https?://[^"/]+/design-system"#')
        ->not->toContain('?view=');
});

test('the language switcher links to the same page in the other language', function () {
    expect($this->get('/design-system')->getContent())
        ->toMatch('#href="https?://[^"/]+/en/design-system"\s+hreflang="en"#');

    expect($this->get('/en/design-system')->getContent())
        ->toMatch('#href="https?://[^"/]+/design-system"\s+hreflang="bn-BD"#');
});

test('the theme is applied before first paint', function () {
    $this->get('/')
        ->assertSee("localStorage.getItem('chintor-theme')", false)
        ->assertSee('x-data="themeSwitcher"', false);
});

test('localized route names resolve per locale', function () {
    expect(Localization::route('home', locale: 'bn'))->toBe(url('/'))
        ->and(Localization::route('home', locale: 'en'))->toBe(url('/en'))
        ->and(Localization::baseRouteName('en.courses.show'))->toBe('courses.show');
});

test('Bangla digits are used only for the Bangla locale', function () {
    expect(Localization::digits('2026', 'bn'))->toBe('২০২৬')
        ->and(Localization::digits('2026', 'en'))->toBe('2026');
});

test('contact channels come from admin settings and are hidden when empty', function () {
    $this->get('/')->assertDontSee('wa.me', false);

    app(SiteSettings::class)->updateContact(['whatsapp' => '+8801712345678', 'address' => 'Mirpur, Dhaka']);

    $this->get('/')
        ->assertSee('https://wa.me/8801712345678', false)
        ->assertSee('Mirpur, Dhaka');
});

test('the design system page is available outside production', function () {
    $this->get('/design-system')->assertOk()->assertSee('noindex', false);
});
