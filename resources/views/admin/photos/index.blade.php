@extends('admin.layouts.app')

@section('title', 'Şəkil qalereyası')

@section('content')
<div class="page-head">
    <div>
        <h1>Şəkil qalereyası</h1>
        <p class="sub">{{ $photos->total() }} şəkil</p>
    </div>
    <a href="{{ route('admin.photos.create') }}" class="btn">+ Şəkil yüklə</a>
</div>

@if ($photos->isEmpty())
    <div class="card"><div class="empty">Hələ şəkil yoxdur.</div></div>
@else
    <div class="photo-grid">
        @foreach ($photos as $photo)
            <div class="card photo-card">
                <a href="{{ route('admin.photos.edit', $photo) }}">
                    <img src="{{ Storage::disk('public')->url($photo->image) }}" alt="{{ $photo->tr('title', 'az') }}" loading="lazy">
                </a>
                <div class="meta">
                    <div class="title">{{ $photo->tr('title', 'az') ?: 'Yazısız' }}</div>
                    <div class="small muted">Sıra: {{ $photo->sort_order }} · @include('admin.partials.status', ['active' => $photo->is_active])</div>
                    <div class="row-actions">
                        <a href="{{ route('admin.photos.edit', $photo) }}" class="btn btn-ghost btn-sm">Redaktə</a>
                        @include('admin.partials.delete', [
                            'action'  => route('admin.photos.destroy', $photo),
                            'confirm' => 'Şəkil silinsin?',
                        ])
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{ $photos->links('pagination::default') }}
@endsection
