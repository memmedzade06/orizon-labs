{{-- @include('admin.partials.status', ['active' => $item->is_active]) --}}
@if ($active)
    <span class="badge badge-green">Aktiv</span>
@else
    <span class="badge">Gizli</span>
@endif
