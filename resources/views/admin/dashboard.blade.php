@extends('admin.layouts.app')

@section('title', 'Panel')

@section('content')
<div class="page-head">
    <div>
        <h1>Xoş gəldiniz, {{ auth()->user()->name }}</h1>
        <p class="sub">Orizon Labs admin paneli</p>
    </div>
</div>

<div class="stats">
    @foreach ($stats as $s)
        <a href="{{ route($s['route']) }}" class="card stat">
            <div class="label">{{ $s['label'] }}</div>
            <div class="value">{{ $s['value'] }}</div>
        </a>
    @endforeach
</div>

<div class="card" style="margin-top:20px">
    <div class="card-body" style="padding-bottom:6px">
        <h2 class="card-title">Son müraciətlər</h2>
    </div>
    @if ($latestMessages->isEmpty())
        <div class="empty">Hələ müraciət yoxdur.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <tbody>
                @foreach ($latestMessages as $m)
                    <tr class="{{ $m->isRead() ? '' : 'unread' }}">
                        <td><a href="{{ route('admin.messages.show', $m) }}">{{ $m->name }}</a></td>
                        <td><span class="badge">{{ $m->typeLabel() }}</span></td>
                        <td class="small">{{ \Illuminate\Support\Str::limit($m->message, 60) }}</td>
                        <td class="small muted">{{ $m->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
