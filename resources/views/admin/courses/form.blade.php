@use('App\Enums\PublishStatus')
@use('App\Support\Money')

@php
    $isEditing = $course->exists;
    $assignedTeacherIds = collect(old('teacher_ids', $isEditing ? $course->teachers->pluck('id')->all() : []))->map(fn ($id) => (int) $id);
@endphp

<x-layouts.admin :title="$isEditing ? 'কোর্স এডিট' : 'নতুন কোর্স'">
    <x-admin.page-header :title="$isEditing ? $course->translate('title', 'bn') : 'নতুন কোর্স'" description="বাংলা ঘর আবশ্যক; ইংরেজি খালি থাকলে ইংরেজি সাইটে বাংলাই দেখাবে।" />

    <form method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.courses.update', $course) : route('admin.courses.store') }}" class="grid gap-6">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <x-card title="মূল তথ্য">
            <div class="grid gap-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <x-form.select name="program_id" label="প্রোগ্রাম" :value="$course->program_id" :options="$programs->mapWithKeys(fn ($program) => [$program->id => $program->translate('name', 'bn')])->all()" placeholder="—" required />
                    <x-form.field name="slug" label="URL নাম (ইংরেজি)" :value="$course->slug" help="খালি রাখলে ইংরেজি শিরোনাম থেকে তৈরি হবে।" />
                </div>
                <x-form.translatable name="title" label="কোর্সের নাম" :value="$course->title" required />
                <x-form.translatable name="subtitle" label="সাবটাইটেল" :value="$course->subtitle" />
                <x-form.translatable name="summary" label="সংক্ষিপ্ত পরিচিতি (কার্ডে দেখাবে)" :value="$course->summary" :multiline="true" :rows="2" />
                <x-form.translatable name="description" label="কোর্স সম্পর্কে বিস্তারিত" :value="$course->description" :multiline="true" :rows="6" />
            </div>
        </x-card>

        <x-card title="এক্সপ্লেইনিং ভিডিও" description="YouTube Unlisted লিংক দিন। ভিডিও ছাড়া কোর্স প্রকাশ করা যাবে না।">
            <x-form.field name="explainer_video_url" type="url" label="YouTube লিংক" :value="$course->explainer_video_url" placeholder="https://youtu.be/..." />
            @if ($course->explainer_video_url)
                <x-youtube :url="$course->explainer_video_url" class="mt-4 max-w-xl" />
            @endif
        </x-card>

        <x-card title="এক নজরে">
            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <x-form.field name="class_from" type="number" label="শ্রেণি (থেকে)" :value="$course->class_from" />
                <x-form.field name="class_to" type="number" label="শ্রেণি (পর্যন্ত)" :value="$course->class_to" />
                <x-form.field name="age_from" type="number" label="বয়স (থেকে)" :value="$course->age_from" />
                <x-form.field name="age_to" type="number" label="বয়স (পর্যন্ত)" :value="$course->age_to" />
                <x-form.field name="live_class_count" type="number" label="লাইভ ক্লাস সংখ্যা" :value="$course->live_class_count" />
                <x-form.field name="class_duration_minutes" type="number" label="প্রতি ক্লাস (মিনিট)" :value="$course->class_duration_minutes" />
                <x-form.field name="duration_months" type="number" label="মেয়াদ (মাস)" :value="$course->duration_months" />
            </div>
        </x-card>

        <x-card title="কনটেন্ট">
            <div class="grid gap-4">
                <x-form.lines name="learning_outcomes" label="কোর্সটি করে যা শিখবে" :value="$course->learning_outcomes" />
                <x-form.lines name="highlights" label="কোর্সে যা থাকছে (যেমন: প্রতি ক্লাসের পর PDF শিট)" :value="$course->highlights" />
                <x-form.lines name="syllabus" label="সিলেবাস" :value="$course->syllabus" help="প্রতি লাইনে একটি মডিউল। শিরোনাম ও টপিক কোলন দিয়ে আলাদা করুন — যেমন: সংখ্যাতত্ত্ব: মৌলিক সংখ্যা, ভাজ্যতা" :rows="7" />
            </div>
        </x-card>

        <x-card title="শিক্ষক">
            @if ($teachers->isEmpty())
                <p class="text-sm text-ink-subtle">আগে “শিক্ষক” মেনু থেকে শিক্ষক যোগ করুন।</p>
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($teachers as $teacher)
                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl p-3 ring-1 ring-line ring-inset has-[:checked]:bg-accent-soft">
                            <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" @checked($assignedTeacherIds->contains($teacher->id)) class="size-4 accent-[var(--accent)]">
                            <span class="text-sm font-medium text-ink">{{ $teacher->translate('name', 'bn') }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card title="প্রকাশ ও SEO">
            <div class="grid gap-4">
                <div class="grid gap-4 md:grid-cols-3">
                    <x-form.select name="status" label="অবস্থা" :value="$course->status?->value" :options="PublishStatus::options()" />
                    <x-form.field name="sort_order" type="number" label="ক্রম" :value="$course->sort_order ?? 0" />
                    <div class="self-end pb-2"><x-form.checkbox name="is_featured" label="হোম পেজে ফিচার করুন" :checked="$course->is_featured" /></div>
                </div>
                <x-form.image name="thumbnail" label="কার্ডের ছবি (না দিলে ভিডিওর থাম্বনেইল দেখাবে)" :value="$course->thumbnail_url" />
                <x-form.translatable name="seo_title" label="SEO শিরোনাম (৭০ অক্ষরের মধ্যে)" :value="$course->seo_title" />
                <x-form.translatable name="seo_description" label="SEO বিবরণ (১৭০ অক্ষরের মধ্যে)" :value="$course->seo_description" :multiline="true" :rows="2" />
            </div>
        </x-card>

        <div class="flex gap-3">
            <x-button type="submit">{{ $isEditing ? 'কোর্স আপডেট করুন' : 'কোর্স তৈরি করুন' }}</x-button>
            <x-button :href="route('admin.courses.index')" variant="secondary">ফিরে যান</x-button>
        </div>
    </form>

    @if ($isEditing)
        {{-- Pricing variations --}}
        <x-card id="plans" class="mt-10 scroll-mt-24" title="পেমেন্ট প্ল্যান" description="প্রতিটি কোর্সে প্রয়োজনমতো প্ল্যান — মেইন প্রাইস আবশ্যক, ডিসকাউন্টেড প্রাইস ঐচ্ছিক।">
            <div class="grid gap-3">
                @foreach ($course->variations as $variation)
                    <details class="rounded-2xl ring-1 ring-line ring-inset">
                        <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3 p-4">
                            <span class="flex items-center gap-3">
                                <span class="font-semibold text-ink">{{ $variation->translate('label', 'bn') }}</span>
                                @unless ($variation->is_active) <x-badge>নিষ্ক্রিয়</x-badge> @endunless
                            </span>
                            <x-price :regular="$variation->regular_price" :discounted="$variation->discounted_price" size="sm" />
                        </summary>
                        <div class="border-t border-line p-4">
                            @include('admin.courses.partials.plan-form', ['variation' => $variation, 'action' => route('admin.courses.variations.update', [$course, $variation]), 'method' => 'PUT'])
                            <form method="POST" action="{{ route('admin.courses.variations.destroy', [$course, $variation]) }}" onsubmit="return confirm('প্ল্যানটি মুছে ফেলবেন?')" class="mt-3">
                                @csrf @method('DELETE')
                                <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> মুছুন</x-button>
                            </form>
                        </div>
                    </details>
                @endforeach

                <details class="rounded-2xl bg-surface-muted ring-1 ring-line ring-inset" @if ($course->variations->isEmpty()) open @endif>
                    <summary class="flex cursor-pointer list-none items-center gap-2 p-4 font-semibold text-accent-ink"><x-icon name="plus" class="size-4" /> নতুন প্ল্যান যোগ করুন</summary>
                    <div class="border-t border-line p-4">
                        @include('admin.courses.partials.plan-form', ['variation' => new App\Models\CourseVariation(['is_active' => true, 'billing_type' => 'one_time']), 'action' => route('admin.courses.variations.store', $course), 'method' => 'POST'])
                    </div>
                </details>
            </div>
        </x-card>

        {{-- Live batches --}}
        <x-card id="batches" class="mt-6 scroll-mt-24" title="ব্যাচ" description="নির্দিষ্ট শুরুর তারিখ, ক্লাসের দিন-সময় ও আসন সংখ্যা।">
            <div class="grid gap-3">
                @foreach ($course->batches as $batch)
                    <details class="rounded-2xl ring-1 ring-line ring-inset">
                        <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3 p-4">
                            <span>
                                <span class="font-semibold text-ink">{{ $batch->code }}</span>
                                <span class="text-sm text-ink-muted"> · {{ $batch->daysLabel() }} · {{ $batch->starts_on?->format('d/m/Y') }}</span>
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="text-sm text-ink-muted">{{ $batch->seats_taken }}/{{ $batch->seat_limit ?? '∞' }}</span>
                                <x-badge :tone="$batch->status->tone()">{{ $batch->status->label() }}</x-badge>
                            </span>
                        </summary>
                        <div class="border-t border-line p-4">
                            @include('admin.courses.partials.batch-form', ['batch' => $batch, 'action' => route('admin.courses.batches.update', [$course, $batch]), 'method' => 'PUT'])
                            <form method="POST" action="{{ route('admin.courses.batches.destroy', [$course, $batch]) }}" onsubmit="return confirm('ব্যাচটি মুছে ফেলবেন?')" class="mt-3">
                                @csrf @method('DELETE')
                                <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> মুছুন</x-button>
                            </form>
                        </div>
                    </details>
                @endforeach

                <details class="rounded-2xl bg-surface-muted ring-1 ring-line ring-inset" @if ($course->batches->isEmpty()) open @endif>
                    <summary class="flex cursor-pointer list-none items-center gap-2 p-4 font-semibold text-accent-ink"><x-icon name="plus" class="size-4" /> নতুন ব্যাচ যোগ করুন</summary>
                    <div class="border-t border-line p-4">
                        @include('admin.courses.partials.batch-form', ['batch' => new App\Models\CourseBatch(['status' => App\Enums\BatchStatus::Enrolling]), 'action' => route('admin.courses.batches.store', $course), 'method' => 'POST'])
                    </div>
                </details>
            </div>
        </x-card>

        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('কোর্সটি মুছে ফেলবেন?')" class="mt-10">
            @csrf @method('DELETE')
            <x-button type="submit" variant="ghost" size="sm" class="text-accent-ink"><x-icon name="trash" class="size-4" /> কোর্স মুছে ফেলুন</x-button>
        </form>
    @endif
</x-layouts.admin>
