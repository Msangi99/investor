@extends('layouts.dashboard')

@php
    $pageTitle = 'My Projects';
    $pageName = 'investor-my-projects';
    $activeSidebar = 'my-projects';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        @if(session('success'))
            <div class="form-alert form-alert-success"><p>{{ session('success') }}</p></div>
        @endif

        <div class="cards-grid two-columns">
            <article class="dashboard-panel">
                <div class="panel-head">
                    <div><h3>Accepted Projects</h3><p>Projects you accepted from opportunities.</p></div>
                </div>
                <div class="dashboard-table-wrap" style="width:100%;">
                    <table class="dashboard-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Business</th>
                                <th>Stage</th>
                                <th>Funding</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>{{ $project['title'] }}</td>
                                <td>{{ $project['business_name'] }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $project['stage'] ?? 'mvp')) }}</td>
                                <td>{{ $project['currency'] }} {{ number_format((float) $project['funding_amount'], 2) }}</td>
                                <td><span class="status-badge status-open">{{ ucwords(str_replace('_', ' ', $project['status'])) }}</span></td>
                                <td><a class="btn btn-soft" href="{{ route('investor.my-projects', ['shortlist_id' => $project['id']]) }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No accepted projects yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="dashboard-panel">
                <div class="panel-head">
                    <div><h3>Project Details</h3><p>Business contact and proposal information.</p></div>
                </div>
                @if($selected_project)
                    <div class="pipeline-list">
                        <div><span>Project</span><strong>{{ $selected_project['title'] }}</strong></div>
                        <div><span>Description</span><strong>{{ $selected_project['summary'] ?: '-' }}</strong></div>
                        <div><span>Business</span><strong>{{ $selected_project['business_name'] }}</strong></div>
                        <div><span>Sector / Region</span><strong>{{ $selected_project['sector'] ?: 'General' }} / {{ $selected_project['region'] ?: 'N/A' }}</strong></div>
                        <div><span>Funding Request</span><strong>{{ $selected_project['currency'] }} {{ number_format((float) $selected_project['funding_amount'], 2) }}</strong></div>
                    </div>

                    <h4 style="margin-top:14px;">Business Contact</h4>
                    <div class="pipeline-list">
                        <div><span>Contact Name</span><strong>{{ $business_contact->full_name ?? '-' }}</strong></div>
                        <div><span>Email</span><strong>{{ $business_contact->email ?? '-' }}</strong></div>
                        <div><span>Phone</span><strong>{{ $business_contact->phone ?? '-' }}</strong></div>
                        <div><span>Organization</span><strong>{{ $business_contact->organization ?? '-' }}</strong></div>
                    </div>
                @else
                    <p class="auth-note">Select an accepted project to view details and business contact information.</p>
                @endif
            </article>
        </div>
    </div>
</section>
@endsection

