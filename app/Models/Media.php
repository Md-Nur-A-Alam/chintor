<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    public const ProviderLocal = 'local';

    public const ProviderImgbb = 'imgbb';

    public const ProviderR2 = 'r2';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'provider',
        'path',
        'url',
        'delete_url',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'alt',
        'collection',
        'sort_order',
        'uploaded_by',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    protected function casts(): array
    {
        return [
            'alt' => 'array',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }
}
