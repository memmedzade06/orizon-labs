{{--
    Şəkil yükləmə sahəsi
    @include('admin.partials.image', ['name' => 'image', 'label' => 'Şəkil', 'current' => $model->image ?? null, 'required' => false])
--}}
@php
    $required  = $required ?? false;
    $previewId = 'preview_' . $name;
    $currentUrl = ! empty($current) ? \Illuminate\Support\Facades\Storage::disk('public')->url($current) : null;
@endphp
<div class="field">
    <label for="f_{{ $name }}">{{ $label }} @if ($required)<span class="req">*</span>@endif</label>

    <div class="image-preview">
        <img id="{{ $previewId }}" src="{{ $currentUrl }}" alt="" @unless($currentUrl) hidden @endunless>
    </div>

    <input id="f_{{ $name }}" type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp"
           data-preview="{{ $previewId }}" class="@error($name) is-invalid @enderror">
    @error($name) <div class="error-text">{{ $message }}</div> @enderror
    <div class="help">{{ $help ?? 'JPG, PNG və ya WEBP, ən çox 4 MB.' }}</div>

    @if ($currentUrl && ! $required)
        <label class="check" style="margin-top:8px">
            <input type="checkbox" name="remove_{{ $name }}" value="1"> Şəkli sil
        </label>
    @endif
</div>
