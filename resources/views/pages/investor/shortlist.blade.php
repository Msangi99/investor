@extends('layouts.dashboard')

@php
    $pageTitle = 'Shortlist';
    $pageName = 'investor-shortlist';
    $activeSidebar = 'shortlist';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        @if(session('success'))
            <div class="form-alert form-alert-success"><p>{{ session('success') }}</p></div>
        @endif
        <article class="dashboard-panel">
            <div class="panel-head"><div><h3>Shortlisted Opportunities</h3><p>Move each deal through pipeline stages.</p></div></div>
            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead><tr><th>Title</th><th>Sector/Region</th><th>Funding</th><th>Status</th><th>Stage</th></tr></thead>
                    <tbody>
                    @forelse($shortlist as $item)
                        <tr>
                            <td>{{ $item['title'] }}</td>
                            <td>{{ $item['sector'] ?: 'General' }} / {{ $item['region'] ?: 'N/A' }}</td>
                            <td>{{ $item['currency'] }} {{ number_format($item['funding_amount'], 2) }}</td>
                            <td><span class="status-badge status-open">{{ ucwords(str_replace('_', ' ', $item['status'])) }}</span></td>
                            <td>
                                <form method="post" action="{{ route('investor.shortlist.stage') }}" style="display:flex; gap:8px;">
                                    @csrf
                                    <input type="hidden" name="shortlist_id" value="{{ $item['id'] }}">
                                    <select class="form-control" name="status">
                                        @foreach(['saved','interested','contacted','meeting_requested','in_review','not_interested'] as $stage)
                                            <option value="{{ $stage }}" @selected($item['status'] === $stage)>{{ ucwords(str_replace('_', ' ', $stage)) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-soft" type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No shortlisted opportunities yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
