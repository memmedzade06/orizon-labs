<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends AdminController
{
    private const TRANS = ['name', 'description'];

    public function index(): View
    {
        $categories = Category::query()
            ->roots()
            ->withCount('products')
            ->with(['children' => fn ($q) => $q->withCount('products')->ordered()])
            ->ordered()
            ->get();

        $total = Category::count();

        return view('admin.categories.index', compact('categories', 'total'));
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category(['is_active' => true]),
            'parents'  => Category::roots()->ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = new Category();
        $this->save($request, $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kateqoriya əlavə olundu.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents'  => Category::roots()->where('id', '!=', $category->id)->ordered()->get(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->save($request, $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kateqoriya yeniləndi.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'Bu kateqoriyanın alt-kateqoriyaları var. Əvvəlcə onları silin və ya başqa kateqoriyaya köçürün.');
        }

        $count = $category->products()->count();

        if ($count > 0) {
            return back()->with('error', "Bu kateqoriyada {$count} məhsul var. Əvvəlcə məhsulları başqa kateqoriyaya köçürün və ya silin.");
        }

        // Əvvəllər silinmiş (soft delete) məhsullar hələ kateqoriyaya bağlıdır — onları birdəfəlik silirik
        DB::transaction(function () use ($category) {
            $category->products()->onlyTrashed()->with('images')->get()->each(function (Product $product) {
                $this->deleteFile($product->image);
                $product->images->each(fn ($img) => $this->deleteFile($img->path));
                $product->forceDelete();
            });

            $this->deleteFile($category->image);
            $category->delete();
        });

        return redirect()->route('admin.categories.index')->with('success', 'Kateqoriya silindi.');
    }

    private function save(Request $request, Category $category): void
    {
        $parentRule = function (string $attribute, mixed $value, \Closure $fail) use ($category) {
            if (!$value) {
                return;
            }
            if ($category->exists && (int) $value === (int) $category->id) {
                $fail('Kateqoriya özünə alt-kateqoriya ola bilməz.');
                return;
            }
            $parent = Category::find($value);
            if ($parent && $parent->parent_id !== null) {
                $fail('Yalnız əsas kateqoriya üst kateqoriya ola bilər (bir səviyyə).');
                return;
            }
            if ($category->exists && $category->children()->exists()) {
                $fail('Bu kateqoriyanın alt-kateqoriyaları var, ona görə özü alt-kateqoriya ola bilməz.');
            }
        };

        $data = $this->validateAz($request, array_merge(
            $this->transRules('name', 'Ad', true),
            $this->transRules('description', 'Təsvir', false, 'string|max:1000'),
            [
                'parent_id'  => ['nullable', 'integer', 'exists:categories,id', $parentRule],
                'slug'       => 'nullable|string|max:255|alpha_dash',
                'image'      => 'nullable|' . self::IMAGE_RULE,
                'sort_order' => 'nullable|integer|min:0',
                'is_active'  => 'boolean',
            ]
        ), ['parent_id' => 'Üst kateqoriya', 'slug' => 'Slug', 'image' => 'Şəkil', 'sort_order' => 'Sıra']);

        $data = $this->cleanTransFields($data, self::TRANS);

        $category->fill([
            'parent_id'   => $data['parent_id'] ?? null,
            'name'        => $data['name'],
            'description' => $data['description'],
            'slug'        => $this->uniqueSlug(Category::class, $data['slug'] ?? null, $data['name']['az'] ?? '', $category->id),
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => $request->boolean('is_active'),
            'image'       => $this->handleUpload($request, 'image', 'categories', $category->image),
        ])->save();
    }
}
