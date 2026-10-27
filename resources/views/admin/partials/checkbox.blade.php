{{-- @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Aktiv', 'checked' => true]) --}}
<input type="hidden" name="{{ $name }}" value="0">
<label class="check">
    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked ?? false))>
    {{ $label }}
</label>
