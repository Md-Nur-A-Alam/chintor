<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A printed book written by Chintor's own authors (delivered by courier;
 * no soft copies are sold).
 */
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory, HasTranslations, Publishable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'isbn',
        'program_id',
        'title',
        'subtitle',
        'description',
        'table_of_contents',
        'class_from',
        'class_to',
        'cover_url',
        'sample_pages',
        'pages',
        'edition',
        'published_year',
        'language',
        'regular_price',
        'discounted_price',
        'stock_quantity',
        'seo_title',
        'seo_description',
        'status',
        'published_at',
        'is_featured',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsToMany<Author, $this>
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function payablePrice(): int
    {
        return $this->discounted_price !== null && $this->discounted_price < $this->regular_price
            ? $this->discounted_price
            : $this->regular_price;
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'subtitle' => 'array',
            'description' => 'array',
            'table_of_contents' => 'array',
            'sample_pages' => 'array',
            'seo_title' => 'array',
            'seo_description' => 'array',
            'status' => PublishStatus::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'regular_price' => 'integer',
            'discounted_price' => 'integer',
            'stock_quantity' => 'integer',
        ];
    }
}
