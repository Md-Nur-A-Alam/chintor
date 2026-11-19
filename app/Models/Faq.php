<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A question and answer. Without a "faqable" owner it is a general FAQ;
 * with one (e.g. a course) it shows on that page only.
 */
class Faq extends Model
{
    use HasTranslations;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'faqable_type',
        'faqable_id',
        'question',
        'answer',
        'is_active',
        'sort_order',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function faqable(): MorphTo
    {
        return $this->morphTo();
    }

    #[Scope]
    protected function general(Builder $query): void
    {
        $query->whereNull('faqable_type')->where('is_active', true)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'question' => 'array',
            'answer' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
