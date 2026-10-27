{{-- @include('admin.partials.input', ['name' => 'slug', 'label' => 'Slug', 'value' => ..., 'type' => 'text', 'help' => ...]) --}}
@php $type = $type ?? 'text'; @endphp
<div class="field">
    <label for="f_{{ $name }}">{{ $label }} @if (! empty($required))<span class="req">*</span>@endif</label>
    <input id="f_{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value ?? '') }}"
           class="@error($name) is-invalid @enderror"
           @isset($placeholder) placeholder="{{ $placeholder }}" @endisset
           @isset($step) step="{{ $step }}" @endisset
           @isset($min) min="{{ $min }}" @endisset>
    @error($name) <div class="error-text">{{ $message }}</div> @enderror
    @isset($help) <div class="help">{{ $help }}</div> @endisset
</div>
