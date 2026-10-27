<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasTranslations, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'slug', 'title', 'excerpt', 'body', 'tag', 'image',
        'is_featured', 'is_published', 'published_at',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'title'            => 'array',
        'excerpt'          => 'array',
        'body'             => 'array',
        'tag'              => 'array',
        'meta_title'       => 'array',
        'meta_description' => 'array',
        'is_featured'      => 'boolean',
        'is_published'     => 'boolean',
        'published_at'     => 'datetime',
    ];

    /** Saytda görünən xəbərlər: dərc olunub və tarixi gəlib */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
