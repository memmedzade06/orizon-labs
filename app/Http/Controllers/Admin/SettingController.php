<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends AdminController
{
    /**
     * Formadakı ayarlar. Yeni ayar lazım olsa, yalnız bura əlavə olunur.
     * type: text | email | url | number | trans | textarea_trans | file
     */
    public const FIELDS = [
        'Əlaqə' => [
            'contact.phone'    => ['label' => 'Telefon', 'type' => 'text', 'placeholder' => '+994 55 265 15 22'],
            'contact.whatsapp' => ['label' => 'WhatsApp nömrəsi', 'type' => 'text', 'placeholder' => '994552651522', 'help' => 'Yalnız rəqəmlər, + işarəsi olmadan.'],
            'contact.email'    => ['label' => 'E-poçt', 'type' => 'email', 'placeholder' => 'info@orizon-labs.com'],
            'contact.address'  => ['label' => 'Ünvan', 'type' => 'trans'],
        ],
        'Haqqımızda rəqəmləri' => [
            'about.founded_year'     => ['label' => 'Fəaliyyətə başladığı il', 'type' => 'number', 'placeholder' => '2019'],
            'about.countries_count'  => ['label' => 'İxrac olunan ölkə sayı', 'type' => 'number', 'placeholder' => '5'],
            'about.partners_count'   => ['label' => 'Tərəfdaş sayı', 'type' => 'text', 'placeholder' => '70+'],
            'about.export_countries' => ['label' => 'İxrac ölkələri', 'type' => 'trans', 'help' => 'Vergüllə ayırın: Türkiyə, Gürcüstan, Polşa'],
        ],
        'Kataloq' => [
            'catalog.pdf' => ['label' => 'Məhsul kataloqu (PDF)', 'type' => 'file', 'help' => 'PDF, ən çox 20 MB.'],
        ],
        'Sosial şəbəkələr' => [
            'social.instagram' => ['label' => 'Instagram', 'type' => 'url', 'placeholder' => 'https://instagram.com/...'],
            'social.facebook'  => ['label' => 'Facebook', 'type' => 'url', 'placeholder' => 'https://facebook.com/...'],
            'social.linkedin'  => ['label' => 'LinkedIn', 'type' => 'url', 'placeholder' => 'https://linkedin.com/company/...'],
        ],
    ];

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'groups' => self::FIELDS,
            'values' => Setting::allValues(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];
        $names = [];

        foreach (self::FIELDS as $fields) {
            foreach ($fields as $key => $f) {
                $input = $this->inputName($key);
                $names[$input] = $f['label'];

                $rules = array_merge($rules, match ($f['type']) {
                    'trans'  => $this->transRules($input, $f['label'], false, 'string|max:1000'),
                    'email'  => [$input => 'nullable|email|max:255'],
                    'url'    => [$input => 'nullable|url|max:255'],
                    'number' => [$input => 'nullable|integer|min:0|max:100000'],
                    'file'   => [$input => 'nullable|file|mimes:pdf|max:20480'],
                    default  => [$input => 'nullable|string|max:255'],
                });
            }
        }

        $data = $this->validateAz($request, $rules, $names);

        foreach (self::FIELDS as $fields) {
            foreach ($fields as $key => $f) {
                $input = $this->inputName($key);
                $group = strtok($key, '.');

                if ($f['type'] === 'file') {
                    $current = Setting::raw($key);
                    $path = $this->handleUpload($request, $input, 'settings', $current);
                    if ($path !== $current) {
                        Setting::set($key, $path, $group);
                    }
                    continue;
                }

                $value = $f['type'] === 'trans'
                    ? $this->cleanTrans($data[$input] ?? null)
                    : (isset($data[$input]) && $data[$input] !== '' ? trim((string) $data[$input]) : null);

                Setting::set($key, $value, $group);
            }
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Ayarlar yadda saxlanıldı.');
    }

    /** "contact.phone" → "contact_phone" (formada nöqtə massiv kimi oxunur) */
    public static function inputName(string $key): string
    {
        return str_replace('.', '_', $key);
    }

    public static function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
