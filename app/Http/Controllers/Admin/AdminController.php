<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Admin controller-ləri üçün ortaq köməkçilər.
 */
abstract class AdminController extends Controller
{
    /** Validasiya xətalarında görünən sahə adları */
    protected array $attributeNames = [];

    /** Şəkil faylları üçün ortaq qayda (maks. 4 MB) */
    protected const IMAGE_RULE = 'image|mimes:jpg,jpeg,png,webp|max:4096';

    /**
     * Tərcüməli sahə üçün qaydalar: name.az (mütləq), name.en, name.ru (istəyə bağlı).
     */
    protected function transRules(string $field, string $label, bool $required = false, string $rules = 'string|max:255'): array
    {
        $result = [$field => 'nullable|array'];

        foreach (Locales::ALL as $code => $name) {
            $isRequired = $required && $code === Locales::DEFAULT;
            $result["{$field}.{$code}"] = ($isRequired ? 'required|' : 'nullable|') . $rules;
            $this->attributeNames["{$field}.{$code}"] = "{$label} ({$name})";
        }

        return $result;
    }

    /** Validasiya — xəta mesajları Azərbaycan dilində */
    protected function validateAz(Request $request, array $rules, array $attributes = []): array
    {
        return $request->validate($rules, $this->messages(), array_merge($this->attributeNames, $attributes));
    }

    protected function messages(): array
    {
        return [
            'required'     => ':attribute sahəsini doldurun.',
            'string'       => ':attribute mətn olmalıdır.',
            'array'        => ':attribute düzgün formatda deyil.',
            'integer'      => ':attribute tam ədəd olmalıdır.',
            'numeric'      => ':attribute rəqəm olmalıdır.',
            'boolean'      => ':attribute düzgün deyil.',
            'date'         => ':attribute düzgün tarix deyil.',
            'email'        => ':attribute düzgün e-poçt deyil.',
            'url'          => ':attribute düzgün link deyil (https:// ilə başlamalıdır).',
            'exists'       => 'Seçilmiş :attribute mövcud deyil.',
            'unique'       => 'Bu :attribute artıq istifadə olunur.',
            'image'        => ':attribute şəkil olmalıdır.',
            'file'         => ':attribute fayl olmalıdır.',
            'mimes'        => ':attribute bu formatlardan biri olmalıdır: :values.',
            'regex'        => ':attribute formatı düzgün deyil.',
            'alpha_dash'   => ':attribute yalnız hərf, rəqəm, "-" və "_" ola bilər.',
            // Ölçü qaydaları sahənin növünə görə massiv kimi verilir
            'min' => [
                'numeric' => ':attribute ən az :min olmalıdır.',
                'string'  => ':attribute ən az :min simvol olmalıdır.',
                'file'    => ':attribute ən az :min KB olmalıdır.',
                'array'   => ':attribute ən az :min element olmalıdır.',
            ],
            'max' => [
                'numeric' => ':attribute ən çox :max ola bilər.',
                'string'  => ':attribute ən çox :max simvol ola bilər.',
                'file'    => ':attribute ən çox :max KB ola bilər.',
                'array'   => ':attribute ən çox :max element ola bilər.',
            ],
            'uploaded'     => ':attribute yüklənmədi (fayl çox böyük ola bilər).',
        ];
    }

    /**
     * Tərcümə massivini təmizləyir: boş dilləri atır. Hamısı boşdursa null.
     */
    protected function cleanTrans(?array $value): ?array
    {
        if (! $value) {
            return null;
        }

        $clean = [];
        foreach (Locales::codes() as $code) {
            $text = isset($value[$code]) ? trim((string) $value[$code]) : '';
            if ($text !== '') {
                $clean[$code] = $text;
            }
        }

        return $clean ?: null;
    }

    /** Bir neçə tərcüməli sahəni birdən təmizləyir */
    protected function cleanTransFields(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            $data[$field] = $this->cleanTrans($data[$field] ?? null);
        }

        return $data;
    }

    /**
     * Şəkil yükləmə: yeni fayl gəlibsə köhnəni silir və yenisini saxlayır.
     * "remove_{field}" işarələnibsə, şəkli silir.
     * Qaytarır: yeni yol, köhnə yol və ya null.
     */
    protected function handleUpload(Request $request, string $field, string $dir, ?string $current = null): ?string
    {
        if ($request->hasFile($field)) {
            $this->deleteFile($current);

            return $this->storeFile($request->file($field), $dir);
        }

        if ($request->boolean("remove_{$field}")) {
            $this->deleteFile($current);

            return null;
        }

        return $current;
    }

    protected function storeFile(UploadedFile $file, string $dir): string
    {
        return $file->store($dir, 'public');
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Unikal slug: əl ilə yazılıbsa onu, yoxdursa AZ adından yaradır.
     * Eyni slug varsa sonuna -2, -3 ... əlavə edir.
     */
    protected function uniqueSlug(string $modelClass, ?string $manual, ?string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($manual ?: (string) $source, '-', 'az') ?: Str::lower(Str::random(8));
        $slug = $base;
        $i = 2;

        while ($this->slugExists($modelClass, $slug, $ignoreId)) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Tərcüməli JSON sahədə bütün dillərdə axtarış (böyük/kiçik hərf fərqi olmadan).
     */
    protected function whereTransLike(Builder $query, string $column, string $term): void
    {
        $like = '%' . mb_strtolower($term) . '%';

        $query->where(function (Builder $w) use ($column, $like) {
            foreach (Locales::codes() as $code) {
                $w->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(`{$column}`, '$.{$code}'))) LIKE ?", [$like]);
            }
        });
    }

    private function slugExists(string $modelClass, string $slug, ?int $ignoreId): bool
    {
        /** @var Model $model */
        $model = new $modelClass;
        $query = $model->newQuery()->where('slug', $slug);

        // Soft delete olunmuşlar da yoxlanmalıdır (unique index onları da sayır)
        if (method_exists($model, 'bootSoftDeletes')) {
            $query->withTrashed();
        }

        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }
}
