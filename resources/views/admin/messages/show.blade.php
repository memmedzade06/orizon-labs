@extends('admin.layouts.app')

@section('title', 'Müraciət')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.messages.index') }}">Müraciətlər</a> /</div>
        <h1>{{ $item->name }}</h1>
        <p class="sub">{{ $item->typeLabel() }} · {{ $item->created_at->format('d.m.Y H:i') }}</p>
    </div>
    @include('admin.partials.delete', [
        'action'  => route('admin.messages.destroy', $item),
        'confirm' => 'Müraciət silinsin?',
    ])
</div>

<div class="form-grid">
    <div class="card"><div class="card-body">
        <h2 class="card-title">Mesaj</h2>
        <div class="message-body">{{ $item->message }}</div>
    </div></div>

    <div class="card"><div class="card-body">
        <dl class="meta-list">
            @if ($item->company)<dt>Şirkət</dt><dd>{{ $item->company }}</dd>@endif
            @if ($item->country)<dt>Ölkə</dt><dd>{{ $item->country }}</dd>@endif
            @if ($item->phone)<dt>Telefon</dt><dd><a href="tel:{{ preg_replace('/[^0-9+]/', '', $item->phone) }}">{{ $item->phone }}</a></dd>@endif
            @if ($item->email)<dt>E-poçt</dt><dd><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></dd>@endif
            @if ($item->locale)<dt>Dil</dt><dd>{{ strtoupper($item->locale) }}</dd>@endif
            <dt>Oxundu</dt><dd>{{ $item->read_at?->format('d.m.Y H:i') }}</dd>
        </dl>
    </div></div>
</div>
@endsection
