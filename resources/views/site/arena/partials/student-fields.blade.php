{{--
    Name (required) and an optional guardian phone. Consent appears only
    once a phone is typed; a phone makes the sitting a lead for advice.
--}}
<div x-data="{ phone: @js(old('phone', '')) }" class="grid gap-4">
    <x-form.field name="student_name" :label="__('site.arena.student_name')" required autocomplete="name" />
    <x-form.field name="phone" type="tel" :label="__('site.arena.phone')" :help="__('site.arena.phone_help')" x-model="phone" inputmode="tel" autocomplete="tel" placeholder="01XXXXXXXXX" />
    <div x-show="phone.trim() !== ''" x-cloak>
        <x-form.checkbox name="consent" :label="__('site.arena.consent')" />
    </div>
    {{-- Honeypot --}}
    <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
</div>
