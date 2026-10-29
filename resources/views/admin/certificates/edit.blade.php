@extends('admin.layouts.app')

@section('title', 'Sertifikatı redaktə et')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.certificates.index') }}">Sertifikatlar</a> /</div>
        <h1>Sertifikatı redaktə et</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.certificates.update', $certificate) }}">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="card"><div class="card-body">
            @include('admin.partials.trans', ['name' => 'title', 'label' => 'Ad', 'value' => $certificate->title])
            @include('admin.partials.input', ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'min' => 0, 'value' => $certificate->sort_order])
            @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Saytda göstər', 'checked' => $certificate->is_active])
        </div></div>

        <div class="card"><div class="card-body">
            @include('admin.partials.image', [
                'name' => 'image', 'label' => 'Sertifikat şəkli', 'current' => $certificate->image, 'required' => true,
                'help' => 'Dəyişmək üçün yeni şəkil seçin. JPG, PNG və ya WEBP, ən çox 4 MB.',
            ])
        </div></div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">Yadda saxla</button>
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection
