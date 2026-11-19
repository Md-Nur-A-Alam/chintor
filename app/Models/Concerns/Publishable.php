<?php

namespace App\Models\Concerns;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Shared "status" behaviour for catalog and content models.
 * Requires a `status` column cast to PublishStatus.
 */
trait Publishable
{
    /**
     * Only items visitors may see.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where($this->qualifyColumn('status'), PublishStatus::Published);
    }

    public function isPublished(): bool
    {
        return $this->status === PublishStatus::Published;
    }
}
