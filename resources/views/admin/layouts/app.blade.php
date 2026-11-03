<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') — Orizon Labs Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/admin/admin.css') }}?v=2">
</head>
<body>
@php
    $unreadMessages = \App\Models\ContactMessage::unread()->count();
    $nav = [
        'Əsas' => [
            ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Panel'],
        ],
        'Kataloq' => [
            ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'label' => 'Kateqoriyalar'],
            ['route' => 'admin.products.index',   'match' => 'admin.products.*',   'label' => 'Məhsullar'],
        ],
        'Məzmun' => [
            ['route' => 'admin.news.index',   'match' => 'admin.news.*',   'label' => 'Xəbərlər'],
            ['route' => 'admin.slides.index', 'match' => 'admin.slides.*', 'label' => 'Slayder'],
            ['route' => 'admin.photos.index', 'match' => 'admin.photos.*', 'label' => 'Şəkil qalereyası'],
            ['route' => 'admin.videos.index', 'match' => 'admin.videos.*', 'label' => 'Video qalereya'],
            ['route' => 'admin.certificates.index', 'match' => 'admin.certificates.*', 'label' => 'Sertifikatlar'],
            ['route' => 'admin.partners.index', 'match' => 'admin.partners.*', 'label' => 'Tərəfdaşlar'],
        ],
        'Digər' => [
            ['route' => 'admin.messages.index', 'match' => 'admin.messages.*', 'label' => 'Müraciətlər', 'count' => $unreadMessages],
            ['route' => 'admin.settings.edit',  'match' => 'admin.settings.*', 'label' => 'Ayarlar'],
        ],
    ];
@endphp

<div class="shell">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand"><span class="brand-mark">O</span> Orizon Labs</a>
        <nav class="nav" aria-label="Admin menyu">
            @foreach ($nav as $group => $items)
                <div class="nav-label">{{ $group }}</div>
                @foreach ($items as $item)
                    <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['match']) ? 'active' : '' }}">
                        <span>{{ $item['label'] }}</span>
                        @if (! empty($item['count']))
                            <span class="count">{{ $item['count'] }}</span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <button type="button" class="menu-toggle" onclick="document.body.classList.toggle('nav-open')" aria-controls="sidebar">☰ Menyu</button>
            <a href="{{ url('/') }}" target="_blank" rel="noopener" class="small muted">Sayta bax ↗</a>
            <div class="user">
                <span class="name">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Çıxış</button>
                </form>
            </div>
        </header>

        <main class="content">
            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    Formada xətalar var, zəhmət olmasa yoxlayın.
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.min.js') }}?v=11.10.5"></script>

<script>
    // Dil tabları: bir sahədə dili dəyişəndə formadakı bütün sahələr həmin dilə keçir
    (function () {
        function activate(scope, lang) {
            scope.querySelectorAll('[data-trans]').forEach(function (box) {
                box.querySelectorAll('[data-lang-btn]').forEach(function (b) {
                    var on = b.dataset.langBtn === lang;
                    b.classList.toggle('active', on);
                    b.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
                box.querySelectorAll('[data-lang-pane]').forEach(function (p) {
                    p.hidden = p.dataset.langPane !== lang;
                });
            });
        }
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-lang-btn]');
            if (!btn) return;
            e.preventDefault();
            activate(btn.closest('form') || document, btn.dataset.langBtn);
        });

        // SweetAlert2: təsdiq, bildiriş və toast mesajları
        var colors = { primary: '#2f7d4f', danger: '#b4372f', muted: '#66736d' };
        var hasSwal = typeof window.Swal !== 'undefined';

        window.adminModal = {
            confirm: function (message, onYes) {
                if (!hasSwal) { if (window.confirm(message)) onYes(); return; } // CDN yüklənməsə
                Swal.fire({
                    title: 'Silməyi təsdiqləyin',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Bəli, sil',
                    cancelButtonText: 'Ləğv et',
                    confirmButtonColor: colors.danger,
                    cancelButtonColor: colors.muted,
                    reverseButtons: true,
                    focusCancel: true
                }).then(function (r) { if (r.isConfirmed) onYes(); });
            },
            alert: function (message, heading) {
                if (!hasSwal) { window.alert(message); return; }
                Swal.fire({ title: heading || 'Diqqət', text: message, icon: 'info', confirmButtonText: 'Oldu', confirmButtonColor: colors.primary });
            },
            toast: function (message, type) {
                if (!hasSwal) return;
                Swal.fire({
                    toast: true, position: 'top-end', icon: type || 'success', title: message,
                    showConfirmButton: false, timer: type === 'error' ? 5000 : 3000, timerProgressBar: true,
                    didOpen: function (t) {
                        t.addEventListener('mouseenter', Swal.stopTimer);
                        t.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            }
        };

        // data-confirm olan formalar təsdiqdən sonra göndərilir
        document.addEventListener('submit', function (e) {
            var form = e.target;
            var msg = form.dataset.confirm;
            if (!msg) return;
            e.preventDefault();
            window.adminModal.confirm(msg, function () { form.submit(); });
        });

        // Serverdən gələn mesajlar (əlavə olundu, silindi, xəta ...)
        var flash = { success: @json(session('success')), error: @json(session('error')) };
        if (flash.error) {
            hasSwal
                ? Swal.fire({ icon: 'error', title: 'Xəta', text: flash.error, confirmButtonText: 'Oldu', confirmButtonColor: colors.primary })
                : window.alert(flash.error);
        } else if (flash.success) {
            window.adminModal.toast(flash.success, 'success');
        }

        // Yeni şəkil seçiləndə önizləmə
        document.addEventListener('change', function (e) {
            var input = e.target;
            if (input.type !== 'file' || !input.dataset.preview || !input.files[0]) return;
            var img = document.getElementById(input.dataset.preview);
            if (img) { img.src = URL.createObjectURL(input.files[0]); img.hidden = false; }
        });
    })();
</script>
@stack('scripts')
</body>
</html>
