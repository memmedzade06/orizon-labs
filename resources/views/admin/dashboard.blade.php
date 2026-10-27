@extends('admin.layouts.base')

@section('title', 'Panel')

@section('styles')
    .topbar {
        background: var(--surface); border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 24px; gap: 12px;
    }
    .topbar .brand { font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .brand-mark {
        width: 30px; height: 30px; border-radius: 7px; background: var(--primary);
        color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 14px;
    }
    .user { display: flex; align-items: center; gap: 12px; color: var(--muted); font-size: 14px; }
    .user form { margin: 0; }
    .user .btn { padding: 7px 12px; font-size: 14px; }
    .content { max-width: 1100px; margin: 0 auto; padding: 32px 24px; }
    h1 { font-size: 24px; margin: 0 0 4px; }
    .sub { color: var(--muted); margin: 0 0 28px; }
    .cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
    .card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 20px;
    }
    .card .label { color: var(--muted); font-size: 13px; }
    .card .value { font-size: 28px; font-weight: 700; margin-top: 4px; font-variant-numeric: tabular-nums; }
    @media (max-width: 560px) {
        .topbar { padding: 12px 16px; }
        .user .name { display: none; }
        .content { padding: 24px 16px; }
    }
@endsection

@section('body')
<header class="topbar">
    <div class="brand"><span class="brand-mark">O</span> Orizon Labs · Admin</div>
    <div class="user">
        <span class="name">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost">Çıxış</button>
        </form>
    </div>
</header>

<main class="content">
    <h1>Xoş gəldiniz, {{ auth()->user()->name }}</h1>
    <p class="sub">Admin panelə uğurla daxil oldunuz.</p>

    <div class="cards">
        <div class="card">
            <div class="label">İstifadəçilər</div>
            <div class="value">{{ $userCount }}</div>
        </div>
        <div class="card">
            <div class="label">Adminlər</div>
            <div class="value">{{ $adminCount }}</div>
        </div>
    </div>
</main>
@endsection
