@extends('admin.layouts.app')

@php $isEdit = $video->exists; @endphp

@section('title', $isEdit ? 'Videonu redaktə et' : 'Yeni video')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.videos.index') }}">Video qalereya</a> /</div>
        <h1>{{ $isEdit ? $video->tr('title', 'az') : 'Yeni video' }}</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $isEdit ? route('admin.videos.update', $video) : route('admin.videos.store') }}">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="form-grid">
        <div class="card"><div class="card-body">
            @include('admin.partials.trans', ['name' => 'title', 'label' => 'Başlıq', 'value' => $video->title, 'required' => true])
            @include('admin.partials.input', [
                'name' => 'video_url', 'label' => 'Video linki', 'type' => 'url', 'required' => true,
                'value' => $video->video_url, 'placeholder' => 'https://www.youtube.com/watch?v=...',
                'help' => 'YouTube və ya Vimeo linki.',
            ])
            @if ($isEdit && $video->embedUrl())
                <div class="field">
                    <div class="label" style="margin-bottom:6px">Baxış</div>
                    <iframe src="{{ $video->embedUrl() }}" title="Video" style="width:100%;aspect-ratio:16/9;border:0;border-radius:8px"
                            allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                </div>
            @endif
        </div></div>

        <div>
            <div class="card"><div class="card-body">
                @include('admin.partials.image', [
                    'name' => 'thumbnail', 'label' => 'Önizləmə şəkli', 'current' => $video->thumbnail,
                    'help' => 'İstəyə bağlıdır. Boş qalsa YouTube-un öz şəkli göstərilir.',
                ])
            </div></div>
            <div class="card"><div class="card-body">
                @include('admin.partials.input', ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'min' => 0, 'value' => $video->sort_order ?? 0])
                @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Saytda göstər', 'checked' => $video->is_active])
            </div></div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">{{ $isEdit ? 'Yadda saxla' : 'Əlavə et' }}</button>
        <a href="{{ route('admin.videos.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection
