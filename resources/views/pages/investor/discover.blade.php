@extends('layouts.dashboard')

@php
    $pageTitle = 'Discover Opportunities';
    $pageName = 'investor-discover';
    $activeSidebar = 'discover';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        @if(session('success'))
            <div class="form-alert form-alert-success"><p>{{ session('success') }}</p></div>
        @endif
        <article class="dashboard-panel">
            <div class="panel-head"><div><h3>Verified Opportunities</h3><p>Only reviewed and published business requests are shown.</p></div></div>
            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead><tr><th>Title</th><th>Business</th><th>Sector/Region</th><th>Funding</th><th>Action</th></tr></thead>
                    <tbody>
                    @forelse($opportunities as $opportunity)
                        <tr>
                            <td>{{ $opportunity['title'] }}</td>
                            <td>{{ $opportunity['business_name'] }}</td>
                            <td>{{ $opportunity['sector'] ?: 'General' }} / {{ $opportunity['region'] ?: 'N/A' }}</td>
                            <td>{{ $opportunity['currency'] }} {{ number_format($opportunity['funding_amount'], 2) }}</td>
                            <td>
                                <form method="post" action="{{ route('investor.discover.shortlist') }}">
                                    @csrf
                                    <input type="hidden" name="opportunity_id" value="{{ $opportunity['id'] }}">
                                    <button class="btn btn-soft" type="submit">Save to Shortlist</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No verified opportunities published yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
