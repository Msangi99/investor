@extends('layouts.dashboard')

@php
    $pageTitle = 'Investment Pipeline';
    $pageName = 'investor-pipeline';
    $activeSidebar = 'pipeline';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <div class="cards-grid two-columns">
            @foreach(['reviewing' => 'Reviewing', 'contacted' => 'Contacted', 'meeting' => 'Meeting', 'decided' => 'Decided'] as $key => $label)
                <article class="dashboard-panel">
                    <div class="panel-head"><div><h3>{{ $label }}</h3></div></div>
                    <div class="pipeline-list">
                        @forelse($pipeline[$key] as $item)
                            <div><span>{{ $item['title'] }}</span><strong>{{ $item['currency'] }} {{ number_format($item['funding_amount'], 0) }}</strong></div>
                        @empty
                            <div><span>No items</span><strong>-</strong></div>
                        @endforelse
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
