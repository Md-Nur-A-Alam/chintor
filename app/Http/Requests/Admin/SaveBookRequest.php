<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Admin\Concerns\HandlesImageInput;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveBookRequest extends FormRequest
{
    use HandlesImageInput;

    public function authorize(): bool
    {
        return $this->user()?->can('books.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('title.en', '')),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'alpha_dash', 'max:120', Rule::unique('books', 'slug')->ignore($this->route('book'))],
            'program_id' => ['nullable', Rule::exists('programs', 'id')],
            'title.bn' => ['required', 'string', 'max:200'],
            'title.en' => ['nullable', 'string', 'max:200'],
            'subtitle.*' => ['nullable', 'string', 'max:255'],
            'description.*' => ['nullable', 'string', 'max:5000'],
            'table_of_contents.*' => ['nullable', 'string', 'max:5000'],
            'authors' => ['nullable', 'string', 'max:500'],
            'sample_pages' => ['nullable', 'string', 'max:5000'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'pages' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'edition' => ['nullable', 'string', 'max:50'],
            'published_year' => ['nullable', 'integer', 'between:2000,2100'],
            'class_from' => ['nullable', 'integer', 'between:0,12'],
            'class_to' => ['nullable', 'integer', 'between:0,12'],
            'regular_price' => ['required', 'integer', 'min:0', 'max:1000000'],
            'discounted_price' => ['nullable', 'integer', 'min:0', 'lt:regular_price'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'seo_title.*' => ['nullable', 'string', 'max:70'],
            'seo_description.*' => ['nullable', 'string', 'max:170'],
            ...self::imageRules('cover'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'discounted_price.lt' => 'ডিসকাউন্টেড প্রাইস অবশ্যই মেইন প্রাইসের চেয়ে কম হতে হবে।',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesForModel(): array
    {
        $status = PublishStatus::from($this->validated('status'));

        return [
            ...$this->safe()->only(['slug', 'program_id', 'isbn', 'pages', 'edition', 'published_year', 'class_from', 'class_to', 'regular_price', 'discounted_price', 'stock_quantity', 'is_featured']),
            'title' => TranslatableInput::text($this->validated('title')),
            'subtitle' => TranslatableInput::text($this->validated('subtitle')),
            'description' => TranslatableInput::text($this->validated('description')),
            'table_of_contents' => TranslatableInput::lines($this->validated('table_of_contents')),
            'sample_pages' => $this->samplePageUrls(),
            'seo_title' => TranslatableInput::text($this->validated('seo_title')),
            'seo_description' => TranslatableInput::text($this->validated('seo_description')),
            'status' => $status,
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }

    /**
     * @return list<string> Author names, comma separated in the form.
     */
    public function authorNames(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->validated('authors')))));
    }

    /**
     * @return list<string>
     */
    private function samplePageUrls(): array
    {
        $lines = preg_split('/\R/', (string) $this->validated('sample_pages')) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn (string $url) => filter_var($url, FILTER_VALIDATE_URL) !== false));
    }
}
