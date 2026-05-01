@extends('layouts.dashboard')

@php
    $pageTitle = 'Messages';
    $pageName = 'investor-messages';
    $activeSidebar = 'messages';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div><h3>Inbox</h3><p>Messages sent to your investor account.</p></div>
            </div>
            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($inbox ?? [] as $message)
                        <tr>
                            <td>{{ $message['sender_name'] }}</td>
                            <td>{{ $message['subject'] }}</td>
                            <td>{{ $message['message'] }}</td>
                            <td>{{ $message['created_at'] ?: '-' }}</td>
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
