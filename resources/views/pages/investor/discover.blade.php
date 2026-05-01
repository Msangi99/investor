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
            <div class="dashboard-table-wrap" style="width:100%;">
                <table class="dashboard-table" style="width:100%;">
                    <thead><tr><th>Title</th><th>Business</th><th>Sector/Region</th><th>Stage</th><th>Funding</th><th>Document</th><th>Action</th></tr></thead>
                    <tbody>
                    @forelse($opportunities as $opportunity)
                        <tr>
                            <td>{{ $opportunity['title'] }}</td>
                            <td>{{ $opportunity['business_name'] }}</td>
                            <td>{{ $opportunity['sector'] ?: 'General' }} / {{ $opportunity['region'] ?: 'N/A' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $opportunity['stage'] ?? 'mvp')) }}</td>
                            <td>{{ $opportunity['currency'] }} {{ number_format($opportunity['funding_amount'], 2) }}</td>
                            <td>
                                @if(!empty($opportunity['document_path']))
                                    <a class="btn btn-soft" href="{{ asset('storage/'.$opportunity['document_path']) }}" target="_blank" rel="noopener">
                                        {{ $opportunity['document_name'] ?: 'View Document' }}
                                    </a>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    @if($opportunity['is_shortlisted'] ?? false)
                                        <span class="status-badge status-verified">Saved in Shortlist</span>
                                    @else
                                        <form method="post" action="{{ route('investor.discover.shortlist') }}">
                                            @csrf
                                            <input type="hidden" name="opportunity_id" value="{{ $opportunity['id'] }}">
                                            <button class="btn btn-soft" type="submit">Save to Shortlist</button>
                                        </form>
                                    @endif
                                    <form method="post" action="{{ route('investor.shortlist.accept') }}">
                                        @csrf
                                        <input type="hidden" name="opportunity_id" value="{{ $opportunity['id'] }}">
                                        <button class="btn btn-primary" type="submit">Accept Project</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No verified opportunities published yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
