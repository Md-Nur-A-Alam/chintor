{{-- One pricing variation. Used for both "add" and "edit" on the course page. --}}
<form method="POST" action="{{ $action }}" class="grid gap-4">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <x-form.translatable name="label" label="প্ল্যানের নাম (যেমন: মাসিক, ৩ মাস, ফুল কোর্স)" :value="$variation->label" required />
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-form.field name="regular_price" type="number" label="মেইন প্রাইস (৳)" :value="$variation->regular_price" required />
        <x-form.field name="discounted_price" type="number" label="ডিসকাউন্টেড প্রাইস (৳)" :value="$variation->discounted_price" help="ঐচ্ছিক — থাকলে মেইন প্রাইস কাটা দেখাবে।" />
        <x-form.select name="billing_type" label="পেমেন্টের ধরন" :value="$variation->billing_type" :options="['one_time' => 'একবারে', 'monthly' => 'মাসিক']" />
        <x-form.field name="plan_duration_months" type="number" label="মেয়াদ (মাস)" :value="$variation->duration_months" />
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-2"><x-form.translatable name="badge" label="ব্যাজ (ঐচ্ছিক, যেমন: সেরা মূল্য)" :value="$variation->badge" /></div>
        <x-form.field name="plan_sort_order" type="number" label="ক্রম" :value="$variation->sort_order ?? 0" />
        <div class="self-end pb-2"><x-form.checkbox name="is_active" label="সক্রিয়" :checked="$variation->is_active" /></div>
    </div>
    <div><x-button type="submit" size="sm">সেভ করুন</x-button></div>
</form>
