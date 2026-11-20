<?php

namespace App\Http\Requests\Admin;

use App\Services\HomePageContent;
use App\Support\TranslatableInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Hero texts and buttons, impact numbers, and section order/visibility.
 *
 * Buttons and stats are posted as fixed rows; empty rows are dropped.
 * Sections are posted keyed by section key with a visible flag, an order
 * number and optional heading overrides.
 */
class UpdateHomePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('homepage.manage') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $text = ['nullable', 'string', 'max:200'];

        return [
            'hero.eyebrow.*' => ['nullable', 'string', 'max:120'],
            'hero.title.*' => ['nullable', 'string', 'max:120'],
            'hero.subtitle.*' => ['nullable', 'string', 'max:400'],
            'hero.video_url' => ['nullable', 'url', 'max:255'],
            'hero.show_trust_chips' => ['boolean'],

            'buttons' => ['array', 'max:'.HomePageContent::MAX_BUTTONS],
            'buttons.*.label.bn' => ['nullable', 'string', 'max:40', 'required_with:buttons.*.url'],
            'buttons.*.label.en' => ['nullable', 'string', 'max:40'],
            'buttons.*.url' => ['nullable', 'string', 'max:500', 'required_with:buttons.*.label.bn', 'regex:/^(\/|https?:\/\/|tel:|mailto:|#)/i'],
            'buttons.*.style' => ['nullable', Rule::in(array_keys(HomePageContent::ButtonStyles))],

            'stats' => ['array', 'max:'.HomePageContent::MAX_STATS],
            'stats.*.value' => ['nullable', 'string', 'max:20', 'required_with:stats.*.label.bn'],
            'stats.*.label.bn' => ['nullable', 'string', 'max:60', 'required_with:stats.*.value'],
            'stats.*.label.en' => ['nullable', 'string', 'max:60'],

            'sections' => ['required', 'array'],
            'sections.*.visible' => ['boolean'],
            'sections.*.order' => ['nullable', 'integer', 'min:0', 'max:99'],
            'sections.*.title.*' => $text,
            'sections.*.subtitle.*' => ['nullable', 'string', 'max:300'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'buttons.*.url.regex' => 'লিংক "/" দিয়ে (সাইটের পেজ), অথবা https://, tel:, mailto: বা # দিয়ে শুরু করুন।',
            'buttons.*.url.required_with' => 'বাটনের লিংক দিন।',
            'buttons.*.label.bn.required_with' => 'বাটনের বাংলা লেখা দিন।',
            'stats.*.value.required_with' => 'সংখ্যাটি দিন।',
            'stats.*.label.bn.required_with' => 'সংখ্যার পরিচিতি (বাংলা) দিন।',
        ];
    }

    /**
     * The shape stored by HomePageContent::update().
     *
     * @return array{hero: array<string, mixed>, stats: list<array<string, mixed>>, sections: list<array<string, mixed>>}
     */
    public function content(): array
    {
        $validated = $this->validated();

        return [
            'hero' => [
                'eyebrow' => TranslatableInput::text($validated['hero']['eyebrow'] ?? null),
                'title' => TranslatableInput::text($validated['hero']['title'] ?? null),
                'subtitle' => TranslatableInput::text($validated['hero']['subtitle'] ?? null),
                'buttons' => $this->buttons($validated['buttons'] ?? []),
                'video_url' => $validated['hero']['video_url'] ?? null,
                'show_trust_chips' => (bool) ($validated['hero']['show_trust_chips'] ?? false),
            ],
            'stats' => $this->stats($validated['stats'] ?? []),
            'sections' => $this->sections($validated['sections']),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return list<array{label: array<string, ?string>, url: string, style: string}>
     */
    private function buttons(array $rows): array
    {
        $buttons = [];

        foreach ($rows as $row) {
            $label = TranslatableInput::text($row['label'] ?? null);

            if ($label !== null && filled($row['url'] ?? null)) {
                $buttons[] = ['label' => $label, 'url' => trim($row['url']), 'style' => $row['style'] ?? 'primary'];
            }
        }

        return $buttons;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return list<array{value: string, label: array<string, ?string>}>
     */
    private function stats(array $rows): array
    {
        $stats = [];

        foreach ($rows as $row) {
            $label = TranslatableInput::text($row['label'] ?? null);

            if ($label !== null && filled($row['value'] ?? null)) {
                $stats[] = ['value' => trim($row['value']), 'label' => $label];
            }
        }

        return $stats;
    }

    /**
     * Known sections sorted by their order number (ties keep the form order).
     *
     * @param  array<string, array<string, mixed>>  $rows
     * @return list<array{key: string, visible: bool, title: ?array<string, ?string>, subtitle: ?array<string, ?string>}>
     */
    private function sections(array $rows): array
    {
        $sections = [];
        $position = 0;

        foreach ($rows as $key => $row) {
            if (! array_key_exists($key, HomePageContent::SECTIONS)) {
                continue;
            }

            $sections[] = [
                'sort' => [(int) ($row['order'] ?? 99), $position++],
                'key' => $key,
                'visible' => (bool) ($row['visible'] ?? false),
                'title' => TranslatableInput::text($row['title'] ?? null),
                'subtitle' => TranslatableInput::text($row['subtitle'] ?? null),
            ];
        }

        usort($sections, fn (array $first, array $second): int => $first['sort'] <=> $second['sort']);

        return array_map(function (array $section): array {
            unset($section['sort']);

            return $section;
        }, $sections);
    }
}
