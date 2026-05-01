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
            <div class="dashboard-table-wrap" style="width:100%;">
                <table class="dashboard-table" style="width:100%;">
                    <thead><tr><th>Title</th><th>Sector/Region</th><th>Opportunity Stage</th><th>Funding</th><th>Document</th><th>Status</th><th style="min-width:260px;">Pipeline Stage</th><th>Project</th></tr></thead>
                    <tbody>
                    @forelse($shortlist as $item)
                        <tr>
                            <td>{{ $item['title'] }}</td>
                            <td>{{ $item['sector'] ?: 'General' }} / {{ $item['region'] ?: 'N/A' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $item['stage'] ?? 'mvp')) }}</td>
                            <td>{{ $item['currency'] }} {{ number_format($item['funding_amount'], 2) }}</td>
                            <td>
                                @if(!empty($item['document_path']))
                                    <a class="btn btn-soft" href="{{ asset('storage/'.$item['document_path']) }}" target="_blank" rel="noopener">
                                        {{ $item['document_name'] ?: 'View Document' }}
                                    </a>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td><span class="status-badge status-open">{{ ucwords(str_replace('_', ' ', $item['status'])) }}</span></td>
                            <td style="min-width:260px;">
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
                            <td>
                                <form method="post" action="{{ route('investor.shortlist.accept') }}">
                                    @csrf
                                    <input type="hidden" name="opportunity_id" value="{{ $item['opportunity_id'] }}">
                                    <button class="btn btn-primary" type="submit">Accept</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No shortlisted opportunities yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
