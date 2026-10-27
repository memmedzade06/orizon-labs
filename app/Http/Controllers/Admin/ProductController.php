<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends AdminController
{
    /** Əsas şəkildən əlavə ən çox neçə şəkil (cəmi 3 = 1 əsas + 2 əlavə) */
    public const MAX_EXTRA_IMAGES = 2;

    private const TRANS = [
        'name', 'dosage_form', 'package_size', 'short_description', 'description',
        'composition', 'usage', 'warnings', 'meta_title', 'meta_description',
    ];

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = (string) $request->input('q');
                $q->where(function ($w) use ($term) {
                    $this->whereTransLike($w, 'name', $term);
                    $w->orWhere('sku', 'like', '%' . $term . '%');
                });
            })
            ->when($request->input('status') === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->input('status') === 'hidden', fn ($q) => $q->where('is_active', false))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product'    => new Product(['is_active' => true]),
            'categories' => Category::ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = new Product();
        $this->save($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Məhsul əlavə olundu.');
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('admin.products.form', [
            'product'    => $product,
            'categories' => Category::ordered()->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->save($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Məhsul yeniləndi.');
    }

    /** Soft delete — məlumat bazada qalır, şəkillər silinmir (bərpa üçün) */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Məhsul silindi.');
    }

    /** Qalereyadan bir şəkli silmək */
    public function destroyImage(ProductImage $productImage): RedirectResponse
    {
        $product = $productImage->product;
        $this->deleteFile($productImage->path);
        $productImage->delete();

        return redirect()->route('admin.products.edit', $product)->with('success', 'Şəkil silindi.');
    }

    private function save(Request $request, Product $product): void
    {
        $text = 'string|max:10000';

        // Əlavə şəkil limiti: mövcud + yeni yüklənən ≤ MAX_EXTRA_IMAGES
        $existingImages = $product->exists ? $product->images()->count() : 0;
        $allowedImages  = max(0, self::MAX_EXTRA_IMAGES - $existingImages);
        $galleryLimit   = function (string $attribute, mixed $value, \Closure $fail) use ($existingImages, $allowedImages) {
            if (is_array($value) && count($value) > $allowedImages) {
                $fail($allowedImages === 0
                    ? 'Artıq ' . self::MAX_EXTRA_IMAGES . ' əlavə şəkil var. Yenisini yükləmək üçün əvvəlcə birini silin.'
                    : "Ən çox {$allowedImages} əlavə şəkil yükləyə bilərsiniz (hazırda {$existingImages} var, limit " . self::MAX_EXTRA_IMAGES . ').');
            }
        };

        $data = $this->validateAz($request, array_merge(
            $this->transRules('name', 'Ad', true),
            $this->transRules('dosage_form', 'Forma', false, 'string|max:100'),
            $this->transRules('package_size', 'Həcm / say', false, 'string|max:100'),
            $this->transRules('short_description', 'Qısa təsvir', false, 'string|max:500'),
            $this->transRules('description', 'Ətraflı təsvir', false, $text),
            $this->transRules('composition', 'Tərkibi', false, $text),
            $this->transRules('usage', 'İstifadə qaydası', false, $text),
            $this->transRules('warnings', 'Xəbərdarlıq', false, $text),
            $this->transRules('meta_title', 'SEO başlıq', false, 'string|max:255'),
            $this->transRules('meta_description', 'SEO təsvir', false, 'string|max:500'),
            [
                'category_id' => 'required|integer|exists:categories,id',
                'slug'        => 'nullable|string|max:255|alpha_dash',
                'sku'         => 'nullable|string|max:50|unique:products,sku,' . ($product->id ?: 'NULL'),
                'image'       => 'nullable|' . self::IMAGE_RULE,
                'gallery'     => ['nullable', 'array', $galleryLimit],
                'gallery.*'   => self::IMAGE_RULE,
                'price'       => 'nullable|numeric|min:0|max:99999999',
                'popularity'  => 'nullable|integer|min:0',
                'sort_order'  => 'nullable|integer|min:0',
                'is_new'      => 'boolean',
                'is_featured' => 'boolean',
                'is_active'   => 'boolean',
            ]
        ), [
            'category_id' => 'Kateqoriya', 'slug' => 'Slug', 'sku' => 'Məhsul kodu', 'image' => 'Əsas şəkil',
            'gallery' => 'Əlavə şəkillər', 'gallery.*' => 'Əlavə şəkil', 'price' => 'Qiymət',
            'popularity' => 'Populyarlıq', 'sort_order' => 'Sıra',
        ]);

        $data = $this->cleanTransFields($data, self::TRANS);

        $product->fill(array_merge(
            array_intersect_key($data, array_flip(self::TRANS)),
            [
                'category_id' => $data['category_id'],
                'slug'        => $this->uniqueSlug(Product::class, $data['slug'] ?? null, $data['name']['az'] ?? '', $product->id),
                'sku'         => $data['sku'] ?? null,
                'price'       => $data['price'] ?? null,
                'popularity'  => $data['popularity'] ?? 0,
                'sort_order'  => $data['sort_order'] ?? 0,
                'is_new'      => $request->boolean('is_new'),
                'is_featured' => $request->boolean('is_featured'),
                'is_active'   => $request->boolean('is_active'),
                'image'       => $this->handleUpload($request, 'image', 'products', $product->image),
            ]
        ))->save();

        // Qalereya şəkilləri
        if ($request->hasFile('gallery')) {
            $next = (int) $product->images()->max('sort_order') + 1;

            foreach ($request->file('gallery') as $file) {
                $product->images()->create([
                    'path'       => $this->storeFile($file, 'products/gallery'),
                    'sort_order' => $next++,
                ]);
            }
        }
    }
}
