@extends('admin.layouts.app')

@section('title', 'Şəkli redaktə et')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.photos.index') }}">Şəkil qalereyası</a> /</div>
        <h1>Şəkli redaktə et</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.photos.update', $photo) }}">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="card"><div class="card-body">
            @include('admin.partials.trans', ['name' => 'title', 'label' => 'Şəkil altı yazı', 'value' => $photo->title])
            @include('admin.partials.input', ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'min' => 0, 'value' => $photo->sort_order])
            @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Saytda göstər', 'checked' => $photo->is_active])
        </div></div>

        <div class="card"><div class="card-body">
            @include('admin.partials.image', [
                'name' => 'image', 'label' => 'Şəkil', 'current' => $photo->image, 'required' => true,
                'help' => 'Dəyişmək üçün yeni şəkil seçin. JPG, PNG və ya WEBP, ən çox 4 MB.',
            ])
        </div></div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">Yadda saxla</button>
        <a href="{{ route('admin.photos.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection
