<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Models\Concerns\HasTranslations;
use Database\Factories\PaymentAccountFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A number or bank account customers send money to. Shown at checkout,
 * so changing a number never needs a deploy.
 */
class PaymentAccount extends Model
{
    /** @use HasFactory<PaymentAccountFactory> */
    use HasFactory, HasTranslations;

    /**
     * @var array<string, string>
     */
    public const ACCOUNT_TYPES = [
        'personal' => 'পার্সোনাল (Send Money)',
        'merchant' => 'মার্চেন্ট (Payment)',
        'agent' => 'এজেন্ট (Cash In)',
        'bank' => 'ব্যাংক অ্যাকাউন্ট',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'method',
        'account_type',
        'account_name',
        'account_number',
        'bank_name',
        'branch',
        'instructions',
        'is_active',
        'sort_order',
    ];

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'instructions' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
