@extends('layouts.dashboard')

@php
    $pageTitle = 'Business Dashboard';
    $pageName = 'business-dashboard';
    $activeSidebar = 'overview';
    $stats = $dashboard['stats'] ?? [];
    $nextActions = $dashboard['next_actions'] ?? [];
@endphp

@section('content')
<section class="dashboard-hero">
    <div class="container dashboard-hero-grid">
        <div>
            <div class="page-kicker"><i class="fa-solid fa-briefcase"></i> Business Workspace</div>
            <h1>Manage your business readiness and verification journey.</h1>
            <p>Complete your profile, upload required documents, improve readiness and publish opportunities after approval.</p>
        </div>
        <div class="dashboard-profile-card">
            <div class="profile-avatar">{{ strtoupper(substr((string)($userName ?? 'BU'), 0, 2)) }}</div>
            <div>
                <h3>{{ $userName ?? 'Business User' }}</h3>
                <p>Business Account</p>
                <span class="status-badge status-progress"><i class="fa-solid fa-spinner"></i> {{ $dashboard['status_label'] ?? 'Active' }}</span>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-content">
            <div class="dashboard-stat-grid">
                <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-chart-line"></i></span><div><strong>{{ (int) ($stats['readiness_score'] ?? 0) }}%</strong><small>Readiness score</small></div></article>
                <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-file-circle-check"></i></span><div><strong>{{ (int) ($stats['approved_documents'] ?? 0) }}</strong><small>Approved documents</small></div></article>
                <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-briefcase"></i></span><div><strong>{{ (int) ($stats['investment_requests'] ?? 0) }}</strong><small>Investment requests</small></div></article>
                <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-message"></i></span><div><strong>{{ (int) ($stats['unread_messages'] ?? 0) }}</strong><small>Unread messages</small></div></article>
            </div>

            <article class="dashboard-panel">
                <div class="panel-head">
                    <div><h3>Next Actions</h3><p>Recommended steps for your business account.</p></div>
                    <span class="status-badge status-open">{{ collect($nextActions)->where('completed', true)->count() }} completed</span>
                </div>
                <div class="cards-grid three-columns">
                    @foreach($nextActions as $action)
                        <article class="info-card">
                            <div class="icon-box"><i class="fa-solid fa-{{ $action['icon'] ?? 'circle' }}"></i></div>
                            <h3>{{ $action['title'] ?? 'Action' }}</h3>
                            <p>{{ $action['description'] ?? '' }}</p>
                            <a class="btn btn-soft" href="{{ $action['href'] ?? '#' }}">
                                {{ !empty($action['completed']) ? 'Review' : 'Open' }}
                            </a>
                        </article>
                    @endforeach
                </div>
            </article>
        </div>
    </div>
</section>
@endsection
