<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A top-level group of courses (e.g. an olympiad or academic program).
 * Programs are fully admin-managed; nothing about them is hard-coded.
 */
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory, HasTranslations, Publishable, SoftDeletes;

    /**
     * Accent colours a program card may use (see the design system).
     */
    public const ACCENTS = ['teal', 'coral', 'sun'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'name',
        'tagline',
        'description',
        'icon',
        'accent',
        'image_url',
        'status',
        'sort_order',
    ];

    /**
     * @return HasMany<Course, $this>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * @return HasMany<Book, $this>
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'tagline' => 'array',
            'description' => 'array',
            'status' => PublishStatus::class,
        ];
    }
}
