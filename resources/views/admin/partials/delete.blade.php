{{-- @include('admin.partials.delete', ['action' => route(...), 'confirm' => 'Silinsin?']) --}}
<form method="POST" action="{{ $action }}" class="inline" data-confirm="{{ $confirm ?? 'Silmək istədiyinizə əminsiniz?' }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">{{ $label ?? 'Sil' }}</button>
</form>
