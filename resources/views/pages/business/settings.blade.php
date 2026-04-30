@extends('layouts.dashboard')

@php
    $pageTitle = 'Business Settings';
    $pageName = 'business-settings';
    $activeSidebar = 'settings';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Settings</h3>
                    <p>Manage your account profile and notification preferences.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="form-alert form-alert-success" style="margin-bottom:14px;"><p>{{ session('success') }}</p></div>
            @endif
            @if($errors->any())
                <div class="form-alert form-alert-error" style="margin-bottom:14px;"><p>{{ $errors->first() }}</p></div>
            @endif

            <form method="post" action="{{ route('business.settings.save') }}" class="form-grid two">
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
                    <small>Email</small>
                    <input class="form-control" type="text" value="{{ $user->email ?? '' }}" disabled>
                </div>
                <div class="form-group">
                    <small>Phone</small>
                    <input class="form-control" type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                </div>

                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Notifications</small>
                    <label class="readiness-check"><input type="checkbox" name="notification_email" value="1" @checked($notification_email)> Email updates</label>
                    <label class="readiness-check"><input type="checkbox" name="notification_messages" value="1" @checked($notification_messages)> New messages alerts</label>
                    <label class="readiness-check"><input type="checkbox" name="notification_connections" value="1" @checked($notification_connections)> Connection request alerts</label>
                </div>

                <div>
                    <button class="btn btn-primary" type="submit">Save Settings</button>
                </div>
            </form>
        </article>
    </div>
</section>

@endsection
