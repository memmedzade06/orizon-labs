<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlideController extends AdminController
{
    private const TRANS = ['eyebrow', 'title', 'text', 'button_text'];

    public function index(): View
    {
        return view('admin.slides.index', ['slides' => Slide::ordered()->get()]);
    }

    public function create(): View
    {
        $next = (int) Slide::max('sort_order') + 1;

        return view('admin.slides.form', ['slide' => new Slide(['is_active' => true, 'sort_order' => $next])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->save($request, new Slide());

        return redirect()->route('admin.slides.index')->with('success', 'Slayd əlavə olundu.');
    }

    public function edit(Slide $slide): View
    {
        return view('admin.slides.form', compact('slide'));
    }

    public function update(Request $request, Slide $slide): RedirectResponse
    {
        $this->save($request, $slide);

        return redirect()->route('admin.slides.index')->with('success', 'Slayd yeniləndi.');
    }

    public function destroy(Slide $slide): RedirectResponse
    {
        $this->deleteFile($slide->image);
        $slide->delete();

        return redirect()->route('admin.slides.index')->with('success', 'Slayd silindi.');
    }

    private function save(Request $request, Slide $slide): void
    {
        $data = $this->validateAz($request, array_merge(
            $this->transRules('eyebrow', 'Üst yazı', false, 'string|max:100'),
            $this->transRules('title', 'Başlıq', true),
            $this->transRules('text', 'Mətn', false, 'string|max:1000'),
            $this->transRules('button_text', 'Düymə mətni', false, 'string|max:50'),
            [
                'button_url' => 'nullable|string|max:255',
                'image'      => 'nullable|' . self::IMAGE_RULE,
                'sort_order' => 'nullable|integer|min:0',
                'is_active'  => 'boolean',
            ]
        ), ['button_url' => 'Düymə linki', 'image' => 'Şəkil', 'sort_order' => 'Sıra']);

        $data = $this->cleanTransFields($data, self::TRANS);

        $slide->fill(array_merge(
            array_intersect_key($data, array_flip(self::TRANS)),
            [
                'button_url' => $data['button_url'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active'  => $request->boolean('is_active'),
                'image'      => $this->handleUpload($request, 'image', 'slides', $slide->image),
            ]
        ))->save();
    }
}
