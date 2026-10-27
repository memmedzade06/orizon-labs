<?php

namespace App\Http\Controllers\Admin;

use App\Models\VideoGallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoGalleryController extends AdminController
{
    public function index(): View
    {
        return view('admin.videos.index', ['videos' => VideoGallery::ordered()->paginate(30)]);
    }

    public function create(): View
    {
        $next = (int) VideoGallery::max('sort_order') + 1;

        return view('admin.videos.form', ['video' => new VideoGallery(['is_active' => true, 'sort_order' => $next])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->save($request, new VideoGallery());

        return redirect()->route('admin.videos.index')->with('success', 'Video əlavə olundu.');
    }

    public function edit(VideoGallery $video): View
    {
        return view('admin.videos.form', compact('video'));
    }

    public function update(Request $request, VideoGallery $video): RedirectResponse
    {
        $this->save($request, $video);

        return redirect()->route('admin.videos.index')->with('success', 'Video yeniləndi.');
    }

    public function destroy(VideoGallery $video): RedirectResponse
    {
        $this->deleteFile($video->thumbnail);
        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Video silindi.');
    }

    private function save(Request $request, VideoGallery $video): void
    {
        $data = $this->validateAz($request, array_merge(
            $this->transRules('title', 'Başlıq', true),
            [
                'video_url'  => ['required', 'url', 'max:255', 'regex:~(youtube\.com|youtu\.be|vimeo\.com)~i'],
                'thumbnail'  => 'nullable|' . self::IMAGE_RULE,
                'sort_order' => 'nullable|integer|min:0',
                'is_active'  => 'boolean',
            ]
        ), ['video_url' => 'Video linki', 'thumbnail' => 'Önizləmə şəkli', 'sort_order' => 'Sıra']);

        $video->fill([
            'title'      => $this->cleanTrans($data['title'] ?? null),
            'video_url'  => $data['video_url'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active'),
            'thumbnail'  => $this->handleUpload($request, 'thumbnail', 'gallery/videos', $video->thumbnail),
        ])->save();
    }
}
