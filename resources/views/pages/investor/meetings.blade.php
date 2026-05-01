@extends('layouts.dashboard')

@php
    $pageTitle = 'Meetings';
    $pageName = 'investor-meetings';
    $activeSidebar = 'meetings';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div><h3>Meeting Requests</h3><p>Opportunities currently at meeting request stage.</p></div>
            </div>
            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Opportunity</th>
                            <th>Business</th>
                            <th>Status</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($meetings ?? [] as $meeting)
                        <tr>
                            <td>{{ $meeting['title'] }}</td>
                            <td>{{ $meeting['business_name'] }}</td>
                            <td><span class="status-badge status-open">{{ ucwords(str_replace('_', ' ', $meeting['status'])) }}</span></td>
                            <td>{{ $meeting['updated_at'] ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No meeting requests yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
