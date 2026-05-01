<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? config('app.name') }} | {{ config('app.name') }}</title>
    <meta name="description" content="{{ $pageDescription ?? 'UNIDA Gateway workspace for businesses, investors, stakeholders and ecosystem partners.' }}">
    <meta name="theme-color" content="#0A5DB7">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/legal.css') }}">
</head>
<body data-page="{{ $pageName ?? 'dashboard' }}">
    @php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = \App\Support\LegacyRoleMatrix::normalizeRole((string) ($_SESSION['user_role'] ?? ''));
        $roleLabel = $role !== ''
            ? ucwords(str_replace(['_', '-'], ' ', strtolower($role)))
            : 'User';
        $activeSidebar = $activeSidebar
            ?? \App\Support\LegacyRoleMatrix::moduleKeyFromUri(request()->getPathInfo())
            ?? 'overview';
        $legacyData = \App\Support\LegacyRoleMatrix::modulesForRole($role);
        $moduleIcons = [
            'overview' => 'gauge-high',
            'profile' => 'user',
            'readiness' => 'chart-line',
            'documents' => 'file-shield',
            'discover' => 'magnifying-glass-chart',
            'verified-businesses' => 'circle-check',
            'shortlist' => 'bookmark',
            'my-projects' => 'folder-open',
            'pipeline' => 'diagram-project',
            'meetings' => 'calendar-check',
            'businesses' => 'building',
            'recommendations' => 'handshake-angle',
            'connections' => 'handshake',
            'follow-ups' => 'list-check',
            'reports' => 'file-lines',
            'users' => 'users',
            'roles' => 'user-lock',
            'verification' => 'shield-halved',
            'verifications' => 'shield-halved',
            'verification-track' => 'clipboard-list',
            'investors' => 'coins',
            'stakeholders' => 'users-gear',
            'opportunities' => 'briefcase',
            'uploads' => 'cloud-arrow-up',
            'insights' => 'chart-pie',
            'messages' => 'envelope',
            'chatbot' => 'robot',
            'ai-assistants' => 'wand-magic-sparkles',
            'ai-tools' => 'screwdriver-wrench',
            'legal' => 'scale-balanced',
            'settings' => 'gear',
            'role-dashboard' => 'table-columns',
        ];
    @endphp

    <div class="topbar">
        <div class="container topbar-inner">
            <span>
                <i class="fa-solid fa-shield-halved"></i>
                UNIDA Gateway: verification, readiness and stakeholder coordination
            </span>
            <span>
                <i class="fa-solid fa-location-dot"></i>
                Built in Tanzania by UNIDA TECH LIMITED
            </span>
        </div>
    </div>

    <header class="site-header" id="siteHeader">
        <div class="container nav-wrap">
            <a href="/" class="brand" aria-label="UNIDA Gateway">
                <span class="brand-mark">
                    <span class="drop drop-blue"></span>
                    <span class="drop drop-green"></span>
                </span>
                <span class="brand-text">
                    <strong>UNIDA Gateway</strong>
                    <small>Investment Ecosystem Platform</small>
                </span>
            </a>

            <nav class="desktop-nav" aria-label="Primary navigation">
                <a href="/">Home</a>
                <a href="/dashboard">
                    <i class="fa-solid fa-gauge-high"></i>
                    My Workspace
                </a>
            </nav>

            <div class="nav-actions">
                <span class="role-indicator" title="Logged in role">
                    <i class="fa-solid fa-user-shield"></i>
                    {{ $roleLabel }}
                </span>
                <a class="btn btn-primary" href="/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
                <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="dashboard-layout">
        <aside class="dashboard-sidebar">
            @if(!empty($legacyData))
                @foreach($legacyData as $moduleKey => $module)
                    <a
                        class="{{ $activeSidebar === $moduleKey ? 'active' : '' }}"
                        href="{{ $module['uri'] ?? '#' }}"
                    >
                        <i class="fa-solid fa-{{ $moduleIcons[$moduleKey] ?? 'circle' }}"></i>
                        {{ $module['label'] ?? ucfirst($moduleKey) }}
                    </a>
                @endforeach
            @endif

            <a href="/logout.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>
        </aside>

        <main class="dashboard-main">
            @yield('content')
        </main>
    </div>

    <footer class="site-footer">
        <div class="container footer-bottom">
            <span>© {{ date('Y') }} UNIDA TECH LIMITED. All rights reserved.</span>
            <span>
                Designed and developed by
                <a href="https://unidatechs.com" target="_blank" rel="noopener">UNIDA TECH LIMITED</a>
            </span>
        </div>
    </footer>

    <link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rieta-chatbot.css') }}">

    <script>
        window.UNIDA_BASE_URL = "{{ url('/') }}/";
        window.UNIDA_LANG = "en";
        window.UNIDA_ROLE_ACCESS = @json($legacyData);
    </script>

    <script src="{{ asset('assets/js/chatbot.js') }}"></script>
    <script src="{{ asset('assets/js/rieta-chatbot.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
