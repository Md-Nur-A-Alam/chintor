<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Str;

/**
 * A public ULID (for URLs) plus a short, human-friendly reference that is
 * easy to read out over the phone, e.g. "CH-O-2611-0042".
 *
 * The using model defines: protected const REFERENCE_CODE = 'O';
 */
trait HasReference
{
    use HasUlids;

    /**
     * Only the "ulid" column is generated; the primary key stays numeric.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    protected static function bootHasReference(): void
    {
        static::creating(function (self $model): void {
            // Temporary unique value; replaced with the id-based reference below.
            $model->reference ??= 'TMP-'.Str::ulid();
        });

        static::created(function (self $model): void {
            if (str_starts_with($model->reference, 'TMP-')) {
                $model->reference = sprintf(
                    'CH-%s-%s-%04d',
                    static::REFERENCE_CODE,
                    $model->created_at->format('ym'),
                    $model->getKey(),
                );
                $model->saveQuietly();
            }
        });
    }
}
