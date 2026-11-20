<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\LearningTopicFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A subject (no parent, e.g. "Maths") or a topic inside one (e.g.
 * "Geometry"). Shared by learning videos and exam questions, so the
 * website's filters and the question bank use the same names.
 */
class LearningTopic extends Model
{
    /** @use HasFactory<LearningTopicFactory> */
    use HasFactory, HasTranslations;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'slug',
        'name',
        'sort_order',
    ];

    /**
     * @return BelongsTo<LearningTopic, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<LearningTopic, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Videos filed under this subject.
     *
     * @return HasMany<LearningVideo, $this>
     */
    public function videosAsSubject(): HasMany
    {
        return $this->hasMany(LearningVideo::class, 'subject_id');
    }

    /**
     * Questions filed under this subject.
     *
     * @return HasMany<Question, $this>
     */
    public function questionsAsSubject(): HasMany
    {
        return $this->hasMany(Question::class, 'subject_id');
    }

    #[Scope]
    protected function subjects(Builder $query): void
    {
        $query->whereNull('parent_id')->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Subject → topics options for admin <select>s, e.g. "Maths › Geometry".
     *
     * @return array<string, array<int, string>> Subject name => [topic id => topic name]
     */
    public static function groupedOptions(): array
    {
        $options = [];

        foreach (self::query()->subjects()->with('children')->get() as $subject) {
            $options[$subject->translate('name', 'bn')] = $subject->children
                ->mapWithKeys(fn (LearningTopic $topic) => [$topic->id => $topic->translate('name', 'bn')])
                ->all();
        }

        return $options;
    }

    protected function casts(): array
    {
        return [
            'name' => 'array',
        ];
    }
}
