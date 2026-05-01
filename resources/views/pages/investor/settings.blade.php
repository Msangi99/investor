@extends('layouts.dashboard')

@php
    $pageTitle = 'Investor Settings';
    $pageName = 'investor-settings';
    $activeSidebar = 'settings';
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
            <div class="panel-head">
                <div><h3>Account & Notifications</h3><p>Manage your profile details and investor notifications.</p></div>
            </div>
            <form method="post" action="{{ route('investor.settings.save') }}" class="form-grid two">
                @csrf
                <div class="form-group">
                    <small>Full Name</small>
                    <input class="form-control" type="text" name="full_name" value="{{ old('full_name', $user->full_name ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Organization</small>
                    <input class="form-control" type="text" name="organization" value="{{ old('organization', $user->organization ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Phone</small>
                    <input class="form-control" type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Email Notifications</small>
                    <select class="form-control" name="notification_email">
                        <option value="1" @selected(old('notification_email', $notification_email ? '1' : '0') === '1')>Enabled</option>
                        <option value="0" @selected(old('notification_email', $notification_email ? '1' : '0') === '0')>Disabled</option>
                    </select>
                </div>
                <div class="form-group">
                    <small>Message Notifications</small>
                    <select class="form-control" name="notification_messages">
                        <option value="1" @selected(old('notification_messages', $notification_messages ? '1' : '0') === '1')>Enabled</option>
                        <option value="0" @selected(old('notification_messages', $notification_messages ? '1' : '0') === '0')>Disabled</option>
                    </select>
                </div>
                <div class="form-group">
                    <small>Deal Alerts</small>
                    <select class="form-control" name="notification_deals">
                        <option value="1" @selected(old('notification_deals', $notification_deals ? '1' : '0') === '1')>Enabled</option>
                        <option value="0" @selected(old('notification_deals', $notification_deals ? '1' : '0') === '0')>Disabled</option>
                    </select>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Save Settings</button>
                </div>
            </form>
        </article>
    </div>
</section>
@endsection
