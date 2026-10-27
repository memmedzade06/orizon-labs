@extends('admin.layouts.app')

@section('title', 'Xəbərlər')

@section('content')
<div class="page-head">
    <div>
        <h1>Xəbərlər</h1>
        <p class="sub">{{ $news->total() }} xəbər</p>
    </div>
    <a href="{{ route('admin.news.create') }}" class="btn">+ Yeni xəbər</a>
</div>

<form method="GET" class="filters">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Başlıqda axtar..." aria-label="Axtarış">
    <button type="submit" class="btn btn-ghost">Axtar</button>
    @if (request()->filled('q'))
        <a href="{{ route('admin.news.index') }}" class="small">Sıfırla</a>
    @endif
</form>

<div class="card">
    @if ($news->isEmpty())
        <div class="empty">Xəbər tapılmadı.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:70px">Şəkil</th>
                    <th>Başlıq</th>
                    <th>Tarix</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($news as $item)
                    <tr>
                        <td>
                            @if ($item->image)
                                <img src="{{ Storage::disk('public')->url($item->image) }}" alt="" class="thumb">
                            @else
                                <div class="thumb-empty"></div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.news.edit', $item) }}"><strong>{{ $item->tr('title', 'az') }}</strong></a>
                            <div>
                                @if ($item->tr('tag', 'az')) <span class="badge">{{ $item->tr('tag', 'az') }}</span> @endif
                                @if ($item->is_featured) <span class="badge badge-amber">Əsas xəbər</span> @endif
                            </div>
                        </td>
                        <td class="small">{{ $item->published_at?->format('d.m.Y H:i') }}</td>
                        <td>
                            @if (! $item->is_published)
                                <span class="badge">Qaralama</span>
                            @elseif ($item->published_at && $item->published_at->isFuture())
                                <span class="badge badge-amber">Planlaşdırılıb</span>
                            @else
                                <span class="badge badge-green">Dərc olunub</span>
                            @endif
                        </td>
                        <td class="actions">
                            <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-ghost btn-sm">Redaktə</a>
                            @include('admin.partials.delete', [
                                'action'  => route('admin.news.destroy', $item),
                                'confirm' => 'Xəbər silinsin?',
                            ])
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{ $news->links('pagination::default') }}
@endsection
