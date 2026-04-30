@extends('layouts.dashboard')

@php
    $pageTitle = 'Stakeholder Dashboard';
    $pageName = 'stakeholder-dashboard';
    $activeSidebar = 'overview';
@endphp

@section('content')
<section class="dashboard-hero stakeholder">
    <div class="container dashboard-hero-grid">
        <div>
            <div class="page-kicker"><i class="fa-solid fa-building-columns"></i> Stakeholder Workspace</div>
            <h1>Coordinate support, recommendations and ecosystem connections.</h1>
            <p>Review businesses, send recommendations, manage connections, reports and follow-up actions.</p>
        </div>
        <div class="dashboard-profile-card">
            <div class="profile-avatar">{{ strtoupper(substr((string)($_SESSION['user_name'] ?? 'ST'), 0, 2)) }}</div>
            <div>
                <h3>{{ $_SESSION['user_name'] ?? 'Stakeholder' }}</h3>
                <p>Stakeholder Account</p>
                <span class="status-badge status-progress"><i class="fa-solid fa-spinner"></i> Active</span>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-content">
            <div class="dashboard-stat-grid">
                <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-building"></i></span><div><strong>0</strong><small>Total businesses</small></div></article>
                <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-circle-check"></i></span><div><strong>0</strong><small>Verified businesses</small></div></article>
                <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-handshake-angle"></i></span><div><strong>0</strong><small>Recommendations</small></div></article>
                <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-file-lines"></i></span><div><strong>0</strong><small>Reports</small></div></article>
            </div>

            <article class="dashboard-panel">
                <div class="panel-head">
                    <div><h3>Stakeholder Actions</h3><p>Quick actions for ecosystem coordination.</p></div>
                    <span class="status-badge status-open">Workspace</span>
                </div>
                <div class="cards-grid two-columns">
                    <article class="info-card"><div class="icon-box"><i class="fa-solid fa-building"></i></div><h3>Review Businesses</h3><p>View businesses needing support.</p><a class="btn btn-soft" href="/stakeholder/businesses.php">Open</a></article>
                    <article class="info-card"><div class="icon-box"><i class="fa-solid fa-handshake-angle"></i></div><h3>Recommendations</h3><p>Send support or referral recommendations.</p><a class="btn btn-soft" href="/stakeholder/recommendations.php">Open</a></article>
                </div>
            </article>
        </div>
    </div>
</section>
@endsection
