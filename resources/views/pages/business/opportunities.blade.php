@extends('layouts.dashboard')

@php
    $pageTitle = 'Investment Requests';
    $pageName = 'business-opportunities';
    $activeSidebar = 'opportunities';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Investment Requests</h3>
                    <p>Create and manage opportunities for potential investors.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="form-alert form-alert-success" style="margin-bottom:14px;">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="form-alert form-alert-error" style="margin-bottom:14px;">
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="post" action="{{ route('business.opportunities.save') }}" class="form-grid two" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <small>Title *</small>
                    <input class="form-control" type="text" name="title" value="{{ old('title') }}" required>
                </div>
                <div class="form-group">
                    <small>Sector</small>
                    <input class="form-control" type="text" name="sector" value="{{ old('sector') }}">
                </div>
                <div class="form-group">
                    <small>Region</small>
                    <input class="form-control" type="text" name="region" value="{{ old('region') }}">
                </div>
                <div class="form-group">
                    <small>Funding Amount</small>
                    <input class="form-control" type="number" name="funding_amount" min="0" step="0.01" value="{{ old('funding_amount') }}">
                </div>
                <div class="form-group">
                    <small>Currency</small>
                    <input class="form-control" type="text" name="currency" value="{{ old('currency', 'TZS') }}">
                </div>
                <div class="form-group">
                    <small>Funding Type *</small>
                    <select class="form-control" name="funding_type" required>
                        @foreach($funding_types as $type)
                            <option value="{{ $type }}" @selected(old('funding_type', 'equity') === $type)>{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Business Stage *</small>
                    <select class="form-control" name="stage" required>
                        @foreach($stages as $stage)
                            <option value="{{ $stage }}" @selected(old('stage', 'mvp') === $stage)>{{ ucwords(str_replace('_', ' ', $stage)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Status *</small>
                    <select class="form-control" name="status" required>
                        <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                        <option value="published" @selected(old('status') === 'published')>Published</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Summary *</small>
                    <textarea class="form-control" name="summary" rows="4" required>{{ old('summary') }}</textarea>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Save Request</button>
                </div>
            </form>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Sector/Region</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                @forelse($opportunities as $opportunity)
                        <tr>
                            <td>{{ $opportunity['title'] }}</td>
                            <td>{{ $opportunity['sector'] ?: 'General' }} / {{ $opportunity['region'] ?: 'N/A' }}</td>
                            <td>{{ $opportunity['currency'] }} {{ number_format((float) ($opportunity['funding_amount'] ?? 0), 2) }}</td>
                            <td><span class="status-badge status-open">{{ ucfirst($opportunity['status']) }}</span></td>
                        </tr>
                @empty
                        <tr>
                            <td colspan="4">No investment requests yet.</td>
                        </tr>
                @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>

@endsection
