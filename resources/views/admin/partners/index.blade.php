@extends('admin.layouts.app')

@section('title', 'Tərəfdaşlar')

@section('content')
<div class="page-head">
    <div>
        <h1>Tərəfdaşlar</h1>
        <p class="sub">{{ $partners->total() }} tərəfdaş</p>
    </div>
    <a href="{{ route('admin.partners.create') }}" class="btn">+ Tərəfdaş əlavə et</a>
</div>

@if ($partners->isEmpty())
    <div class="card"><div class="empty">Hələ tərəfdaş yoxdur.</div></div>
@else
    <div class="photo-grid">
        @foreach ($partners as $partner)
            <div class="card photo-card">
                <a href="{{ route('admin.partners.edit', $partner) }}">
                    <img src="{{ Storage::disk('public')->url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy">
                </a>
                <div class="meta">
                    <div class="title">{{ $partner->name ?: 'Adsız' }}</div>
                    <div class="small muted">Sıra: {{ $partner->sort_order }} · @include('admin.partials.status', ['active' => $partner->is_active])</div>
                    <div class="row-actions">
                        <a href="{{ route('admin.partners.edit', $partner) }}" class="btn btn-ghost btn-sm">Redaktə</a>
                        @include('admin.partials.delete', [
                            'action'  => route('admin.partners.destroy', $partner),
                            'confirm' => 'Tərəfdaş silinsin?',
                        ])
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{ $partners->links('pagination::default') }}
@endsection
