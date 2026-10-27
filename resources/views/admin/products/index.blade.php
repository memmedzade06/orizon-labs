@extends('admin.layouts.app')

@section('title', 'Məhsullar')

@section('content')
<div class="page-head">
    <div>
        <h1>Məhsullar</h1>
        <p class="sub">{{ $products->total() }} məhsul</p>
    </div>
    @if ($categories->isEmpty())
        <a href="{{ route('admin.categories.create') }}" class="btn">Əvvəlcə kateqoriya yaradın</a>
    @else
        <a href="{{ route('admin.products.create') }}" class="btn">+ Yeni məhsul</a>
    @endif
</div>

<form method="GET" class="filters">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Ad və ya məhsul kodu..." aria-label="Axtarış">
    <select name="category" aria-label="Kateqoriya">
        <option value="">Bütün kateqoriyalar</option>
        @foreach ($categories as $c)
            <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->tr('name', 'az') }}</option>
        @endforeach
    </select>
    <select name="status" aria-label="Status">
        <option value="">Hamısı</option>
        <option value="active" @selected(request('status') === 'active')>Aktiv</option>
        <option value="hidden" @selected(request('status') === 'hidden')>Gizli</option>
    </select>
    <button type="submit" class="btn btn-ghost">Filtr</button>
    @if (request()->hasAny(['q', 'category', 'status']))
        <a href="{{ route('admin.products.index') }}" class="small">Sıfırla</a>
    @endif
</form>

<div class="card">
    @if ($products->isEmpty())
        <div class="empty">Məhsul tapılmadı.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:70px">Şəkil</th>
                    <th>Ad</th>
                    <th>Kateqoriya</th>
                    <th>Forma / həcm</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            @if ($product->image)
                                <img src="{{ Storage::disk('public')->url($product->image) }}" alt="" class="thumb">
                            @else
                                <div class="thumb-empty"></div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}"><strong>{{ $product->tr('name', 'az') }}</strong></a>
                            <div>
                                @if ($product->is_new) <span class="badge badge-green">Yeni</span> @endif
                                @if ($product->is_featured) <span class="badge badge-amber">Ən çox seçilən</span> @endif
                                @if ($product->sku) <span class="small muted">{{ $product->sku }}</span> @endif
                            </div>
                        </td>
                        <td>{{ $product->category?->tr('name', 'az') }}</td>
                        <td class="small">
                            {{ $product->tr('dosage_form', 'az') }}
                            @if ($product->tr('package_size', 'az')) · {{ $product->tr('package_size', 'az') }} @endif
                        </td>
                        <td>@include('admin.partials.status', ['active' => $product->is_active])</td>
                        <td class="actions">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-ghost btn-sm">Redaktə</a>
                            @include('admin.partials.delete', [
                                'action'  => route('admin.products.destroy', $product),
                                'confirm' => 'Məhsul silinsin?',
                            ])
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{ $products->links('pagination::default') }}
@endsection
