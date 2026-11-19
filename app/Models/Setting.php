<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'secrets',
        'updated_by',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'secrets',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
            'secrets' => 'encrypted:array',
        ];
    }
}
