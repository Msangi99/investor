@extends('layouts.dashboard')

@php
    $pageTitle = 'Admin Dashboard';
    $pageName = 'admin-dashboard';
    $activeSidebar = 'overview';
@endphp

@section('content')
<section class="dashboard-hero admin">
    <div class="container dashboard-hero-grid">
        <div>
            <div class="page-kicker"><i class="fa-solid fa-user-shield"></i> Admin Control Center</div>
            <h1>Manage users, verification, roles, opportunities and ecosystem activity.</h1>
            <p>Control core system modules, user management, AI tools, legal pages and reporting visibility.</p>
        </div>
        <div class="dashboard-profile-card">
            <div class="profile-avatar admin-avatar">{{ strtoupper(substr((string)($_SESSION['user_name'] ?? 'AD'), 0, 2)) }}</div>
            <div>
                <h3>{{ $_SESSION['user_name'] ?? 'Administrator' }}</h3>
                <p>{{ \App\Support\LegacyRoleMatrix::normalizeRole((string)($_SESSION['user_role'] ?? 'SUPER_ADMIN')) }}</p>
                <span class="status-badge status-verified"><i class="fa-solid fa-user-lock"></i> Admin Access</span>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-content">
            <div class="dashboard-stat-grid">
                <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-users"></i></span><div><strong>0</strong><small>Total users</small></div></article>
                <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-building"></i></span><div><strong>0</strong><small>Businesses</small></div></article>
                <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-coins"></i></span><div><strong>0</strong><small>Investors</small></div></article>
                <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-users-gear"></i></span><div><strong>0</strong><small>Stakeholders</small></div></article>
            </div>

            <article class="dashboard-panel">
                <div class="panel-head">
                    <div><h3>Admin Quick Actions</h3><p>Jump to the most used control modules.</p></div>
                    <span class="status-badge status-open">System</span>
                </div>
                <div class="cards-grid three-columns">
                    <article class="info-card"><div class="icon-box"><i class="fa-solid fa-users"></i></div><h3>Users</h3><p>Manage platform user accounts.</p><a class="btn btn-soft" href="/admin/user.php">Open</a></article>
                    <article class="info-card"><div class="icon-box"><i class="fa-solid fa-user-lock"></i></div><h3>Roles</h3><p>Configure roles and permissions.</p><a class="btn btn-soft" href="/admin/roles.php">Open</a></article>
                    <article class="info-card"><div class="icon-box"><i class="fa-solid fa-file-shield"></i></div><h3>Verifications</h3><p>Review verification workflow.</p><a class="btn btn-soft" href="/admin/verifications.php">Open</a></article>
                </div>
            </article>
        </div>
    </div>
</section>
@endsection
