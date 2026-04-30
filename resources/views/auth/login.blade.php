<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | UNIDA Gateway</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/legal.css">
</head>
<body data-page="login">
    <div class="topbar">
        <div class="container topbar-inner">
            <span><i class="fa-solid fa-shield-halved"></i> UNIDA Gateway: verification, readiness and stakeholder coordination</span>
            <span><i class="fa-solid fa-location-dot"></i> Built in Tanzania by UNIDA TECH LIMITED</span>
        </div>
    </div>

    <header class="site-header">
        <div class="container nav-wrap">
            <a href="/index.php" class="brand" aria-label="UNIDA Gateway">
                <span class="brand-mark"><span class="drop drop-blue"></span><span class="drop drop-green"></span></span>
                <span class="brand-text"><strong>UNIDA Gateway</strong><small>Investment Ecosystem Platform</small></span>
            </a>
            <nav class="desktop-nav" aria-label="Primary navigation">
                <a href="/index.php">Home</a>
                <a href="/ecosystem.php">Ecosystem</a>
                <a href="/verification.php">Verification</a>
                <a href="/opportunities.php">Opportunities</a>
                <a href="/about.php">About</a>
                <a href="/contact.php">Contact</a>
            </nav>
            <div class="nav-actions">
                <a class="btn btn-primary" href="/register.php"><i class="fa-solid fa-user-plus"></i> Create Account</a>
            </div>
        </div>
    </header>

    <main class="auth-section">
        <div class="container">
            <form class="auth-card form-grid" method="post" action="/login.php" autocomplete="on">
                @csrf
                <div>
                    <div class="page-kicker"><i class="fa-solid fa-lock"></i> Secure Access</div>
                    <h1>Login to your workspace.</h1>
                    <p class="auth-note">Access your workspace based on your account role and permissions.</p>
                </div>

                @if ($errors->any())
                    <div class="form-alert form-alert-error">
                        <p>{{ $errors->first() }}</p>
                    </div>
                @endif

                <div class="form-group">
                    <label>Email Address</label>
                    <input class="form-control" type="email" name="email" placeholder="Email address" value="{{ old('email') }}" autocomplete="email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-field">
                        <input class="form-control" id="loginPassword" type="password" name="password" placeholder="Password" autocomplete="current-password" required>
                        <button class="password-toggle" type="button" data-password-toggle="#loginPassword" aria-label="Show or hide password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </button>

                <p class="auth-note">
                    No account yet?
                    <a href="/register.php" style="color:var(--primary-blue);font-weight:800;">Create account</a>
                </p>
            </form>
        </div>
    </main>

    <script src="/assets/js/main.js"></script>
</body>
</html>
