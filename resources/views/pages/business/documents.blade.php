@extends('layouts.dashboard')

@php
    $pageTitle = 'Business Documents';
    $pageName = 'business-documents';
    $activeSidebar = 'documents';
@endphp

@section('content')
<section class="dashboard-shell">
    <div class="container">
        <article class="dashboard-panel">
            <div class="panel-head">
                <div>
                    <h3>Documents</h3>
                    <p>Upload verification documents and monitor review status.</p>
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

            <form method="post" action="{{ route('business.documents.save') }}" enctype="multipart/form-data" class="form-grid two" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <small>Document Type</small>
                    <select class="form-control" name="document_type_id">
                        <option value="">Select type</option>
                        @foreach($types as $type)
                            <option value="{{ $type['id'] }}">
                                {{ $type['type_name'] }}{{ $type['is_required'] ? ' *' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <small>Upload File *</small>
                    <input class="form-control" type="file" name="document" required>
                </div>
                <div style="align-self:end;">
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                @forelse($documents as $document)
                        <tr>
                            <td>{{ $document['name'] }}</td>
                            <td>{{ $document['type_name'] }}</td>
                            <td><span class="status-badge status-progress">{{ ucfirst(str_replace('_', ' ', $document['status'])) }}</span></td>
                            <td>{{ !empty($document['uploaded_at']) ? \Illuminate\Support\Carbon::parse($document['uploaded_at'])->format('d M Y') : '-' }}</td>
                        </tr>
                @empty
                        <tr>
                            <td colspan="4">No uploaded documents yet.</td>
                        </tr>
                @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>

@endsection
