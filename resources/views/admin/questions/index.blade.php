<x-layouts.admin title="প্রশ্ন ব্যাংক">
    <x-admin.page-header title="প্রশ্ন ব্যাংক" description="এরিনার MCQ প্রশ্ন — বিষয়, টপিক ও শ্রেণি দিয়ে চিহ্নিত। শিক্ষার্থীরা এগুলো থেকে নিজের মতো প্র্যাকটিস বানায়; নির্দিষ্ট পরীক্ষাতেও এখান থেকে প্রশ্ন বাছা হয়।">
        <x-slot:actions>
            <x-button :href="route('admin.questions.create', request()->only(['subject', 'topic', 'class']))" size="sm"><x-icon name="plus" class="size-4" /> নতুন প্রশ্ন</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="GET" class="mb-5 grid gap-3 rounded-card bg-surface p-4 shadow-card ring-1 ring-line md:grid-cols-[1fr_1fr_0.7fr_1.3fr_auto] md:items-end">
        <x-form.select name="subject" label="বিষয়" :value="request('subject')" placeholder="সব" :options="$subjects->mapWithKeys(fn ($subject) => [$subject->id => $subject->translate('name', 'bn')])->all()" />
        <x-form.select name="topic" label="টপিক" :value="request('topic')" placeholder="সব" :options="$subjects->flatMap(fn ($subject) => $subject->children->mapWithKeys(fn ($topic) => [$topic->id => $subject->translate('name', 'bn').' › '.$topic->translate('name', 'bn')]))->all()" />
        <x-form.select name="class" label="শ্রেণি" :value="request('class')" placeholder="সব" :options="collect(range(1, 12))->mapWithKeys(fn ($class) => [$class => $class.' শ্রেণি'])->all()" />
        <x-form.field name="search" label="প্রশ্নে খুঁজুন" :value="request('search')" />
        <x-button type="submit" variant="secondary">ফিল্টার</x-button>
    </form>

    <x-admin.table>
        <x-slot:head>
            <tr><th class="px-4 py-3">প্রশ্ন</th><th class="px-4 py-3">বিষয় › টপিক</th><th class="px-4 py-3">শ্রেণি</th><th class="px-4 py-3">অবস্থা</th><th class="px-4 py-3 text-right">অ্যাকশন</th></tr>
        </x-slot:head>
        @forelse ($questions as $question)
            <tr>
                <td class="max-w-md px-4 py-3">
                    <p class="line-clamp-2 font-medium text-ink">{{ $question->body }}</p>
                    <p class="mt-1 text-xs text-ink-subtle">সঠিক: {{ $question->options[$question->correct_option] ?? '—' }} @if ($question->hasExplanation()) · ব্যাখ্যা আছে @endif</p>
                </td>
                <td class="px-4 py-3 text-ink-muted">{{ $question->subject->translate('name', 'bn') }}@if ($question->topic) › {{ $question->topic->translate('name', 'bn') }}@endif</td>
                <td class="px-4 py-3 text-ink-muted">{{ $question->class_level ?? '—' }}</td>
                <td class="px-4 py-3"><x-badge :tone="$question->is_active ? 'success' : 'neutral'">{{ $question->is_active ? 'চালু' : 'বন্ধ' }}</x-badge></td>
                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        <x-button :href="route('admin.questions.edit', $question)" variant="ghost" size="sm" aria-label="এডিট"><x-icon name="pencil" class="size-4" /></x-button>
                        <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('প্রশ্নটি মুছে ফেলবেন? যেসব পরীক্ষায় আছে সেখান থেকেও সরে যাবে।')">@csrf @method('DELETE')<x-button type="submit" variant="ghost" size="sm" class="text-accent-ink" aria-label="মুছুন"><x-icon name="trash" class="size-4" /></x-button></form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-ink-subtle">কোনো প্রশ্ন পাওয়া যায়নি।</td></tr>
        @endforelse
    </x-admin.table>

    <div class="mt-6">{{ $questions->links() }}</div>
</x-layouts.admin>
