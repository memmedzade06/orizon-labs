@extends('admin.layouts.app')

@php $isEdit = $slide->exists; @endphp

@section('title', $isEdit ? 'Slaydı redaktə et' : 'Yeni slayd')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.slides.index') }}">Slayder</a> /</div>
        <h1>{{ $isEdit ? $slide->tr('title', 'az') : 'Yeni slayd' }}</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $isEdit ? route('admin.slides.update', $slide) : route('admin.slides.store') }}">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="form-grid">
        <div class="card"><div class="card-body">
            @include('admin.partials.trans', ['name' => 'eyebrow', 'label' => 'Üst yazı', 'value' => $slide->eyebrow, 'help' => 'Məs: İstehsalat · 1-ci mərhələ'])
            @include('admin.partials.trans', ['name' => 'title', 'label' => 'Başlıq', 'value' => $slide->title, 'required' => true])
            @include('admin.partials.trans', ['name' => 'text', 'label' => 'Mətn', 'value' => $slide->text, 'type' => 'textarea', 'rows' => 3])
            <div class="row-2">
                @include('admin.partials.trans', ['name' => 'button_text', 'label' => 'Düymə mətni', 'value' => $slide->button_text, 'help' => 'Məs: Məhsullara bax'])
                @include('admin.partials.input', ['name' => 'button_url', 'label' => 'Düymə linki', 'value' => $slide->button_url, 'placeholder' => '#products', 'help' => 'Boş qalsa düymə göstərilmir.'])
            </div>
        </div></div>

        <div>
            <div class="card"><div class="card-body">
                @include('admin.partials.image', ['name' => 'image', 'label' => 'Şəkil', 'current' => $slide->image, 'help' => 'Tövsiyə: 1600×700 px, JPG/WEBP, ən çox 4 MB.'])
            </div></div>
            <div class="card"><div class="card-body">
                @include('admin.partials.input', ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'min' => 0, 'value' => $slide->sort_order ?? 0])
                @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Saytda göstər', 'checked' => $slide->is_active])
            </div></div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">{{ $isEdit ? 'Yadda saxla' : 'Əlavə et' }}</button>
        <a href="{{ route('admin.slides.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection
