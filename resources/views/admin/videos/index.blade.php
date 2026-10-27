@extends('admin.layouts.app')

@section('title', 'Video qalereya')

@section('content')
<div class="page-head">
    <div>
        <h1>Video qalereya</h1>
        <p class="sub">{{ $videos->total() }} video</p>
    </div>
    <a href="{{ route('admin.videos.create') }}" class="btn">+ Yeni video</a>
</div>

@if ($videos->isEmpty())
    <div class="card"><div class="empty">Hələ video yoxdur.</div></div>
@else
    <div class="photo-grid">
        @foreach ($videos as $video)
            <div class="card photo-card">
                <a href="{{ $video->video_url }}" target="_blank" rel="noopener" title="Videoya bax">
                    @if ($video->thumbnailUrl())
                        <img src="{{ $video->thumbnailUrl() }}" alt="" loading="lazy">
                    @else
                        <img alt="" style="aspect-ratio:4/3">
                    @endif
                </a>
                <div class="meta">
                    <div class="title">{{ $video->tr('title', 'az') }}</div>
                    <div class="small muted">Sıra: {{ $video->sort_order }} · @include('admin.partials.status', ['active' => $video->is_active])</div>
                    <div class="row-actions">
                        <a href="{{ route('admin.videos.edit', $video) }}" class="btn btn-ghost btn-sm">Redaktə</a>
                        @include('admin.partials.delete', [
                            'action'  => route('admin.videos.destroy', $video),
                            'confirm' => 'Video silinsin?',
                        ])
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{ $videos->links('pagination::default') }}
@endsection
