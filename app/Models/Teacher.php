<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A teacher profile. Details are either rich text or a fully custom HTML
 * page, rendered inside a sandboxed iframe (requirements §3.4).
 */
class Teacher extends Model
{
    /** @use HasFactory<TeacherFactory> */
    use HasFactory, HasTranslations, SoftDeletes;

    public const DetailsRich = 'rich';

    public const DetailsHtml = 'html';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'name',
        'designation',
        'photo_url',
        'summary',
        'details_mode',
        'details_rich',
        'details_html',
        'user_id',
        'is_active',
        'sort_order',
    ];

    /**
     * @return BelongsToMany<Course, $this>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot('sort_order');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function usesCustomHtml(): bool
    {
        return $this->details_mode === self::DetailsHtml && filled($this->details_html);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'designation' => 'array',
            'summary' => 'array',
            'details_rich' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
