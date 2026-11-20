@use('App\Models\Question')

@php
    $isEditing = $question->exists;
    $options = array_pad(old('options', $question->options ?? []), Question::MAX_OPTIONS, '');
    $correct = (int) old('correct_option', $question->correct_option ?? 0);
    $labels = ['ক', 'খ', 'গ', 'ঘ', 'ঙ'];
@endphp

<x-layouts.admin :title="$isEditing ? 'প্রশ্ন এডিট' : 'নতুন প্রশ্ন'">
    <x-admin.page-header :title="$isEditing ? 'প্রশ্ন এডিট' : 'নতুন প্রশ্ন'" />

    @if ($subjects->isEmpty())
        <x-card><p class="text-ink-muted">আগে <a href="{{ route('admin.learning-topics.index') }}" class="font-semibold text-accent-ink underline">বিষয় ও টপিক</a> যোগ করুন।</p></x-card>
    @else
        <form method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.questions.update', $question) : route('admin.questions.store') }}" class="grid gap-6">
            @csrf
            @if ($isEditing) @method('PUT') @endif

            <x-card title="শ্রেণিবিন্যাস">
                <div class="grid gap-4">
                    @include('admin.partials.subject-topic-fields', ['subjectId' => $question->subject_id, 'topicId' => $question->topic_id])
                    <x-form.select name="class_level" label="শ্রেণি" :value="$question->class_level" placeholder="যেকোনো শ্রেণি" :options="collect(range(1, 12))->mapWithKeys(fn ($class) => [$class => $class.' শ্রেণি'])->all()" />
                </div>
            </x-card>

            <x-card title="প্রশ্ন ও উত্তর" description="অন্তত ২টি বিকল্প লিখুন; বাকিগুলো ফাঁকা রাখলে বাদ যাবে। সঠিক উত্তরের পাশের বৃত্তে টিক দিন।">
                <div class="grid gap-4">
                    <x-form.textarea name="body" label="প্রশ্ন" :value="$question->body" :rows="3" required />
                    <x-form.image name="image" label="প্রশ্নের ছবি (ঐচ্ছিক — চিত্র, জ্যামিতি ইত্যাদি)" :value="$question->image_url" />

                    <fieldset class="grid gap-2.5">
                        <legend class="mb-1 text-sm font-semibold text-ink">উত্তর-বিকল্প</legend>
                        @foreach ($options as $index => $option)
                            <div class="flex items-center gap-3 rounded-xl bg-surface-muted p-2 pr-3 has-[:checked]:bg-brand-soft has-[:checked]:ring-2 has-[:checked]:ring-brand">
                                <label class="flex shrink-0 cursor-pointer items-center gap-2 pl-2 text-sm font-bold text-ink">
                                    <input type="radio" name="correct_option" value="{{ $index }}" @checked($correct === $index) class="size-5 accent-[var(--brand)]" aria-label="বিকল্প {{ $labels[$index] }} সঠিক">
                                    {{ $labels[$index] }}
                                </label>
                                <input type="text" name="options[{{ $index }}]" value="{{ $option }}" placeholder="বিকল্প {{ $labels[$index] }}" class="h-11 flex-1 rounded-lg bg-surface px-3 text-ink ring-1 ring-line-strong ring-inset focus:ring-2 focus:ring-focus">
                            </div>
                        @endforeach
                        @error('options') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
                        @error('correct_option') <p class="text-sm text-accent-ink">{{ $message }}</p> @enderror
                    </fieldset>
                </div>
            </x-card>

            <x-card title="ব্যাখ্যা (ফলাফলের পেজে দেখাবে)" description="লেখা, ছবি বা Google Drive-এর PDF লিংক — যেকোনোটা বা সবগুলো।">
                <div class="grid gap-4">
                    <x-form.textarea name="explanation" label="ব্যাখ্যা" :value="$question->explanation" :rows="4" />
                    <x-form.image name="explanation_image" label="ব্যাখ্যার ছবি" :value="$question->explanation_image_url" />
                    <x-form.field name="explanation_pdf_url" type="url" label="ব্যাখ্যার PDF (Google Drive লিংক)" :value="$question->explanation_pdf_url" placeholder="https://drive.google.com/file/d/…/view" help="Drive-এ ফাইলটির শেয়ার সেটিং “Anyone with the link” রাখুন।" />
                </div>
            </x-card>

            <x-card>
                <x-form.checkbox name="is_active" label="চালু (প্র্যাকটিস ও পরীক্ষায় ব্যবহার হবে)" :checked="$question->is_active" />
            </x-card>

            <div class="flex flex-wrap gap-3">
                <x-button type="submit">সেভ করুন</x-button>
                @unless ($isEditing)
                    <x-button type="submit" name="add_another" value="1" variant="secondary">সেভ করে পরেরটি লিখুন</x-button>
                @endunless
                <x-button :href="route('admin.questions.index')" variant="ghost">ফিরে যান</x-button>
            </div>
        </form>
    @endif
</x-layouts.admin>
