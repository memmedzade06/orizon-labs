@extends('admin.layouts.app')

@section('title', 'Sertifikatlar')

@section('content')
<div class="page-head">
    <div>
        <h1>Sertifikatlar</h1>
        <p class="sub">{{ $certificates->total() }} sertifikat</p>
    </div>
    <a href="{{ route('admin.certificates.create') }}" class="btn">+ Sertifikat yüklə</a>
</div>

@if ($certificates->isEmpty())
    <div class="card"><div class="empty">Hələ sertifikat yoxdur.</div></div>
@else
    <div class="photo-grid">
        @foreach ($certificates as $certificate)
            <div class="card photo-card">
                <a href="{{ route('admin.certificates.edit', $certificate) }}">
                    <img src="{{ Storage::disk('public')->url($certificate->image) }}" alt="{{ $certificate->tr('title', 'az') }}" loading="lazy">
                </a>
                <div class="meta">
                    <div class="title">{{ $certificate->tr('title', 'az') ?: 'Adsız' }}</div>
                    <div class="small muted">Sıra: {{ $certificate->sort_order }} · @include('admin.partials.status', ['active' => $certificate->is_active])</div>
                    <div class="row-actions">
                        <a href="{{ route('admin.certificates.edit', $certificate) }}" class="btn btn-ghost btn-sm">Redaktə</a>
                        @include('admin.partials.delete', [
                            'action'  => route('admin.certificates.destroy', $certificate),
                            'confirm' => 'Sertifikat silinsin?',
                        ])
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{ $certificates->links('pagination::default') }}
@endsection
