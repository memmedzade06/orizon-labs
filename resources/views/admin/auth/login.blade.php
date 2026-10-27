@extends('admin.layouts.base')

@section('title', 'Giriş')

@section('styles')
    .login-wrap {
        min-height: 100vh; display: flex; align-items: center; justify-content: center;
        padding: 24px 16px;
    }
    .login-card {
        width: 100%; max-width: 400px; background: var(--surface);
        border: 1px solid var(--border); border-radius: var(--radius);
        padding: 32px 28px; box-shadow: 0 8px 30px rgba(20,40,30,.06);
    }
    .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; }
    .brand-mark {
        width: 36px; height: 36px; border-radius: 8px; background: var(--primary);
        color: #fff; display: grid; place-items: center; font-weight: 700;
    }
    .brand-name { font-weight: 700; font-size: 17px; }
    h1 { font-size: 22px; margin: 0 0 4px; }
    .sub { color: var(--muted); margin: 0 0 24px; }
    .field { margin-bottom: 16px; }
    label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; }
    input[type=email], input[type=password] {
        width: 100%; padding: 11px 12px; border: 1px solid var(--border);
        border-radius: 8px; font: inherit; background: #fff; color: var(--text);
    }
    input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(47,125,79,.15); }
    input.is-invalid { border-color: var(--danger); }
    .error-text { color: var(--danger); font-size: 13px; margin-top: 4px; }
    .remember { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 14px; color: var(--muted); }
    .remember input { width: 16px; height: 16px; accent-color: var(--primary); }
    .btn { width: 100%; padding: 12px; }
    .back { display: block; text-align: center; margin-top: 18px; font-size: 14px; color: var(--muted); text-decoration: none; }
    .back:hover { color: var(--text); }
@endsection

@section('body')
<main class="login-wrap">
    <div class="login-card">
        <div class="brand">
            <div class="brand-mark">O</div>
            <div class="brand-name">Orizon Labs</div>
        </div>

        <h1>Admin panelə giriş</h1>
        <p class="sub">Davam etmək üçün hesabınıza daxil olun.</p>

        <form method="POST" action="{{ route('admin.login.submit') }}" novalidate>
            @csrf

            <div class="field">
                <label for="email">E-poçt</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="@error('email') is-invalid @enderror"
                       autocomplete="username" required autofocus>
                @error('email') <div class="error-text" role="alert">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password">Şifrə</label>
                <input id="password" type="password" name="password"
                       class="@error('password') is-invalid @enderror"
                       autocomplete="current-password" required>
                @error('password') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <label class="remember">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                Məni xatırla
            </label>

            <button type="submit" class="btn">Daxil ol</button>
        </form>

        <a href="{{ url('/') }}" class="back">← Sayta qayıt</a>
    </div>
</main>
@endsection
