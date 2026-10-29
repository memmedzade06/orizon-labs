<tr>
    <td>
        @if ($category->image)
            <img src="{{ Storage::disk('public')->url($category->image) }}" alt="" class="thumb">
        @else
            <div class="thumb-empty"></div>
        @endif
    </td>
    <td>
        <a href="{{ route('admin.categories.edit', $category) }}">
            @if ($isChild)<span class="muted" aria-hidden="true">&nbsp;&nbsp;&nbsp;&nbsp;↳ </span>@endif<strong>{{ $category->tr('name', 'az') }}</strong>
        </a>
        <div class="small muted">{{ $category->tr('description', 'az') }}</div>
    </td>
    <td class="num">{{ $category->products_count }}</td>
    <td class="num">{{ $category->sort_order }}</td>
    <td>@include('admin.partials.status', ['active' => $category->is_active])</td>
    <td class="actions">
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-ghost btn-sm">Redaktə</a>
        @include('admin.partials.delete', [
            'action'  => route('admin.categories.destroy', $category),
            'confirm' => 'Kateqoriya silinsin?',
        ])
    </td>
</tr>
