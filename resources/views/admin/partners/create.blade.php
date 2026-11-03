@extends('admin.layouts.app')

@section('title', 'Tərəfdaş əlavə et')

@section('content')
<div class="page-head">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.partners.index') }}">Tərəfdaşlar</a> /</div>
        <h1>Tərəfdaş əlavə et</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.partners.store') }}">
    @csrf
    <div class="form-grid">
        <div class="card"><div class="card-body">
            @include('admin.partials.input', ['name' => 'name', 'label' => 'Brend adı', 'help' => 'İstəyə bağlı — şəkil üçün alt mətn kimi işlənir.'])
            @include('admin.partials.input', ['name' => 'url', 'label' => 'Veb-sayt', 'type' => 'url', 'placeholder' => 'https://...', 'help' => 'İstəyə bağlı — loqoya klik edəndə açılır.'])
            @include('admin.partials.input', ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'min' => 0])
            @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Saytda göstər', 'checked' => true])
        </div></div>

        <div class="card"><div class="card-body">
            @include('admin.partials.image', [
                'name' => 'logo', 'label' => 'Loqo', 'required' => true,
                'help' => 'Şəffaf fonlu PNG tövsiyə olunur. JPG, PNG və ya WEBP, ən çox 4 MB.',
            ])
        </div></div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">Əlavə et</button>
        <a href="{{ route('admin.partners.index') }}" class="btn btn-ghost">Ləğv et</a>
    </div>
</form>
@endsection
