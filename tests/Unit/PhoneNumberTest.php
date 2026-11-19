<?php

use App\Support\PhoneNumber;

test('Bangladeshi mobile numbers are normalised to E.164', function (string $input) {
    expect(PhoneNumber::normalizeBangladeshMobile($input))->toBe('+8801712345678');
})->with([
    '01712345678',
    '01712-345678',
    '+8801712345678',
    '8801712345678',
    '+88 017 1234 5678',
    '০১৭১২৩৪৫৬৭৮',
]);

test('invalid Bangladeshi mobile numbers are rejected', function (?string $input) {
    expect(PhoneNumber::isBangladeshMobile($input))->toBeFalse();
})->with([
    null,
    '',
    '0171234567',
    '017123456789',
    '01212345678',
    '09610990880',
]);

test('WhatsApp links use the international number without a plus sign', function () {
    expect(PhoneNumber::whatsappUrl('01712345678'))->toBe('https://wa.me/8801712345678')
        ->and(PhoneNumber::whatsappUrl('01712345678', 'হ্যালো'))->toBe('https://wa.me/8801712345678?text='.rawurlencode('হ্যালো'))
        ->and(PhoneNumber::whatsappUrl(null))->toBeNull();
});

test('tel links keep only digits and a leading plus', function () {
    expect(PhoneNumber::forTelLink('+88 096-1099'))->toBe('+880961099')
        ->and(PhoneNumber::forTelLink(''))->toBeNull();
});
