@extends('admin.layouts.app')

@php $isEdit = $category->exists; @endphp

@section('title', $isEdit ? 'Kateqoriyanı redaktə et' : 'Yeni kateqoriya')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.categories.index') }}">Kateqoriyalar</a> /</div>
        <h1>{{ $isEdit ? $category->tr('name', 'az') : 'Yeni kateqoriya' }}</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="form-grid">
        <div class="card"><div class="card-body">
            @include('admin.partials.trans', ['name' => 'name', 'label' => 'Ad', 'value' => $category->name, 'required' => true])
            @include('admin.partials.trans', ['name' => 'description', 'label' => 'Qısa təsvir', 'value' => $category->description, 'type' => 'textarea', 'rows' => 3])
            @include('admin.partials.input', ['name' => 'slug', 'label' => 'Slug (URL)', 'value' => $category->slug, 'help' => 'Boş qalsa AZ adından avtomatik yaranır. Məs: vitaminler'])
        </div></div>

        <div>
            <div class="card"><div class="card-body">
                @include('admin.partials.image', ['name' => 'image', 'label' => 'Şəkil', 'current' => $category->image])
            </div></div>
            <div class="card"><div class="card-body">
                @include('admin.partials.input', ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'min' => 0, 'value' => $category->sort_order ?? 0, 'help' => 'Kiçik rəqəm əvvəl göstərilir.'])
                @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Saytda göstər', 'checked' => $category->is_active])
            </div></div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">{{ $isEdit ? 'Yadda saxla' : 'Əlavə et' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection
