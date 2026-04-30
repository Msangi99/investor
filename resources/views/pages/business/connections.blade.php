@extends('layouts.dashboard')

@php
    $pageTitle = 'Business Connections';
    $pageName = 'business-connections';
    $activeSidebar = 'connections';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Connections</h3>
                    <p>Send connection requests to investors, stakeholders and support partners.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="form-alert form-alert-success" style="margin-bottom:14px;"><p>{{ session('success') }}</p></div>
            @endif
            @if($errors->any())
                <div class="form-alert form-alert-error" style="margin-bottom:14px;"><p>{{ $errors->first() }}</p></div>
            @endif

            <form method="post" action="{{ route('business.connections.save') }}" class="form-grid two" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <small>Contact</small>
                    <select class="form-control" name="receiver_user_id">
                        <option value="">Select contact</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ ucfirst($contact['role']) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Opportunity</small>
                    <select class="form-control" name="opportunity_id">
                        <option value="">General connection</option>
                        @foreach($opportunities as $opportunity)
                            <option value="{{ $opportunity['id'] }}">{{ $opportunity['title'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Connection Type *</small>
                    <select class="form-control" name="connection_type" required>
                        @foreach($connection_types as $type)
                            <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Subject *</small>
                    <input class="form-control" type="text" name="subject" required>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Message</small>
                    <textarea class="form-control" name="message" rows="3"></textarea>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Send Request</button>
                </div>
            </form>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Opportunity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($connections as $connection)
                        <tr>
                            <td>{{ $connection['subject'] }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $connection['type'])) }}</td>
                            <td>{{ $connection['receiver_name'] }}</td>
                            <td>{{ $connection['opportunity_title'] }}</td>
                            <td>
                                <span class="status-badge status-open">{{ ucwords(str_replace('_', ' ', $connection['status'])) }}</span>
                                @if(!empty($connection['is_incoming']) && $connection['status'] === 'pending')
                                    <div style="display:flex; gap:8px; margin-top:8px;">
                                        <form method="post" action="{{ route('business.connections.status') }}">
                                            @csrf
                                            <input type="hidden" name="connection_id" value="{{ $connection['id'] }}">
                                            <input type="hidden" name="status" value="accepted">
                                            <button class="btn btn-soft" type="submit">Accept</button>
                                        </form>
                                        <form method="post" action="{{ route('business.connections.status') }}">
                                            @csrf
                                            <input type="hidden" name="connection_id" value="{{ $connection['id'] }}">
                                            <input type="hidden" name="status" value="declined">
                                            <button class="btn btn-soft" type="submit">Decline</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No connection requests yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>

@endsection
