@extends('layouts.dashboard')

@php
    $pageTitle = 'Business Readiness';
    $pageName = 'business-readiness';
    $activeSidebar = 'readiness';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Readiness Checklist</h3>
                    <p>Track your preparation level before investment and verification review.</p>
                </div>
                <span class="status-badge status-open">{{ $score }}% complete</span>
            </div>

            @if(session('success'))
                <div class="form-alert form-alert-success" style="margin-bottom:14px;">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="dashboard-stat-grid" style="margin-bottom:16px;">
                <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-list-check"></i></span><div><strong>{{ $completed_count }}</strong><small>Completed items</small></div></article>
                <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-chart-line"></i></span><div><strong>{{ count($items) }}</strong><small>Total checklist items</small></div></article>
            </div>

            <form method="post" action="{{ route('business.readiness.save') }}">
                @csrf
                <div class="cards-grid two-columns">
                    @foreach($items as $item)
                        <label class="info-card readiness-item">
                            <div class="icon-box"><i class="fa-solid fa-check"></i></div>
                            <h3>{{ $item['name'] }}</h3>
                            <p>{{ $item['description'] }}</p>
                            <span class="readiness-check">
                                <input type="checkbox" name="completed_items[]" value="{{ $item['id'] }}" @checked(($item['status'] ?? '') === 'completed')>
                                Mark as completed
                            </span>
                        </label>
                    @endforeach
                </div>

                <div style="margin-top:16px;">
                    <button type="submit" class="btn btn-primary">Save Readiness Progress</button>
                </div>
            </form>
        </article>
    </div>
</section>

@endsection
