@extends('layouts.dashboard')

@php
    $pageTitle = 'Insights';
    $pageName = 'investor-insights';
    $activeSidebar = 'insights';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div><h3>Market Insights</h3><p>Published intelligence visible to investors.</p></div>
            </div>
            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Sector / Region</th>
                            <th>Summary</th>
                            <th>Published</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($insights ?? [] as $insight)
                        <tr>
                            <td>{{ $insight['title'] }}</td>
                            <td>{{ $insight['sector'] ?: 'General' }} / {{ $insight['region'] ?: 'N/A' }}</td>
                            <td>{{ $insight['summary'] ?: '-' }}</td>
                            <td>{{ $insight['published_at'] ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No published insights yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
