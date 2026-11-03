@extends('admin.layouts.app')

@section('title', 'Tərəfdaşı redaktə et')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.partners.index') }}">Tərəfdaşlar</a> /</div>
        <h1>Tərəfdaşı redaktə et</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.partners.update', $partner) }}">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="card"><div class="card-body">
            @include('admin.partials.input', ['name' => 'name', 'label' => 'Brend adı', 'value' => $partner->name])
            @include('admin.partials.input', ['name' => 'url', 'label' => 'Veb-sayt', 'type' => 'url', 'value' => $partner->url, 'placeholder' => 'https://...'])
            @include('admin.partials.input', ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'min' => 0, 'value' => $partner->sort_order])
            @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Saytda göstər', 'checked' => $partner->is_active])
        </div></div>

        <div class="card"><div class="card-body">
            @include('admin.partials.image', [
                'name' => 'logo', 'label' => 'Loqo', 'current' => $partner->logo, 'required' => true,
                'help' => 'Dəyişmək üçün yeni loqo seçin. Şəffaf fonlu PNG tövsiyə olunur.',
            ])
        </div></div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">Yadda saxla</button>
        <a href="{{ route('admin.partners.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection
