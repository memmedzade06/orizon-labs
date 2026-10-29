@extends('admin.layouts.app')

@section('title', 'Sertifikat yüklə')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.certificates.index') }}">Sertifikatlar</a> /</div>
        <h1>Sertifikat yüklə</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.certificates.store') }}">
    @csrf
    <div class="card"><div class="card-body">
        <div class="field">
            <label for="f_images">Sertifikatlar <span class="req">*</span></label>
            <input id="f_images" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required
                   class="@error('images') is-invalid @enderror">
            @error('images') <div class="error-text">{{ $message }}</div> @enderror
            @foreach ($errors->get('images.*') as $msgs)
                <div class="error-text">{{ $msgs[0] }}</div>
            @endforeach
            <div class="help">Bir neçə sertifikatı birdən seçə bilərsiniz (ən çox 30, hər biri 4 MB). Adları sonra "Redaktə" ilə əlavə edə bilərsiniz.</div>
        </div>
        <div class="gallery-grid" id="selected-preview"></div>
    </div></div>

    <div class="form-actions">
        <button type="submit" class="btn">Yüklə</button>
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('f_images').addEventListener('change', function () {
        var box = document.getElementById('selected-preview');
        box.innerHTML = '';
        Array.prototype.forEach.call(this.files, function (file) {
            var div = document.createElement('div');
            div.className = 'item';
            var img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = '';
            div.appendChild(img);
            box.appendChild(div);
        });
    });
</script>
@endpush
