<?php

namespace App\Http\Controllers\Admin;

use App\Models\Certificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends AdminController
{
    public function index(): View
    {
        return view('admin.certificates.index', ['certificates' => Certificate::ordered()->paginate(40)]);
    }

    public function create(): View
    {
        return view('admin.certificates.create');
    }

    /** Eyni anda bir neçə sertifikat yükləmək */
    public function store(Request $request): RedirectResponse
    {
        $this->validateAz($request, [
            'images'   => 'required|array|min:1|max:30',
            'images.*' => self::IMAGE_RULE,
        ], ['images' => 'Sertifikatlar', 'images.*' => 'Sertifikat']);

        $next = (int) Certificate::max('sort_order') + 1;

        foreach ($request->file('images') as $file) {
            Certificate::create([
                'image'      => $this->storeFile($file, 'certificates'),
                'sort_order' => $next++,
                'is_active'  => true,
            ]);
        }

        $count = count($request->file('images'));

        return redirect()->route('admin.certificates.index')->with('success', "{$count} sertifikat yükləndi.");
    }

    public function edit(Certificate $certificate): View
    {
        return view('admin.certificates.edit', compact('certificate'));
    }

    public function update(Request $request, Certificate $certificate): RedirectResponse
    {
        $data = $this->validateAz($request, array_merge(
            $this->transRules('title', 'Ad'),
            [
                'image'      => 'nullable|' . self::IMAGE_RULE,
                'sort_order' => 'nullable|integer|min:0',
                'is_active'  => 'boolean',
            ]
        ), ['image' => 'Sertifikat', 'sort_order' => 'Sıra']);

        $certificate->fill([
            'title'      => $this->cleanTrans($data['title'] ?? null),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active'),
        ]);

        // Şəkil mütləqdir — yalnız yenisi yüklənəndə dəyişir
        if ($request->hasFile('image')) {
            $this->deleteFile($certificate->image);
            $certificate->image = $this->storeFile($request->file('image'), 'certificates');
        }

        $certificate->save();

        return redirect()->route('admin.certificates.index')->with('success', 'Sertifikat yeniləndi.');
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        $this->deleteFile($certificate->image);
        $certificate->delete();

        return redirect()->route('admin.certificates.index')->with('success', 'Sertifikat silindi.');
    }
}
