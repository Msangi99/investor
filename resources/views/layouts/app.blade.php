<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'UNIDA Gateway' }}</title>
    <style>
        :root { color-scheme: light dark; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f7fb; color: #0f172a; }
        .topbar { background: #0b5cab; color: #fff; padding: 12px 18px; }
        .topbar a { color: #fff; text-decoration: none; font-weight: 600; }
        .container { max-width: 1200px; margin: 0 auto; padding: 18px; }
        .panel { background: #fff; border: 1px solid #dbe5f1; border-radius: 8px; padding: 14px; }
    </style>
</head>
<body>
    <header class="topbar">
        <a href="/">UNIDA Gateway (Laravel)</a>
    </header>
    <main class="container">
        @yield('content')
    </main>
</body>
</html>
