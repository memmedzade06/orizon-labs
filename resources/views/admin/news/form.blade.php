@extends('admin.layouts.app')

@php $isEdit = $item->exists; @endphp

@section('title', $isEdit ? 'Xəbəri redaktə et' : 'Yeni xəbər')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.news.index') }}">Xəbərlər</a> /</div>
        <h1>{{ $isEdit ? $item->tr('title', 'az') : 'Yeni xəbər' }}</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $isEdit ? route('admin.news.update', $item) : route('admin.news.store') }}">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="form-grid">
        <div>
            <div class="card"><div class="card-body">
                @include('admin.partials.trans', ['name' => 'title', 'label' => 'Başlıq', 'value' => $item->title, 'required' => true])
                @include('admin.partials.trans', ['name' => 'excerpt', 'label' => 'Qısa mətn', 'value' => $item->excerpt, 'type' => 'textarea', 'rows' => 3, 'help' => 'Xəbər kartında görünür.'])
                @include('admin.partials.trans', ['name' => 'body', 'label' => 'Tam mətn', 'value' => $item->body, 'type' => 'textarea', 'rows' => 12])
            </div></div>

            <div class="card"><div class="card-body">
                <h2 class="card-title">SEO</h2>
                @include('admin.partials.trans', ['name' => 'meta_title', 'label' => 'SEO başlıq', 'value' => $item->meta_title, 'help' => 'Boş qalsa xəbərin başlığı istifadə olunur.'])
                @include('admin.partials.trans', ['name' => 'meta_description', 'label' => 'SEO təsvir', 'value' => $item->meta_description, 'type' => 'textarea', 'rows' => 2])
            </div></div>
        </div>

        <div>
            <div class="card"><div class="card-body">
                @include('admin.partials.input', [
                    'name' => 'published_at', 'label' => 'Dərc tarixi', 'type' => 'datetime-local',
                    'value' => $item->published_at?->format('Y-m-d\TH:i'),
                    'help' => 'Gələcək tarix seçsəniz, xəbər həmin vaxt saytda görünəcək.',
                ])
                @include('admin.partials.checkbox', ['name' => 'is_published', 'label' => 'Dərc et', 'checked' => $item->is_published])
                @include('admin.partials.checkbox', ['name' => 'is_featured', 'label' => 'Əsas xəbər (böyük kart)', 'checked' => $item->is_featured])
            </div></div>

            <div class="card"><div class="card-body">
                @include('admin.partials.image', ['name' => 'image', 'label' => 'Şəkil', 'current' => $item->image])
            </div></div>

            <div class="card"><div class="card-body">
                @include('admin.partials.trans', ['name' => 'tag', 'label' => 'Teq', 'value' => $item->tag, 'help' => 'Məs: İxrac, Məhsul, Tədbir'])
                @include('admin.partials.input', ['name' => 'slug', 'label' => 'Slug (URL)', 'value' => $item->slug, 'help' => 'Boş qalsa AZ başlığından yaranır.'])
            </div></div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">{{ $isEdit ? 'Yadda saxla' : 'Əlavə et' }}</button>
        <a href="{{ route('admin.news.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection
