<?php

namespace App\Models\Concerns;

use App\Support\Locales;

/**
 * JSON sahələrdə saxlanan tərcümələri oxumaq üçün.
 * Sahə {"az": "...", "en": "...", "ru": "..."} formatındadır və modeldə 'array' kimi cast olunur.
 *
 * İstifadə: $product->tr('name')          // cari dil, yoxdursa AZ
 *           $product->tr('name', 'ru')    // konkret dil
 */
trait HasTranslations
{
    public function tr(string $field, ?string $locale = null): ?string
    {
        $value = $this->getAttribute($field);

        if (! is_array($value)) {
            return $value;
        }

        $locale ??= app()->getLocale();

        return $value[$locale]
            ?? $value[Locales::DEFAULT]
            ?? null;
    }
}
