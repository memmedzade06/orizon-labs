<?php

namespace App\Support;

/**
 * Saytın dilləri. Yeni dil lazım olsa, yalnız bura əlavə olunur.
 */
class Locales
{
    /** Əsas dil — bu dildə mətn mütləqdir */
    public const DEFAULT = 'az';

    /** kod => admin paneldə görünən ad */
    public const ALL = [
        'az' => 'AZ',
        'en' => 'EN',
        'ru' => 'RU',
    ];

    public static function codes(): array
    {
        return array_keys(self::ALL);
    }
}
