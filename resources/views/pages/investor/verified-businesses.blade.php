@extends('layouts.dashboard')

@php
    $pageTitle = 'Verified Businesses';
    $pageName = 'investor-verified-businesses';
    $activeSidebar = 'verified-businesses';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div><h3>Verified Businesses</h3><p>Businesses approved through platform verification.</p></div>
            </div>
            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Sector / Region</th>
                            <th>Readiness</th>
                            <th>Active Opportunities</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($businesses ?? [] as $business)
                        <tr>
                            <td>{{ $business['business_name'] }}</td>
                            <td>{{ $business['sector'] ?: 'General' }} / {{ $business['region'] ?: 'N/A' }}</td>
                            <td>{{ (int) $business['readiness_score'] }}%</td>
                            <td>{{ (int) $business['active_opportunities'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No verified businesses available yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
