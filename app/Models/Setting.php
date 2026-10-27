<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Sayt ayarları (açar => dəyər).
 *
 * Setting::get('contact.phone')
 * Setting::get('contact.address')     // tərcüməlidirsə, cari dildəki mətn
 * Setting::set('contact.phone', '+994 ...', 'contact')
 */
class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    private const CACHE_KEY = 'settings.all';

    /** Bütün ayarlar [key => value], keşdən */
    public static function allValues(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            // Eloquent pluck 'array' cast-ı tətbiq edir — dəyərlər artıq decode olunub
            return static::query()->pluck('value', 'key')->all();
        });
    }

    /** Xam dəyər (tərcüməli sahə üçün massiv qaytarır) */
    public static function raw(string $key, mixed $default = null): mixed
    {
        return static::allValues()[$key] ?? $default;
    }

    /** Göstərmək üçün dəyər: tərcüməli sahədə cari dil, yoxdursa AZ */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::raw($key, $default);

        if (is_array($value) && array_key_exists(Locales::DEFAULT, $value)) {
            return $value[app()->getLocale()] ?? $value[Locales::DEFAULT] ?? $default;
        }

        return $value;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget(self::CACHE_KEY);
    }
}
