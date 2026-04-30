@extends('layouts.dashboard')

@php
    $pageTitle = 'Investor Dashboard';
    $pageName = 'investor-dashboard';
    $activeSidebar = 'overview';
@endphp

@section('content')
<section class="dashboard-hero investor">
    <div class="container dashboard-hero-grid">
        <div>
            <div class="page-kicker"><i class="fa-solid fa-coins"></i> Investor Workspace</div>
            <h1>Discover verified opportunities and manage your investment pipeline.</h1>
            <p>Review verified businesses, shortlist opportunities, schedule meetings and track deal flow.</p>
        </div>
        <div class="dashboard-profile-card">
            <div class="profile-avatar investor-avatar">{{ strtoupper(substr((string)($_SESSION['user_name'] ?? 'IN'), 0, 2)) }}</div>
            <div>
                <h3>{{ $_SESSION['user_name'] ?? 'Investor' }}</h3>
                <p>Investor Account</p>
                <span class="status-badge status-verified"><i class="fa-solid fa-circle-check"></i> Active</span>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-content">
            <div class="dashboard-stat-grid">
                <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-briefcase"></i></span><div><strong>0</strong><small>Published opportunities</small></div></article>
                <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-circle-check"></i></span><div><strong>0</strong><small>Verified opportunities</small></div></article>
                <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-bookmark"></i></span><div><strong>0</strong><small>Shortlisted</small></div></article>
                <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-calendar-check"></i></span><div><strong>0</strong><small>Meetings</small></div></article>
            </div>

            <article class="dashboard-panel">
                <div class="panel-head">
                    <div><h3>Investor Actions</h3><p>Quick actions for investor workflow.</p></div>
                    <span class="status-badge status-progress">Workspace</span>
                </div>
                <div class="cards-grid two-columns">
                    <article class="info-card"><div class="icon-box"><i class="fa-solid fa-magnifying-glass-chart"></i></div><h3>Discover</h3><p>Find verified opportunities.</p><a class="btn btn-soft" href="/investor/discover.php">Open</a></article>
                    <article class="info-card"><div class="icon-box"><i class="fa-solid fa-bookmark"></i></div><h3>Shortlist</h3><p>Save opportunities for review.</p><a class="btn btn-soft" href="/investor/shortlist.php">Open</a></article>
                </div>
            </article>
        </div>
    </div>
</section>
@endsection
