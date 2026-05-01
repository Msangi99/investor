@extends('layouts.dashboard')

@section('content')
@php
    $__adminNormRole = \App\Support\LegacyRoleMatrix::normalizeRole((string) ($_SESSION['user_role'] ?? 'SUPER_ADMIN'));
    $__adminRoleTitle = \App\Support\LegacyRoleMatrix::roleConfig($__adminNormRole)['label'] ?? $__adminNormRole;
@endphp
<section class="dashboard-hero admin">
    <div class="container dashboard-hero-grid">
        <div>
            <div class="page-kicker"><i class="fa-solid fa-user-shield"></i> {{ $__adminNormRole === 'SUPER_ADMIN' ? 'Super Admin workspace' : 'Admin workspace' }}</div>
            <h1>{{ $pageTitle }}</h1>
            <p>Manage platform operations, users, data quality, and system controls from one workspace.</p>
        </div>
        <div class="dashboard-profile-card">
            <div class="profile-avatar admin-avatar">{{ strtoupper(substr((string)($_SESSION['user_name'] ?? 'AD'), 0, 2)) }}</div>
            <div>
                <h3>{{ $_SESSION['user_name'] ?? 'Administrator' }}</h3>
                <p>{{ $__adminRoleTitle }}</p>
                <span class="status-badge status-verified">
                    @if ($__adminNormRole === 'SUPER_ADMIN')
                        <i class="fa-solid fa-crown"></i>
                    @else
                        <i class="fa-solid fa-user-shield"></i>
                    @endif
                    {{ $__adminNormRole }}
                </span>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-shell">
    <div class="container">
        <div class="dashboard-content">
            @if (session('status'))
                <div class="form-alert form-alert-success"><p>{{ session('status') }}</p></div>
            @endif

            @if ($moduleKey === 'overview')
                @php $stats = $stats ?? []; @endphp
                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-users"></i></span><div><strong>{{ $stats['users'] ?? 0 }}</strong><small>Total users</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-building"></i></span><div><strong>{{ $stats['businesses'] ?? 0 }}</strong><small>Businesses</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-coins"></i></span><div><strong>{{ $stats['investors'] ?? 0 }}</strong><small>Investors</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-users-gear"></i></span><div><strong>{{ $stats['stakeholders'] ?? 0 }}</strong><small>Stakeholders</small></div></article>
                </div>
                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>Platform Health</h3><p>Quick system metrics and pending workloads.</p></div>
                        <span class="status-badge status-open">Live</span>
                    </div>
                    <div class="pipeline-list">
                        <div><span>Pending verifications</span><strong>{{ $stats['verifications_pending'] ?? 0 }}</strong></div>
                        <div><span>Pending uploads</span><strong>{{ $stats['uploads_pending'] ?? 0 }}</strong></div>
                        <div><span>Unread messages</span><strong>{{ $stats['messages_unread'] ?? 0 }}</strong></div>
                        <div><span>Opportunities</span><strong>{{ $stats['opportunities'] ?? 0 }}</strong></div>
                    </div>
                </article>
            @elseif ($moduleKey === 'profile')
                @php
                    $adminName = $adminName ?? (string) ($_SESSION['user_name'] ?? 'Administrator');
                    $adminEmail = $adminEmail ?? (string) ($_SESSION['user_email'] ?? '');
                    $adminRoleRaw = $adminRole ?? (string) ($_SESSION['user_role'] ?? 'SUPER_ADMIN');
                    $adminNormRole = \App\Support\LegacyRoleMatrix::normalizeRole($adminRoleRaw);
                    $adminRoleHuman = \App\Support\LegacyRoleMatrix::roleConfig($adminNormRole)['label'] ?? $adminNormRole;
                @endphp
                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Administrator profile</h3>
                            <p>Details for the account you are signed in with. Updates to roles for other users are done under Roles &amp; Permissions.</p>
                        </div>
                    </div>
                    <div class="form-grid" style="max-width: 560px;">
                        <div class="form-group">
                            <label for="adminProfileName">Display name</label>
                            <input id="adminProfileName" class="form-control" type="text" value="{{ $adminName }}" readonly autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label for="adminProfileEmail">Email</label>
                            <input id="adminProfileEmail" class="form-control" type="email" value="{{ $adminEmail }}" readonly autocomplete="email">
                        </div>
                        <div class="form-group">
                            <label for="adminProfileRole">Role key</label>
                            <input id="adminProfileRole" class="form-control" type="text" value="{{ $adminNormRole }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="adminProfileRoleLabel">Role description</label>
                            <input id="adminProfileRoleLabel" class="form-control" type="text" value="{{ $adminRoleHuman }}" readonly>
                        </div>
                    </div>
                    <p class="auth-note" style="margin-top: 14px;">
                        Password changes use the main login flow. If your workspace uses extended admin records in the database, those are separate from this summary view.
                    </p>
                    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:8px;">
                        <a class="btn btn-soft" href="/admin/dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                        <a class="btn btn-soft" href="/admin/roles.php"><i class="fa-solid fa-user-lock"></i> Roles &amp; Permissions</a>
                        <a class="btn btn-soft" href="/admin/settings.php"><i class="fa-solid fa-gear"></i> System settings</a>
                    </div>
                </article>
            @elseif ($moduleKey === 'roles')
                @php
                    $rows = $rows ?? [];
                    $users = $users ?? [];
                    $assignableRoles = $assignableRoles ?? [];
                @endphp
                <article class="dashboard-panel" style="margin-bottom:14px;">
                    <div class="panel-head">
                        <div><h3>Role Catalog</h3><p>Available roles and permission groups configured on the platform.</p></div>
                    </div>
                    @if (empty($rows))
                        <p class="auth-note">No role definitions found.</p>
                    @else
                        <div style="overflow:auto;">
                            <table class="table" style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">role_key</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">role_name</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">role_type</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">is_active</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $row)
                                        <tr>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->role_key ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->role_name ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->role_type ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->is_active ?? '') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </article>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>User Role Assignment</h3><p>Assign a role to each user directly from this page.</p></div>
                    </div>
                    <form method="get" class="form-group" style="max-width:380px;">
                        <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search user name or email">
                    </form>
                    @if (empty($users))
                        <p class="auth-note">No users found for role assignment.</p>
                    @else
                        <div style="overflow:auto;">
                            <table class="table" style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">id</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">full_name</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">email</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">current_role</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">status</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (int) ($user->id ?? 0) }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($user->full_name ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($user->email ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($user->role_key ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($user->status ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">
                                                <form method="post" action="/admin/roles.php/assign">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ (int) ($user->id ?? 0) }}">
                                                    <select name="role_key" class="form-control" style="min-width:190px; height:34px;">
                                                        @foreach ($assignableRoles as $roleKey)
                                                            <option value="{{ $roleKey }}" {{ (string) ($user->role_key ?? '') === (string) $roleKey ? 'selected' : '' }}>
                                                                {{ $roleKey }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-primary" type="submit" style="margin-top:6px;">Save Role</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </article>
            @elseif ($moduleKey === 'settings')
                @php $rows = $rows ?? []; @endphp
                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>System Settings</h3><p>Update platform configuration values.</p></div>
                    </div>
                    @if (empty($rows))
                        <p class="auth-note">No settings found.</p>
                    @else
                        <div class="cards-grid two-columns">
                            @foreach ($rows as $row)
                                <form method="post" action="/admin/settings.php" class="auth-card form-grid" style="padding:16px;">
                                    @csrf
                                    <input type="hidden" name="setting_key" value="{{ $row->setting_key ?? '' }}">
                                    <div class="form-group">
                                        <label>{{ $row->setting_key ?? 'Setting' }}</label>
                                        <input class="form-control" type="text" name="setting_value" value="{{ $row->setting_value ?? '' }}">
                                    </div>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </article>
            @elseif ($moduleKey === 'legal')
                @php $rows = $rows ?? []; @endphp
                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>Legal Documents</h3><p>Edit legal page content and titles.</p></div>
                    </div>
                    @if (empty($rows))
                        <p class="auth-note">No legal documents found.</p>
                    @else
                        @foreach ($rows as $row)
                            <form method="post" action="/admin/legal.php" class="auth-card form-grid" style="margin-bottom:14px;">
                                @csrf
                                <input type="hidden" name="id" value="{{ $row->id ?? 0 }}">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input class="form-control" name="title" value="{{ $row->title ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Content</label>
                                    <textarea class="form-control" name="content" rows="5">{{ $row->content ?? '' }}</textarea>
                                </div>
                                <button class="btn btn-primary" type="submit">Update Document</button>
                            </form>
                        @endforeach
                    @endif
                </article>
            @elseif ($moduleKey === 'insights')
                @php
                    $stats = $stats ?? [];
                    $rows = $opportunities ?? [];
                @endphp
                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-chart-line"></i></span><div><strong>{{ $stats['opportunities'] ?? 0 }}</strong><small>Opportunities</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-building"></i></span><div><strong>{{ $stats['businesses'] ?? 0 }}</strong><small>Businesses</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-coins"></i></span><div><strong>{{ $stats['investors'] ?? 0 }}</strong><small>Investors</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-users-gear"></i></span><div><strong>{{ $stats['stakeholders'] ?? 0 }}</strong><small>Stakeholders</small></div></article>
                </div>
                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>Opportunity insights</h3><p>Latest opportunity records available in the system.</p></div>
                    </div>
                    @if (empty($rows))
                        <p class="auth-note">No records found for this module.</p>
                    @else
                        <div style="overflow:auto;">
                            <table class="table" style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        @foreach (array_keys((array) $rows[0]) as $column)
                                            <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">{{ $column }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $row)
                                        <tr>
                                            @foreach ((array) $row as $value)
                                                <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ is_scalar($value) || $value === null ? (string) $value : json_encode($value) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </article>
            @elseif ($moduleKey === 'opportunities')
                @php $rows = $rows ?? []; @endphp
                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>Opportunity Reviews</h3><p>Open proposal documents and decide if each opportunity is published to investors.</p></div>
                    </div>
                    @if (empty($rows))
                        <p class="auth-note">No opportunities found.</p>
                    @else
                        <div style="overflow:auto; width:100%;">
                            <table class="table" style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">id</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">title</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">stage</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">business</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">status</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">verification</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">document</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">created</th>
                                        <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">review</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $row)
                                        <tr>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (int) ($row->id ?? 0) }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->title ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->stage ?? '-') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->business_name ?? '-') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->status ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->verification_status ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">
                                                @if (!empty($row->document_path))
                                                    <a class="btn btn-soft" href="{{ asset('storage/'.$row->document_path) }}" target="_blank" rel="noopener">
                                                        <i class="fa-solid fa-file-arrow-down"></i>
                                                        {{ $row->document_name ?: 'View Document' }}
                                                    </a>
                                                @else
                                                    <span class="auth-note">No document</span>
                                                @endif
                                            </td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ (string) ($row->created_at ?? '') }}</td>
                                            <td style="padding:10px; border-bottom:1px solid #f1f5f9;">
                                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                                    <form method="post" action="/admin/opportunities.php/review">
                                                        @csrf
                                                        <input type="hidden" name="opportunity_id" value="{{ $row->id ?? 0 }}">
                                                        <input type="hidden" name="status" value="published">
                                                        <button class="btn btn-soft" type="submit">Publish</button>
                                                    </form>
                                                    <form method="post" action="/admin/opportunities.php/review">
                                                        @csrf
                                                        <input type="hidden" name="opportunity_id" value="{{ $row->id ?? 0 }}">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button class="btn btn-soft" type="submit">Decline</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </article>
            @else
                @php $rows = $rows ?? []; @endphp
                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>{{ $pageTitle }} Records</h3><p>Live data pulled from the configured database tables.</p></div>
                    </div>
                    @if ($moduleKey === 'users')
                        <form method="get" class="form-group" style="max-width:380px;">
                            <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search user name or email">
                        </form>
                    @endif
                    @if (empty($rows))
                        <p class="auth-note">No records found for this module.</p>
                    @else
                        <div style="overflow:auto;">
                            <table class="table" style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        @foreach (array_keys((array) $rows[0]) as $column)
                                            <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">{{ $column }}</th>
                                        @endforeach
                                        @if ($moduleKey === 'users')
                                            <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">actions</th>
                                        @elseif ($moduleKey === 'verifications')
                                            <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">review</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $row)
                                        <tr>
                                            @foreach ((array) $row as $value)
                                                <td style="padding:10px; border-bottom:1px solid #f1f5f9;">{{ is_scalar($value) || $value === null ? (string) $value : json_encode($value) }}</td>
                                            @endforeach
                                            @if ($moduleKey === 'users')
                                                <td style="padding:10px; border-bottom:1px solid #f1f5f9;">
                                                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                                        <form method="post" action="/admin/user.php/status">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $row->id ?? 0 }}">
                                                            <input type="hidden" name="status" value="active">
                                                            <button class="btn btn-soft" type="submit">Activate</button>
                                                        </form>
                                                        <form method="post" action="/admin/user.php/status">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $row->id ?? 0 }}">
                                                            <input type="hidden" name="status" value="suspended">
                                                            <button class="btn btn-soft" type="submit">Suspend</button>
                                                        </form>
                                                        <form method="post" action="/admin/user.php/role">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $row->id ?? 0 }}">
                                                            <select name="role_key" class="form-control" style="min-width:170px; height:34px;">
                                                                <option value="SUPER_ADMIN">SUPER_ADMIN</option>
                                                                <option value="VERIFICATION_ADMIN">VERIFICATION_ADMIN</option>
                                                                <option value="SUPPORT_ADMIN">SUPPORT_ADMIN</option>
                                                                <option value="FINANCE_ADMIN">FINANCE_ADMIN</option>
                                                                <option value="CONTENT_ADMIN">CONTENT_ADMIN</option>
                                                                <option value="PARTNERSHIP_ADMIN">PARTNERSHIP_ADMIN</option>
                                                                <option value="ANALYTICS_ADMIN">ANALYTICS_ADMIN</option>
                                                                <option value="business">business</option>
                                                                <option value="investor">investor</option>
                                                                <option value="stakeholder">stakeholder</option>
                                                            </select>
                                                            <button class="btn btn-primary" type="submit" style="margin-top:6px;">Update Role</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @elseif ($moduleKey === 'verifications')
                                                <td style="padding:10px; border-bottom:1px solid #f1f5f9;">
                                                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                                        <form method="post" action="/admin/verifications.php/review">
                                                            @csrf
                                                            <input type="hidden" name="business_profile_id" value="{{ $row->id ?? 0 }}">
                                                            <input type="hidden" name="status" value="verified">
                                                            <button class="btn btn-soft" type="submit">Approve</button>
                                                        </form>
                                                        <form method="post" action="/admin/verifications.php/review">
                                                            @csrf
                                                            <input type="hidden" name="business_profile_id" value="{{ $row->id ?? 0 }}">
                                                            <input type="hidden" name="status" value="needs_update">
                                                            <button class="btn btn-soft" type="submit">Needs Update</button>
                                                        </form>
                                                        <form method="post" action="/admin/verifications.php/review">
                                                            @csrf
                                                            <input type="hidden" name="business_profile_id" value="{{ $row->id ?? 0 }}">
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button class="btn btn-soft" type="submit">Reject</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </article>
            @endif
        </div>
    </div>
</section>
@endsection

