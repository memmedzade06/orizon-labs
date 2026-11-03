<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';

    protected $fillable = ['name', 'logo', 'url', 'sort_order', 'is_active'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }
}
