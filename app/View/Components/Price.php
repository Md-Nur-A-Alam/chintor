<?php

namespace App\View\Components;

use App\Support\Money;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Shared price display for courses and books.
 *
 * When a valid discounted price exists, the regular price is struck through
 * and muted while the discounted price is highlighted. Otherwise only the
 * regular price is shown.
 */
class Price extends Component
{
    public function __construct(
        public int $regular,
        public ?int $discounted = null,
        public string $size = 'md',
        public bool $showSaving = true,
    ) {}

    public function hasDiscount(): bool
    {
        return $this->discounted !== null
            && $this->discounted >= 0
            && $this->discounted < $this->regular;
    }

    public function payable(): int
    {
        return $this->hasDiscount() ? $this->discounted : $this->regular;
    }

    public function discountPercent(): int
    {
        return $this->hasDiscount() ? Money::discountPercent($this->regular, $this->discounted) : 0;
    }

    public function render(): View|Closure|string
    {
        return view('components.price');
    }
}
