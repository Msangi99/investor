@extends('layouts.dashboard')

@php
    $pageTitle = 'Stakeholder Recommendations';
    $pageName = 'stakeholder-recommendations';
    $activeSidebar = 'recommendations';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        @if(session('success'))
            <div class="form-alert form-alert-success"><p>{{ session('success') }}</p></div>
        @endif
        @if($errors->any())
            <div class="form-alert form-alert-error"><p>{{ $errors->first() }}</p></div>
        @endif

        <article class="dashboard-panel">
            <div class="panel-head"><div><h3>Create Recommendation</h3><p>Link an investor with a business support opportunity.</p></div></div>
            <form method="post" action="{{ route('stakeholder.recommendations.save') }}" class="form-grid two" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <small>Investor *</small>
                    <select class="form-control" name="investor_user_id" required>
                        <option value="">Select investor</option>
                        @foreach($investors as $investor)
                            <option value="{{ $investor['id'] }}">{{ $investor['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Business (reference)</small>
                    <select class="form-control" name="business_profile_id">
                        <option value="">Select business</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business['id'] }}">{{ $business['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Subject *</small>
                    <input class="form-control" type="text" name="subject" required>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Recommendation Message</small>
                    <textarea class="form-control" name="message" rows="3"></textarea>
                </div>
                <div><button class="btn btn-primary" type="submit">Submit Recommendation</button></div>
            </form>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead><tr><th>Subject</th><th>Investor</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recommendations as $recommendation)
                        <tr>
                            <td>{{ $recommendation['subject'] }}</td>
                            <td>{{ $recommendation['investor_name'] }}</td>
                            <td><span class="status-badge status-open">{{ ucwords(str_replace('_', ' ', $recommendation['status'])) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No recommendations submitted yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
