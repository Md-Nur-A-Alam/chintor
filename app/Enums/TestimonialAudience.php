<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * Who wrote a review. Parents and students get separate homepage sections:
 * parents trust other parents, students trust other students.
 */
enum TestimonialAudience: string
{
    use HasOptions;

    case Parent = 'parent';

    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Parent => 'অভিভাবক',
            self::Student => 'শিক্ষার্থী',
        };
    }

    /**
     * Badge tone used by <x-badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Parent => 'info',
            self::Student => 'success',
        };
    }
}
