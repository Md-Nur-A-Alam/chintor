<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Publication state of catalog and content items.
 */
enum PublishStatus: string
{
    use HasOptions;

    case Draft = 'draft';

    case Published = 'published';

    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'খসড়া',
            self::Published => 'প্রকাশিত',
            self::Archived => 'আর্কাইভ',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Draft => 'neutral',
            self::Published => 'success',
            self::Archived => 'warning',
        };
    }
}
