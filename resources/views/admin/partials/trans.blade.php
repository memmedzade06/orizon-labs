{{--
    Tərcüməli sahə (AZ / EN / RU tabları)
    @include('admin.partials.trans', [
        'name' => 'name', 'label' => 'Ad', 'value' => $model->name ?? [],
        'required' => true, 'type' => 'text|textarea', 'rows' => 4, 'help' => '...'
    ])
--}}
@php
    $value    = $value ?? [];
    $type     = $type ?? 'text';
    $required = $required ?? false;
    $rows     = $rows ?? 4;
    $fieldId  = 'f_' . str_replace(['[', ']', '.'], '_', $name);
@endphp
<div class="field" data-trans>
    <div class="field-head">
        <label for="{{ $fieldId }}_{{ \App\Support\Locales::DEFAULT }}">
            {{ $label }} @if ($required)<span class="req" title="AZ dilində mütləqdir">*</span>@endif
        </label>
        <div class="lang-tabs" role="group" aria-label="{{ $label }} — dil">
            @foreach (\App\Support\Locales::ALL as $code => $langName)
                <button type="button" data-lang-btn="{{ $code }}"
                        class="{{ $loop->first ? 'active' : '' }} {{ $errors->has("$name.$code") ? 'has-error' : '' }}"
                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $langName }}</button>
            @endforeach
        </div>
    </div>

    @foreach (\App\Support\Locales::ALL as $code => $langName)
        @php $current = old("$name.$code", $value[$code] ?? ''); @endphp
        <div class="lang-pane" data-lang-pane="{{ $code }}" @unless($loop->first) hidden @endunless>
            @if ($type === 'textarea')
                <textarea id="{{ $fieldId }}_{{ $code }}" name="{{ $name }}[{{ $code }}]" rows="{{ $rows }}"
                          class="@error("$name.$code") is-invalid @enderror"
                          lang="{{ $code }}">{{ $current }}</textarea>
            @else
                <input id="{{ $fieldId }}_{{ $code }}" type="text" name="{{ $name }}[{{ $code }}]" value="{{ $current }}"
                       class="@error("$name.$code") is-invalid @enderror" lang="{{ $code }}">
            @endif
            @error("$name.$code") <div class="error-text">{{ $message }}</div> @enderror
        </div>
    @endforeach

    @isset($help) <div class="help">{{ $help }}</div> @endisset
</div>
