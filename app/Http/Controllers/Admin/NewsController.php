<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends AdminController
{
    private const TRANS = ['title', 'excerpt', 'body', 'tag', 'meta_title', 'meta_description'];

    public function index(Request $request): View
    {
        $news = News::query()
            ->when($request->filled('q'), fn ($q) => $this->whereTransLike($q, 'title', (string) $request->input('q')))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create(): View
    {
        return view('admin.news.form', ['item' => new News([
            'is_published' => true,
            'published_at' => now(),
        ])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $item = new News();
        $this->save($request, $item);

        return redirect()->route('admin.news.index')->with('success', 'Xəbər əlavə olundu.');
    }

    public function edit(News $news): View
    {
        return view('admin.news.form', ['item' => $news]);
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $this->save($request, $news);

        return redirect()->route('admin.news.index')->with('success', 'Xəbər yeniləndi.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Xəbər silindi.');
    }

    private function save(Request $request, News $item): void
    {
        $data = $this->validateAz($request, array_merge(
            $this->transRules('title', 'Başlıq', true),
            $this->transRules('excerpt', 'Qısa mətn', false, 'string|max:1000'),
            $this->transRules('body', 'Tam mətn', false, 'string|max:50000'),
            $this->transRules('tag', 'Teq', false, 'string|max:50'),
            $this->transRules('meta_title', 'SEO başlıq', false, 'string|max:255'),
            $this->transRules('meta_description', 'SEO təsvir', false, 'string|max:500'),
            [
                'slug'         => 'nullable|string|max:255|alpha_dash',
                'image'        => 'nullable|' . self::IMAGE_RULE,
                'published_at' => 'nullable|date',
                'is_published' => 'boolean',
                'is_featured'  => 'boolean',
            ]
        ), ['slug' => 'Slug', 'image' => 'Şəkil', 'published_at' => 'Dərc tarixi']);

        $data = $this->cleanTransFields($data, self::TRANS);

        $item->fill(array_merge(
            array_intersect_key($data, array_flip(self::TRANS)),
            [
                'slug'         => $this->uniqueSlug(News::class, $data['slug'] ?? null, $data['title']['az'] ?? '', $item->id),
                'published_at' => $data['published_at'] ?? now(),
                'is_published' => $request->boolean('is_published'),
                'is_featured'  => $request->boolean('is_featured'),
                'image'        => $this->handleUpload($request, 'image', 'news', $item->image),
            ]
        ))->save();
    }
}
