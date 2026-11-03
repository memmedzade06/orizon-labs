<?php

namespace App\Http\Controllers\Admin;

use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartnerController extends AdminController
{
    public function index(): View
    {
        return view('admin.partners.index', ['partners' => Partner::ordered()->paginate(60)]);
    }

    public function create(): View
    {
        return view('admin.partners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAz($request, [
            'name'       => 'nullable|string|max:255',
            'url'        => 'nullable|url|max:255',
            'logo'       => 'required|' . self::IMAGE_RULE,
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
        ], ['name' => 'Ad', 'url' => 'Veb-sayt', 'logo' => 'Loqo', 'sort_order' => 'Sıra']);

        Partner::create([
            'name'       => $data['name'] ?? null,
            'url'        => $data['url'] ?? null,
            'logo'       => $this->storeFile($request->file('logo'), 'partners'),
            'sort_order' => $data['sort_order'] ?? ((int) Partner::max('sort_order') + 1),
            'is_active'  => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.partners.index')->with('success', 'Tərəfdaş əlavə olundu.');
    }

    public function edit(Partner $partner): View
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner): RedirectResponse
    {
        $data = $this->validateAz($request, [
            'name'       => 'nullable|string|max:255',
            'url'        => 'nullable|url|max:255',
            'logo'       => 'nullable|' . self::IMAGE_RULE,
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
        ], ['name' => 'Ad', 'url' => 'Veb-sayt', 'logo' => 'Loqo', 'sort_order' => 'Sıra']);

        $partner->fill([
            'name'       => $data['name'] ?? null,
            'url'        => $data['url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active'),
        ]);

        // Loqo mütləqdir — yalnız yenisi yüklənəndə dəyişir
        if ($request->hasFile('logo')) {
            $this->deleteFile($partner->logo);
            $partner->logo = $this->storeFile($request->file('logo'), 'partners');
        }

        $partner->save();

        return redirect()->route('admin.partners.index')->with('success', 'Tərəfdaş yeniləndi.');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        $this->deleteFile($partner->logo);
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Tərəfdaş silindi.');
    }
}
