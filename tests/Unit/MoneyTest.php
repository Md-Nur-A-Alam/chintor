<?php

use App\Support\Money;

test('amounts use South Asian digit grouping', function (int $amount, string $expected) {
    expect(Money::groupDigits($amount))->toBe($expected);
})->with([
    [0, '0'],
    [999, '999'],
    [1000, '1,000'],
    [99999, '99,999'],
    [100000, '1,00,000'],
    [1234567, '12,34,567'],
]);

test('taka amounts are formatted with locale digits', function () {
    expect(Money::taka(125000, 'bn'))->toBe('৳১,২৫,০০০')
        ->and(Money::taka(125000, 'en'))->toBe('৳1,25,000')
        ->and(Money::taka(-500, 'en'))->toBe('-৳500');
});

test('the discount percentage is rounded down and never negative', function () {
    expect(Money::discountPercent(3000, 2400))->toBe(20)
        ->and(Money::discountPercent(2999, 2000))->toBe(33)
        ->and(Money::discountPercent(1000, 1200))->toBe(0)
        ->and(Money::discountPercent(0, 0))->toBe(0);
});
