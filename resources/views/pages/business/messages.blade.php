@extends('layouts.dashboard')

@php
    $pageTitle = 'Business Messages';
    $pageName = 'business-messages';
    $activeSidebar = 'messages';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Messages</h3>
                    <p>Send updates and track incoming communication from your network.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="form-alert form-alert-success" style="margin-bottom:14px;"><p>{{ session('success') }}</p></div>
            @endif
            @if($errors->any())
                <div class="form-alert form-alert-error" style="margin-bottom:14px;"><p>{{ $errors->first() }}</p></div>
            @endif

            <form method="post" action="{{ route('business.messages.save') }}" class="form-grid two" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <small>Recipient *</small>
                    <select class="form-control" name="receiver_user_id" required>
                        <option value="">Select recipient</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact['id'] }}">{{ $contact['name'] }} ({{ $contact['email'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Related Connection</small>
                    <select class="form-control" name="connection_id">
                        <option value="">No connection selected</option>
                        @foreach($connections as $connection)
                            <option value="{{ $connection['id'] }}">#{{ $connection['id'] }} - {{ $connection['subject'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Subject</small>
                    <input class="form-control" type="text" name="subject">
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Message *</small>
                    <textarea class="form-control" name="message" rows="4" required></textarea>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Send Message</button>
                </div>
            </form>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($inbox as $message)
                        <tr>
                            <td>{{ $message['sender_name'] }}</td>
                            <td>{{ $message['subject'] }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($message['message'], 120) }}</td>
                            <td>
                                <span class="status-badge {{ $message['is_read'] ? 'status-verified' : 'status-open' }}">
                                    {{ $message['is_read'] ? 'Read' : 'Unread' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No messages yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>

@endsection
