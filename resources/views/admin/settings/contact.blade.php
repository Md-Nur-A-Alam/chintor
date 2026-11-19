<x-layouts.admin title="যোগাযোগ সেটিংস">
    <x-admin.page-header title="যোগাযোগের তথ্য" description="ওয়েবসাইটের হেডার, ফুটার, মোবাইল বার ও WhatsApp বাটনে এগুলো দেখাবে। খালি রাখলে সেই অংশ লুকানো থাকবে।" />

    <form method="POST" action="{{ route('admin.settings.contact.update') }}" class="grid gap-6">
        @csrf
        @method('PUT')

        <x-card title="ফোন ও মেসেজ">
            <div class="grid gap-4 md:grid-cols-2">
                <x-form.field name="hotline" label="হেল্পলাইন" :value="$contact['hotline']" placeholder="09XXXXXXXXX" />
                <x-form.field name="whatsapp" label="WhatsApp নম্বর" :value="$contact['whatsapp']" placeholder="01XXXXXXXXX" />
                <x-form.field name="messenger_url" label="Messenger লিংক" type="url" :value="$contact['messenger_url']" placeholder="https://m.me/..." />
                <x-form.field name="email" label="ইমেইল" type="email" :value="$contact['email']" />
                <x-form.field name="office_hours" label="অফিসের সময়" :value="$contact['office_hours']" placeholder="09:00-22:00" />
            </div>
        </x-card>

        <x-card title="সোশ্যাল ও ঠিকানা">
            <div class="grid gap-4 md:grid-cols-2">
                <x-form.field name="facebook_url" label="Facebook পেজ" type="url" :value="$contact['facebook_url']" />
                <x-form.field name="youtube_url" label="YouTube চ্যানেল" type="url" :value="$contact['youtube_url']" />
            </div>
            <div class="mt-4">
                <x-form.textarea name="address" label="অফিসের ঠিকানা" :value="$contact['address']" rows="3" />
            </div>
        </x-card>

        <x-card title="আইনি তথ্য" description="ট্রেড লাইসেন্স ও TIN নিরাপত্তার জন্য .env ফাইলে রাখা হয় (CHINTOR_TRADE_LICENSE, CHINTOR_TIN)।">
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-ink-subtle">ট্রেড লাইসেন্স</dt><dd class="font-medium">{{ $legal['trade_license'] ?: '—' }}</dd></div>
                <div><dt class="text-ink-subtle">TIN</dt><dd class="font-medium">{{ $legal['tin'] ?: '—' }}</dd></div>
            </dl>
        </x-card>

        <div><x-button type="submit">সেভ করুন</x-button></div>
    </form>
</x-layouts.admin>
