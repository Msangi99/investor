<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? config('app.name') }} | {{ config('app.name') }}</title>
    <meta name="description" content="{{ $pageDescription ?? 'UNIDA Gateway is an investment ecosystem platform for verification, readiness, stakeholder coordination and data-driven investment access.' }}">
    <meta name="theme-color" content="#0A5DB7">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/legal.css') }}">
</head>
<body data-page="{{ $pageName ?? '' }}">
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
                <a class="{{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
                <a class="{{ request()->is('ecosystem*') ? 'active' : '' }}" href="/ecosystem.php">Ecosystem</a>
                <a class="{{ request()->is('verification.php') ? 'active' : '' }}" href="/verification.php">Verification</a>
                <a class="{{ request()->is('opportunities*') ? 'active' : '' }}" href="/opportunities.php">Opportunities</a>
                <a class="{{ request()->is('about*') ? 'active' : '' }}" href="/about.php">About</a>
                <a class="{{ request()->is('contact*') ? 'active' : '' }}" href="/contact.php">Contact</a>
                <a class="{{ request()->is('verification-policy*') || request()->is('privacy*') || request()->is('terms*') ? 'active' : '' }}" href="/verification-policy.php">Policies</a>
            </nav>

            <div class="nav-actions">
                @auth
                    <a class="btn btn-soft hide-sm" href="/dashboard">
                        <i class="fa-solid fa-gauge-high"></i>
                        My Workspace
                    </a>
                    <a class="btn btn-primary" href="/logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Logout
                    </a>
                @else
                    <a class="btn btn-soft hide-sm" href="/login.php">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </a>
                    <a class="btn btn-primary" href="/register.php">
                        <i class="fa-solid fa-user-plus"></i>
                        Create Account
                    </a>
                @endauth

                <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <nav class="mobile-nav" id="mobileNav" aria-label="Mobile navigation">
            <a class="{{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
            <a class="{{ request()->is('ecosystem*') ? 'active' : '' }}" href="/ecosystem.php">Ecosystem</a>
            <a class="{{ request()->is('verification.php') ? 'active' : '' }}" href="/verification.php">Verification</a>
            <a class="{{ request()->is('opportunities*') ? 'active' : '' }}" href="/opportunities.php">Opportunities</a>
            <a class="{{ request()->is('about*') ? 'active' : '' }}" href="/about.php">About</a>
            <a class="{{ request()->is('contact*') ? 'active' : '' }}" href="/contact.php">Contact</a>
            <a class="{{ request()->is('verification-policy*') ? 'active' : '' }}" href="/verification-policy.php">Verification Policy</a>
            <a class="{{ request()->is('terms*') ? 'active' : '' }}" href="/terms.php">Terms of Use</a>
            <a class="{{ request()->is('privacy*') ? 'active' : '' }}" href="/privacy.php">Privacy Policy</a>

            @auth
                <a href="/dashboard">
                    <i class="fa-solid fa-gauge-high"></i>
                    My Workspace
                </a>
                <a href="/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            @else
                <a href="/login.php">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Login
                </a>
                <a href="/register.php">
                    <i class="fa-solid fa-user-plus"></i>
                    Create Account
                </a>
            @endauth
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-about">
                <a href="https://unidatechs.com" target="_blank" rel="noopener" class="footer-brand">
                    <span class="brand-mark small">
                        <span class="drop drop-blue"></span>
                        <span class="drop drop-green"></span>
                    </span>
                    <span>
                        <strong>UNIDA Gateway</strong>
                        <small>Investment Ecosystem Platform</small>
                    </span>
                </a>
                <p>
                    UNIDA Gateway supports verified business access, investment readiness,
                    stakeholder coordination and data-driven ecosystem insights for Tanzania and beyond.
                </p>
            </div>

            <div>
                <h4>Platform</h4>
                <a href="/">Home</a>
                <a href="/ecosystem.php">Ecosystem Flow</a>
                <a href="/opportunities.php">Opportunities</a>
                <a href="/contact.php">Contact</a>
            </div>

            <div>
                <h4>Legal & Trust</h4>
                <a href="/verification.php">UNIDA Verify</a>
                <a href="/verification-policy.php">Verification Policy</a>
                <a href="/terms.php">Terms of Use</a>
                <a href="/privacy.php">Privacy Policy</a>
            </div>

            <div>
                <h4>Contact</h4>
                <a href="https://unidatechs.com" target="_blank" rel="noopener">UNIDA TECH LIMITED</a>
                <a href="mailto:support@unidatechs.com">support@unidatechs.com</a>
                <a href="tel:+255762494775">0762 494 775</a>
                <a href="https://unidatechs.com" target="_blank" rel="noopener">About UNIDA</a>
            </div>
        </div>

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
    </script>

    <script src="{{ asset('assets/js/chatbot.js') }}"></script>
    <script src="{{ asset('assets/js/rieta-chatbot.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
