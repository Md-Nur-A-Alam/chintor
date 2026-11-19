@use('App\Enums\AssessmentStatus')
@use('App\Models\Assessment')

@php
    $courseOptions = $courses->mapWithKeys(fn ($course) => [$course->id => $course->translate('title', 'bn')])->all();
@endphp

<x-layouts.admin title="অ্যাসেসমেন্ট">
    <x-admin.page-header :title="'অ্যাসেসমেন্ট — '.($assessment->lead->student_name ?? $assessment->lead->displayName())" :description="$assessment->lead->phone">
        <x-slot:actions>
            <x-button :href="route('admin.leads.show', $assessment->lead)" variant="secondary" size="sm">লিড দেখুন</x-button>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="POST" action="{{ route('admin.assessments.update', $assessment) }}" class="grid gap-6">
        @csrf @method('PUT')

        <x-card title="সময় ও শিক্ষক">
            <div class="grid gap-4 md:grid-cols-3">
                <x-form.select name="status" label="অবস্থা" :value="$assessment->status->value" :options="AssessmentStatus::options()" />
                <x-form.select name="teacher_id" label="শিক্ষক" :value="$assessment->teacher_id" placeholder="—" :options="$teachers->mapWithKeys(fn ($teacher) => [$teacher->id => $teacher->translate('name', 'bn')])->all()" />
                <x-form.select name="course_id" label="কোর্স" :value="$assessment->course_id" placeholder="—" :options="$courseOptions" />
                <x-form.field name="scheduled_at" type="datetime-local" label="সময়" :value="$assessment->scheduled_at?->format('Y-m-d\TH:i')" />
                <x-form.field name="duration_minutes" type="number" label="সময়কাল (মিনিট)" :value="$assessment->duration_minutes ?? 30" />
                <x-form.field name="meeting_url" type="url" label="Zoom/Meet লিংক" :value="$assessment->meeting_url" />
            </div>
        </x-card>

        <x-card title="ফলাফল" description="অভিভাবককে জানানোর আগে এখানে লিখে রাখুন — সেলস টিম সঠিক কোর্স সাজেস্ট করবে।">
            <div class="grid gap-4 md:grid-cols-2">
                <x-form.select name="result_level" label="বর্তমান অবস্থা" :value="$assessment->result_level" placeholder="—" :options="Assessment::LEVELS" />
                <x-form.select name="recommended_course_id" label="সুপারিশকৃত কোর্স" :value="$assessment->recommended_course_id" placeholder="—" :options="$courseOptions" />
                <div class="md:col-span-2"><x-form.textarea name="teacher_notes" label="শিক্ষকের মন্তব্য" :value="$assessment->teacher_notes" rows="4" /></div>
            </div>
        </x-card>

        <div><x-button type="submit">সেভ করুন</x-button></div>
    </form>
</x-layouts.admin>
