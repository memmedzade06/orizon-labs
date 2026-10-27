@extends('admin.layouts.app')

@section('title', 'Kateqoriyalar')

@section('content')
<div class="page-head">
    <div>
        <h1>Kateqoriyalar</h1>
        <p class="sub">{{ $categories->count() }} kateqoriya</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn">+ Yeni kateqoriya</a>
</div>

<div class="card">
    @if ($categories->isEmpty())
        <div class="empty">Hələ kateqoriya yoxdur.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:70px">Şəkil</th>
                    <th>Ad</th>
                    <th class="num">Məhsul</th>
                    <th class="num">Sıra</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>
                            @if ($category->image)
                                <img src="{{ Storage::disk('public')->url($category->image) }}" alt="" class="thumb">
                            @else
                                <div class="thumb-empty"></div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category) }}"><strong>{{ $category->tr('name', 'az') }}</strong></a>
                            <div class="small muted">{{ $category->tr('description', 'az') }}</div>
                        </td>
                        <td class="num">{{ $category->products_count }}</td>
                        <td class="num">{{ $category->sort_order }}</td>
                        <td>@include('admin.partials.status', ['active' => $category->is_active])</td>
                        <td class="actions">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-ghost btn-sm">Redaktə</a>
                            @include('admin.partials.delete', [
                                'action'  => route('admin.categories.destroy', $category),
                                'confirm' => 'Kateqoriya silinsin?',
                            ])
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
