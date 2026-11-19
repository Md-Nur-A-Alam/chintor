<?php

use App\View\Components\Price;

test('only the regular price is shown when there is no discount', function () {
    $this->component(Price::class, ['regular' => 2500])
        ->assertSee('৳২,৫০০')
        ->assertDontSee('<del', false);
});

test('a valid discount strikes through the regular price and highlights the discounted price', function () {
    $this->component(Price::class, ['regular' => 3000, 'discounted' => 2400])
        ->assertSeeInOrder(['data-price-payable', '৳২,৪০০', '<del', '৳৩,০০০'], false)
        ->assertSee('২০% ছাড়');
});

test('a discounted price that is not lower than the regular price is ignored', function (int $discounted) {
    $this->component(Price::class, ['regular' => 1500, 'discounted' => $discounted])
        ->assertSee('৳১,৫০০')
        ->assertDontSee('<del', false);
})->with([1500, 1800]);

test('prices use English digits in the English locale', function () {
    app()->setLocale('en');

    $this->component(Price::class, ['regular' => 125000, 'discounted' => 99000])
        ->assertSee('৳99,000')
        ->assertSee('৳1,25,000')
        ->assertSee('20% off');
});
