@extends('layouts.dashboard')

@php
    $pageTitle = 'Investor Profile';
    $pageName = 'investor-profile';
    $activeSidebar = 'profile';
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
                <div><h3>Investor Profile</h3><p>Set your investment preferences so matching is more accurate.</p></div>
            </div>
            <form method="post" action="{{ route('investor.profile.save') }}" class="form-grid two">
                @csrf
                <div class="form-group">
                    <small>Investor Name *</small>
                    <input class="form-control" type="text" name="investor_name" value="{{ old('investor_name', $profile->investor_name ?? ($_SESSION['user_name'] ?? '')) }}" required>
                </div>
                <div class="form-group">
                    <small>Investor Type *</small>
                    <select class="form-control" name="investor_type" required>
                        @foreach($investor_types as $type)
                            <option value="{{ $type }}" @selected(old('investor_type', $profile->investor_type ?? 'individual') === $type)>{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Preferred Sectors</small>
                    <input class="form-control" type="text" name="preferred_sectors" value="{{ old('preferred_sectors', $profile->preferred_sectors ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Preferred Regions</small>
                    <input class="form-control" type="text" name="preferred_regions" value="{{ old('preferred_regions', $profile->preferred_regions ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Ticket Min</small>
                    <input class="form-control" type="number" min="0" step="0.01" name="ticket_min" value="{{ old('ticket_min', $profile->ticket_min ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Ticket Max</small>
                    <input class="form-control" type="number" min="0" step="0.01" name="ticket_max" value="{{ old('ticket_max', $profile->ticket_max ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Currency</small>
                    <input class="form-control" type="text" name="currency" value="{{ old('currency', $profile->currency ?? 'TZS') }}">
                </div>
                <div class="form-group">
                    <small>Stage Interest</small>
                    <input class="form-control" type="text" name="investment_stage_interest" value="{{ old('investment_stage_interest', $profile->investment_stage_interest ?? '') }}">
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Save Profile</button>
                </div>
            </form>
        </article>
    </div>
</section>
@endsection
