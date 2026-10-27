@extends('admin.layouts.app')

@section('title', 'Ayarlar')

@section('content')
<div class="page-head">
    <div>
        <h1>Ayarlar</h1>
        <p class="sub">Saytda görünən əlaqə məlumatları, rəqəmlər və kataloq</p>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    @foreach ($groups as $groupLabel => $fields)
        <div class="card"><div class="card-body">
            <h2 class="card-title">{{ $groupLabel }}</h2>

            @foreach ($fields as $key => $f)
                @php $input = \App\Http\Controllers\Admin\SettingController::inputName($key); $current = $values[$key] ?? null; @endphp

                @if ($f['type'] === 'trans')
                    @include('admin.partials.trans', [
                        'name' => $input, 'label' => $f['label'],
                        'value' => is_array($current) ? $current : [],
                        'help' => $f['help'] ?? null,
                    ])
                @elseif ($f['type'] === 'file')
                    <div class="field">
                        <label for="f_{{ $input }}">{{ $f['label'] }}</label>
                        @if ($current)
                            <p class="small" style="margin:0 0 8px">
                                Hazırkı fayl: <a href="{{ \App\Http\Controllers\Admin\SettingController::fileUrl($current) }}" target="_blank" rel="noopener">{{ basename($current) }}</a>
                            </p>
                        @endif
                        <input id="f_{{ $input }}" type="file" name="{{ $input }}" accept="application/pdf"
                               class="@error($input) is-invalid @enderror">
                        @error($input) <div class="error-text">{{ $message }}</div> @enderror
                        @isset($f['help']) <div class="help">{{ $f['help'] }}</div> @endisset
                        @if ($current)
                            <label class="check" style="margin-top:8px">
                                <input type="checkbox" name="remove_{{ $input }}" value="1"> Faylı sil
                            </label>
                        @endif
                    </div>
                @else
                    @include('admin.partials.input', [
                        'name' => $input, 'label' => $f['label'], 'type' => $f['type'],
                        'value' => is_scalar($current) ? $current : '',
                        'placeholder' => $f['placeholder'] ?? null,
                        'help' => $f['help'] ?? null,
                    ])
                @endif
            @endforeach
        </div></div>
    @endforeach

    <div class="form-actions">
        <button type="submit" class="btn">Yadda saxla</button>
    </div>
</form>
@endsection
