@extends('admin.layouts.app')

@php $isEdit = $product->exists; @endphp

@section('title', $isEdit ? 'Məhsulu redaktə et' : 'Yeni məhsul')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.products.index') }}">Məhsullar</a> /</div>
        <h1>{{ $isEdit ? $product->tr('name', 'az') : 'Yeni məhsul' }}</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="form-grid">
        <div>
            <div class="card"><div class="card-body">
                <h2 class="card-title">Əsas məlumat</h2>
                @include('admin.partials.trans', ['name' => 'name', 'label' => 'Ad', 'value' => $product->name, 'required' => true])
                <div class="row-2">
                    @include('admin.partials.trans', ['name' => 'dosage_form', 'label' => 'Forma', 'value' => $product->dosage_form, 'help' => 'Məs: Kapsul, Tablet, Krem'])
                    @include('admin.partials.trans', ['name' => 'package_size', 'label' => 'Həcm / say', 'value' => $product->package_size, 'help' => 'Məs: 60 kapsul, 50 ml'])
                </div>
                @include('admin.partials.trans', ['name' => 'short_description', 'label' => 'Qısa təsvir', 'value' => $product->short_description, 'type' => 'textarea', 'rows' => 2])
                @include('admin.partials.trans', ['name' => 'description', 'label' => 'Ətraflı təsvir', 'value' => $product->description, 'type' => 'textarea', 'rows' => 6])
            </div></div>

            <div class="card"><div class="card-body">
                <h2 class="card-title">Tərkib və istifadə</h2>
                @include('admin.partials.trans', ['name' => 'composition', 'label' => 'Tərkibi', 'value' => $product->composition, 'type' => 'textarea', 'rows' => 4])
                @include('admin.partials.trans', ['name' => 'usage', 'label' => 'İstifadə qaydası', 'value' => $product->usage, 'type' => 'textarea', 'rows' => 4])
                @include('admin.partials.trans', ['name' => 'warnings', 'label' => 'Əks göstərişlər / xəbərdarlıq', 'value' => $product->warnings, 'type' => 'textarea', 'rows' => 3])
            </div></div>

            <div class="card"><div class="card-body">
                @php
                    $maxExtra  = \App\Http\Controllers\Admin\ProductController::MAX_EXTRA_IMAGES;
                    $extraUsed = $isEdit ? $product->images->count() : 0;
                    $extraLeft = max(0, $maxExtra - $extraUsed);
                @endphp
                <h2 class="card-title">Əlavə şəkillər <span class="muted small">({{ $extraUsed }} / {{ $maxExtra }})</span></h2>
                <p class="help" style="margin:-8px 0 12px">Məhsul səhifəsində əsas şəkillə birlikdə göstərilir. Cəmi 3 şəkil: 1 əsas + {{ $maxExtra }} əlavə.</p>

                @if ($extraUsed > 0)
                    <div class="gallery-grid">
                        @foreach ($product->images as $img)
                            <div class="item">
                                <img src="{{ Storage::disk('public')->url($img->path) }}" alt="">
                                <button type="submit" class="x" form="del-img-{{ $img->id }}" title="Şəkli sil" aria-label="Şəkli sil">×</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($extraLeft > 0)
                    <div class="field">
                        <label for="f_gallery">Şəkil əlavə et</label>
                        <input id="f_gallery" type="file" name="gallery[]" accept="image/jpeg,image/png,image/webp"
                               @if ($extraLeft > 1) multiple @endif data-max="{{ $extraLeft }}"
                               class="@error('gallery') is-invalid @enderror @error('gallery.*') is-invalid @enderror">
                        <div class="help">Daha {{ $extraLeft }} şəkil əlavə edə bilərsiniz (hər biri ən çox 4 MB).</div>
                    </div>
                @else
                    <p class="help">Limit dolub. Yeni şəkil üçün mövcud şəkillərdən birini silin (× düyməsi).</p>
                @endif

                @error('gallery') <div class="error-text">{{ $message }}</div> @enderror
                @foreach ($errors->get('gallery.*') as $msgs)
                    <div class="error-text">{{ $msgs[0] }}</div>
                @endforeach
            </div></div>

            <div class="card"><div class="card-body">
                <h2 class="card-title">SEO</h2>
                @include('admin.partials.trans', ['name' => 'meta_title', 'label' => 'SEO başlıq', 'value' => $product->meta_title, 'help' => 'Boş qalsa məhsulun adı istifadə olunur.'])
                @include('admin.partials.trans', ['name' => 'meta_description', 'label' => 'SEO təsvir', 'value' => $product->meta_description, 'type' => 'textarea', 'rows' => 2])
            </div></div>
        </div>

        <div>
            <div class="card"><div class="card-body">
                <div class="field">
                    <label for="f_category_id">Kateqoriya <span class="req">*</span></label>
                    <select id="f_category_id" name="category_id" class="@error('category_id') is-invalid @enderror">
                        <option value="">— Seçin —</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->tr('name', 'az') }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Saytda göstər', 'checked' => $product->is_active])
                @include('admin.partials.checkbox', ['name' => 'is_new', 'label' => '"Yeni" nişanı', 'checked' => $product->is_new])
                @include('admin.partials.checkbox', ['name' => 'is_featured', 'label' => '"Ən çox seçilən" nişanı', 'checked' => $product->is_featured])
            </div></div>

            <div class="card"><div class="card-body">
                @include('admin.partials.image', ['name' => 'image', 'label' => 'Əsas şəkil', 'current' => $product->image])
            </div></div>

            <div class="card"><div class="card-body">
                @include('admin.partials.input', ['name' => 'sku', 'label' => 'Məhsul kodu (SKU)', 'value' => $product->sku])
                @include('admin.partials.input', ['name' => 'slug', 'label' => 'Slug (URL)', 'value' => $product->slug, 'help' => 'Boş qalsa AZ adından yaranır.'])
                @include('admin.partials.input', ['name' => 'price', 'label' => 'Qiymət (AZN)', 'type' => 'number', 'step' => '0.01', 'min' => 0, 'value' => $product->price, 'help' => 'İstəyə bağlıdır.'])
                <div class="row-2">
                    @include('admin.partials.input', ['name' => 'popularity', 'label' => 'Populyarlıq', 'type' => 'number', 'min' => 0, 'value' => $product->popularity ?? 0])
                    @include('admin.partials.input', ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'min' => 0, 'value' => $product->sort_order ?? 0])
                </div>
                <div class="help">Populyarlıq: böyük rəqəm "Populyarlığa görə" sıralamada yuxarıda olur.</div>
            </div></div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">{{ $isEdit ? 'Yadda saxla' : 'Əlavə et' }}</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>

{{-- Qalereya şəkillərini silmək üçün ayrıca formalar (iç-içə forma olmasın deyə) --}}
@if ($isEdit)
    @foreach ($product->images as $img)
        <form id="del-img-{{ $img->id }}" method="POST" action="{{ route('admin.product-images.destroy', $img) }}"
              data-confirm="Bu şəkil silinsin?" hidden>
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif
@endsection

@push('scripts')
<script>
    // Brauzerdə də limiti yoxla (server yenə yoxlayır)
    (function () {
        var input = document.getElementById('f_gallery');
        if (!input) return;
        input.addEventListener('change', function () {
            var max = parseInt(input.dataset.max, 10);
            if (input.files.length > max) {
                window.adminModal.alert('Ən çox ' + max + ' şəkil seçə bilərsiniz. Cəmi 3 şəkil: 1 əsas + 2 əlavə.', 'Şəkil limiti');
                input.value = '';
            }
        });
    })();
</script>
@endpush
