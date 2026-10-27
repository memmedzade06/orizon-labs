<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #f4f6f5;
            --surface: #ffffff;
            --text: #1c2421;
            --muted: #66736d;
            --border: #dfe5e2;
            --primary: #2f7d4f;
            --primary-hover: #276a43;
            --danger: #b4372f;
            --danger-bg: #fbecea;
            --radius: 10px;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 15px;
            line-height: 1.5;
        }
        a { color: var(--primary); }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 8px; border: 0; border-radius: 8px; cursor: pointer;
            padding: 10px 16px; font: inherit; font-weight: 600;
            background: var(--primary); color: #fff; text-decoration: none;
            transition: background .15s;
        }
        .btn:hover { background: var(--primary-hover); }
        .btn:focus-visible { outline: 3px solid rgba(47,125,79,.35); outline-offset: 2px; }
        .btn-ghost { background: transparent; color: var(--muted); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--bg); color: var(--text); }
        @yield('styles')
    </style>
</head>
<body>
    @yield('body')
</body>
</html>
