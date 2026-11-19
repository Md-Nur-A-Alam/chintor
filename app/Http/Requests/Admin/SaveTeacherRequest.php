<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Models\Teacher;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveTeacherRequest extends FormRequest
{
    use HandlesImageInput;

    public function authorize(): bool
    {
        return $this->user()?->can('catalog.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('name.en', '')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'alpha_dash', 'max:120', Rule::unique('teachers', 'slug')->ignore($this->route('teacher'))],
            'name.bn' => ['required', 'string', 'max:120'],
            'name.en' => ['nullable', 'string', 'max:120'],
            'designation.*' => ['nullable', 'string', 'max:150'],
            'summary.*' => ['nullable', 'string', 'max:500'],
            'details_mode' => ['required', Rule::in([Teacher::DetailsRich, Teacher::DetailsHtml])],
            'details_rich.*' => ['nullable', 'string', 'max:10000'],
            'details_html' => ['nullable', 'string', 'max:200000'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ...self::imageRules('photo'),
        ];
    }

    /**
     * Custom HTML may contain scripts, so only admins may change it
     * (requirements §3.4). It is always rendered in a sandboxed iframe.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $current = $this->route('teacher')?->details_html;
                $changesHtml = (string) $this->input('details_html') !== (string) $current;

                if ($changesHtml && ! $this->user()->isSuperAdmin()) {
                    $validator->errors()->add('details_html', 'কাস্টম HTML শুধু অ্যাডমিন বদলাতে পারেন।');
                }
            },
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        return [
            'slug' => $this->validated('slug'),
            'name' => TranslatableInput::text($this->validated('name')),
            'designation' => TranslatableInput::text($this->validated('designation')),
            'summary' => TranslatableInput::text($this->validated('summary')),
            'details_mode' => $this->validated('details_mode'),
            'details_rich' => TranslatableInput::text($this->validated('details_rich')),
            'details_html' => $this->validated('details_html'),
            'is_active' => $this->validated('is_active'),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
