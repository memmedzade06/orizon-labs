@extends('admin.layouts.app')

@section('title', 'Slayder')

@section('content')
<div class="page-head">
    <div>
        <h1>Slayder</h1>
        <p class="sub">Ana səhifənin yuxarısındakı slaydlar</p>
    </div>
    <a href="{{ route('admin.slides.create') }}" class="btn">+ Yeni slayd</a>
</div>

<div class="card">
    @if ($slides->isEmpty())
        <div class="empty">Hələ slayd yoxdur.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:70px">Şəkil</th>
                    <th>Başlıq</th>
                    <th class="num">Sıra</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($slides as $slide)
                    <tr>
                        <td>
                            @if ($slide->image)
                                <img src="{{ Storage::disk('public')->url($slide->image) }}" alt="" class="thumb">
                            @else
                                <div class="thumb-empty"></div>
                            @endif
                        </td>
                        <td>
                            @if ($slide->tr('eyebrow', 'az')) <div class="small muted">{{ $slide->tr('eyebrow', 'az') }}</div> @endif
                            <a href="{{ route('admin.slides.edit', $slide) }}"><strong>{{ $slide->tr('title', 'az') }}</strong></a>
                        </td>
                        <td class="num">{{ $slide->sort_order }}</td>
                        <td>@include('admin.partials.status', ['active' => $slide->is_active])</td>
                        <td class="actions">
                            <a href="{{ route('admin.slides.edit', $slide) }}" class="btn btn-ghost btn-sm">Redaktə</a>
                            @include('admin.partials.delete', [
                                'action'  => route('admin.slides.destroy', $slide),
                                'confirm' => 'Slayd silinsin?',
                            ])
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
