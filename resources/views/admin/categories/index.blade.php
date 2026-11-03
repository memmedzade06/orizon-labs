@extends('admin.layouts.app')

@section('title', 'Kateqoriyalar')

@section('content')
<div class="page-head">
    <div>
        <h1>Kateqoriyalar</h1>
        <p class="sub">{{ $total }} kateqoriya</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn">+ Yeni kateqoriya</a>
</div>

<div class="card">
    @if ($categories->isEmpty())
        <div class="empty">Aktiv kateqoriya yoxdur.</div>
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
                    @include('admin.categories.row', ['category' => $category, 'isChild' => false])
                    @foreach ($category->children as $child)
                        @include('admin.categories.row', ['category' => $child, 'isChild' => true])
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
