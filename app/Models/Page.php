<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

/**
 * An editable page such as "About us" or a policy. The body is Markdown.
 */
class Page extends Model
{
    use HasTranslations, Publishable;

    /**
     * @var array<string, string>
     */
    public const TYPES = [
        'page' => 'সাধারণ পেজ',
        'policy' => 'নীতিমালা',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'type',
        'title',
        'body',
        'seo_description',
        'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'body' => 'array',
            'seo_description' => 'array',
            'status' => PublishStatus::class,
        ];
    }
}
