@extends('layouts.dashboard')

@php
    $pageTitle = 'Business Insights';
    $pageName = 'business-insights';
    $activeSidebar = 'insights';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-content">
            <div class="dashboard-stat-grid">
                <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-briefcase"></i></span><div><strong>{{ $my_stats['requests'] }}</strong><small>Investment requests</small></div></article>
                <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-handshake"></i></span><div><strong>{{ $my_stats['connections'] }}</strong><small>Connection requests</small></div></article>
                <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-envelope"></i></span><div><strong>{{ $my_stats['messages'] }}</strong><small>Unread messages</small></div></article>
                <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-chart-line"></i></span><div><strong>{{ count($metrics) }}</strong><small>Market metrics</small></div></article>
            </div>

            <article class="dashboard-panel">
                <div class="panel-head">
                    <div>
                        <h3>Market Metrics</h3>
                        <p>Latest high-level ecosystem indicators.</p>
                    </div>
                </div>
                <div class="cards-grid three-columns">
                    @forelse($metrics as $metric)
                        <article class="info-card">
                            <h3>{{ $metric['name'] }}</h3>
                            <p><strong>{{ number_format($metric['value'], 2) }}</strong> {{ $metric['unit'] }}</p>
                        </article>
                    @empty
                        <p>No metrics published yet.</p>
                    @endforelse
                </div>
            </article>

            <article class="dashboard-panel">
                <div class="panel-head">
                    <div>
                        <h3>Latest Insights</h3>
                        <p>Published updates relevant for business and investment preparation.</p>
                    </div>
                </div>
                <div class="cards-grid">
                    @forelse($latest_insights as $insight)
                        <article class="info-card">
                            <h3>{{ $insight['title'] }}</h3>
                            <p>{{ $insight['summary'] ?: 'No summary provided.' }}</p>
                            <span class="status-badge status-progress">
                                {{ $insight['sector'] ?: 'General' }} / {{ $insight['region'] ?: 'All regions' }}
                            </span>
                        </article>
                    @empty
                        <p>No published insights available right now.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </div>
</section>

@endsection
