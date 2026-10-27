<?php

namespace App\Http\Controllers\Admin;

use App\Models\PhotoGallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhotoGalleryController extends AdminController
{
    public function index(): View
    {
        return view('admin.photos.index', ['photos' => PhotoGallery::ordered()->paginate(40)]);
    }

    public function create(): View
    {
        return view('admin.photos.create');
    }

    /** Eyni anda bir neçə şəkil yükləmək */
    public function store(Request $request): RedirectResponse
    {
        $this->validateAz($request, [
            'images'   => 'required|array|min:1|max:30',
            'images.*' => self::IMAGE_RULE,
        ], ['images' => 'Şəkillər', 'images.*' => 'Şəkil']);

        $next = (int) PhotoGallery::max('sort_order') + 1;

        foreach ($request->file('images') as $file) {
            PhotoGallery::create([
                'image'      => $this->storeFile($file, 'gallery/photos'),
                'sort_order' => $next++,
                'is_active'  => true,
            ]);
        }

        $count = count($request->file('images'));

        return redirect()->route('admin.photos.index')->with('success', "{$count} şəkil yükləndi.");
    }

    public function edit(PhotoGallery $photo): View
    {
        return view('admin.photos.edit', compact('photo'));
    }

    public function update(Request $request, PhotoGallery $photo): RedirectResponse
    {
        $data = $this->validateAz($request, array_merge(
            $this->transRules('title', 'Şəkil altı yazı'),
            [
                'image'      => 'nullable|' . self::IMAGE_RULE,
                'sort_order' => 'nullable|integer|min:0',
                'is_active'  => 'boolean',
            ]
        ), ['image' => 'Şəkil', 'sort_order' => 'Sıra']);

        $photo->fill([
            'title'      => $this->cleanTrans($data['title'] ?? null),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active'),
        ]);

        // Şəkil mütləqdir — yalnız yenisi yüklənəndə dəyişir
        if ($request->hasFile('image')) {
            $this->deleteFile($photo->image);
            $photo->image = $this->storeFile($request->file('image'), 'gallery/photos');
        }

        $photo->save();

        return redirect()->route('admin.photos.index')->with('success', 'Şəkil yeniləndi.');
    }

    public function destroy(PhotoGallery $photo): RedirectResponse
    {
        $this->deleteFile($photo->image);
        $photo->delete();

        return redirect()->route('admin.photos.index')->with('success', 'Şəkil silindi.');
    }
}
