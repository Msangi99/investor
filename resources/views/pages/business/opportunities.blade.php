@extends('layouts.dashboard')

@php
    $pageTitle = 'Investment Requests';
    $pageName = 'business-opportunities';
    $activeSidebar = 'opportunities';
    $editing = $edit_opportunity ?? null;
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Project Proposal</h3>
                    <p>Create opportunity proposals with document attachments for Super Admin verification.</p>
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

            <form method="post" action="{{ route('business.opportunities.save') }}" enctype="multipart/form-data" class="form-grid two" style="margin-bottom:20px;">
                @csrf
                <input type="hidden" name="opportunity_id" value="{{ old('opportunity_id', $editing['id'] ?? '') }}">
                <div class="form-group">
                    <small>Project Name *</small>
                    <input class="form-control" type="text" name="title" value="{{ old('title', $editing['title'] ?? '') }}" required>
                </div>
                <div class="form-group">
                    <small>Fund Amount Requested</small>
                    <input class="form-control" type="number" name="funding_amount" min="0" step="0.01" value="{{ old('funding_amount', $editing['funding_amount'] ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Currency</small>
                    <input class="form-control" type="text" name="currency" value="{{ old('currency', $editing['currency'] ?? 'TZS') }}">
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Description *</small>
                    <textarea class="form-control" name="summary" rows="4" required>{{ old('summary', $editing['summary'] ?? '') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Proposal Document {{ $editing ? '(optional when editing)' : '*' }}</small>
                    <input class="form-control" type="file" name="document" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" {{ $editing ? '' : 'required' }}>
                    <small>Accepted: PDF, DOC, DOCX, PNG, JPG (max 10MB).</small>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">{{ $editing ? 'Update Proposal' : 'Submit Proposal' }}</button>
                    @if($editing)
                        <a class="btn btn-soft" href="{{ route('business.opportunities') }}">Cancel Edit</a>
                    @endif
                </div>
            </form>

            <div class="dashboard-table-wrap" style="width:100%;">
                <table class="dashboard-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Fund Request</th>
                            <th>Stage</th>
                            <th>Submitted</th>
                            <th>Verification</th>
                            <th>Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                @forelse($opportunities as $opportunity)
                        <tr>
                            <td>{{ $opportunity['title'] }}</td>
                            <td>{{ $opportunity['currency'] }} {{ number_format((float) ($opportunity['funding_amount'] ?? 0), 2) }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $opportunity['stage'] ?? 'mvp')) }}</td>
                            <td>{{ $opportunity['created_at'] ?: '-' }}</td>
                            <td><span class="status-badge status-progress">{{ ucwords(str_replace('_', ' ', $opportunity['verification_status'])) }}</span></td>
                            <td>
                                @if($opportunity['is_active'])
                                    <span class="status-badge status-verified">Active</span>
                                @else
                                    <span class="status-badge status-open">Pending Admin Verification</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-soft" href="{{ route('business.opportunities', ['edit' => $opportunity['id']]) }}">Edit</a>
                            </td>
                        </tr>
                @empty
                        <tr>
                            <td colspan="7">No opportunities submitted yet.</td>
                        </tr>
                @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>

@endsection
