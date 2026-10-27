@extends('admin.layouts.app')

@section('title', 'Müraciətlər')

@section('content')
<div class="page-head">
    <div>
        <h1>Müraciətlər</h1>
        <p class="sub">Saytdakı "Əlaqə" və "Tərəfdaşlıq" formalarından gələnlər</p>
    </div>
</div>

<form method="GET" class="filters">
    <select name="type" aria-label="Növ">
        <option value="">Bütün növlər</option>
        @foreach (\App\Models\ContactMessage::TYPES as $key => $label)
            <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="status" aria-label="Status">
        <option value="">Hamısı</option>
        <option value="unread" @selected(request('status') === 'unread')>Oxunmamış</option>
    </select>
    <button type="submit" class="btn btn-ghost">Filtr</button>
</form>

<div class="card">
    @if ($messages->isEmpty())
        <div class="empty">Müraciət yoxdur.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>Ad</th>
                    <th>Növ</th>
                    <th>Əlaqə</th>
                    <th>Mesaj</th>
                    <th>Tarix</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($messages as $m)
                    <tr class="{{ $m->isRead() ? '' : 'unread' }}">
                        <td>
                            <a href="{{ route('admin.messages.show', $m) }}">{{ $m->name }}</a>
                            @if ($m->company) <div class="small muted">{{ $m->company }}</div> @endif
                        </td>
                        <td><span class="badge {{ $m->type === 'partner' ? 'badge-amber' : '' }}">{{ $m->typeLabel() }}</span></td>
                        <td class="small">{{ $m->phone }} @if ($m->email)<br>{{ $m->email }}@endif</td>
                        <td class="small">{{ \Illuminate\Support\Str::limit($m->message, 70) }}</td>
                        <td class="small">{{ $m->created_at->format('d.m.Y H:i') }}</td>
                        <td class="actions">
                            @unless ($m->isRead()) <span class="badge badge-green">Yeni</span> @endunless
                            <a href="{{ route('admin.messages.show', $m) }}" class="btn btn-ghost btn-sm">Aç</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{ $messages->links('pagination::default') }}
@endsection
