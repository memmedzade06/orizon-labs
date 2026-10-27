<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasTranslations;

    protected $fillable = [
        'eyebrow', 'title', 'text', 'image', 'button_text', 'button_url', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'eyebrow'     => 'array',
        'title'       => 'array',
        'text'        => 'array',
        'button_text' => 'array',
        'sort_order'  => 'integer',
        'is_active'   => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
