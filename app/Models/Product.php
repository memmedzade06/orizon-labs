<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'category_id', 'slug', 'sku',
        'name', 'dosage_form', 'package_size', 'short_description', 'description',
        'composition', 'usage', 'warnings',
        'image', 'price', 'popularity', 'is_new', 'is_featured', 'is_active', 'sort_order',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'name'              => 'array',
        'dosage_form'       => 'array',
        'package_size'      => 'array',
        'short_description' => 'array',
        'description'       => 'array',
        'composition'       => 'array',
        'usage'             => 'array',
        'warnings'          => 'array',
        'meta_title'        => 'array',
        'meta_description'  => 'array',
        'price'             => 'decimal:2',
        'popularity'        => 'integer',
        'sort_order'        => 'integer',
        'is_new'            => 'boolean',
        'is_featured'       => 'boolean',
        'is_active'         => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
