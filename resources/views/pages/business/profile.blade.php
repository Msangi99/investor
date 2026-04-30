@extends('layouts.dashboard')

@php
    $pageTitle = 'Business Profile';
    $pageName = 'business-profile';
    $activeSidebar = 'profile';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Business Profile</h3>
                    <p>Keep your business information complete for verification and investor matching.</p>
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

            <form method="post" action="{{ route('business.profile.save') }}" class="form-grid two" style="margin-top:6px;">
                @csrf

                <div class="form-group">
                    <small>Business Name *</small>
                    <input class="form-control" type="text" name="business_name" value="{{ old('business_name', $profile->business_name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <small>Sector</small>
                    <select class="form-control" name="sector">
                        <option value="">Select sector</option>
                        @foreach($sectors as $sector)
                            <option value="{{ $sector }}" @selected(old('sector', $profile->sector ?? '') === $sector)>{{ $sector }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Business Stage *</small>
                    <select class="form-control" name="business_stage" required>
                        @foreach($stages as $stage)
                            <option value="{{ $stage }}" @selected(old('business_stage', $profile->business_stage ?? 'mvp') === $stage)>{{ ucwords(str_replace('_', ' ', $stage)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Registration Number</small>
                    <input class="form-control" type="text" name="registration_number" value="{{ old('registration_number', $profile->registration_number ?? '') }}">
                </div>
                <div class="form-group">
                    <small>TIN</small>
                    <input class="form-control" type="text" name="tax_identification_number" value="{{ old('tax_identification_number', $profile->tax_identification_number ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Region</small>
                    <input class="form-control" type="text" name="region" value="{{ old('region', $profile->region ?? '') }}">
                </div>
                <div class="form-group">
                    <small>District</small>
                    <input class="form-control" type="text" name="district" value="{{ old('district', $profile->district ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Current Jobs</small>
                    <input class="form-control" type="number" min="0" name="jobs_current" value="{{ old('jobs_current', $profile->jobs_current ?? 0) }}">
                </div>
                <div class="form-group">
                    <small>Potential Jobs</small>
                    <input class="form-control" type="number" min="0" name="jobs_potential" value="{{ old('jobs_potential', $profile->jobs_potential ?? 0) }}">
                </div>
                <div class="form-group">
                    <small>Funding Need</small>
                    <input class="form-control" type="number" step="0.01" min="0" name="funding_need_amount" value="{{ old('funding_need_amount', $profile->funding_need_amount ?? '') }}">
                </div>
                <div class="form-group">
                    <small>Currency</small>
                    <input class="form-control" type="text" name="funding_currency" value="{{ old('funding_currency', $profile->funding_currency ?? 'TZS') }}">
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Address</small>
                    <textarea class="form-control" name="address" rows="2">{{ old('address', $profile->address ?? '') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Business Description</small>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $profile->description ?? '') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Problem Statement</small>
                    <textarea class="form-control" name="problem_statement" rows="3">{{ old('problem_statement', $profile->problem_statement ?? '') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Solution Summary</small>
                    <textarea class="form-control" name="solution_summary" rows="3">{{ old('solution_summary', $profile->solution_summary ?? '') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Target Market</small>
                    <textarea class="form-control" name="target_market" rows="3">{{ old('target_market', $profile->target_market ?? '') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Traction Summary</small>
                    <textarea class="form-control" name="traction_summary" rows="3">{{ old('traction_summary', $profile->traction_summary ?? '') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <small>Funding Purpose</small>
                    <textarea class="form-control" name="funding_purpose" rows="3">{{ old('funding_purpose', $profile->funding_purpose ?? '') }}</textarea>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                </div>
            </form>
        </article>
    </div>
</section>

@endsection
