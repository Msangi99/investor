@extends('layouts.app')

@section('content')
    @php
        $legacyData = $legacyData ?? ['role' => null, 'modules' => [], 'dashboardUri' => '/dashboard'];
    @endphp
    <div class="panel">
        <div style="margin-bottom:12px;font-size:12px;color:#64748b;">
            Legacy partial rendered: <code>{{ $legacyPath }}</code>
        </div>
        @if (! empty($legacyData['modules']))
            <div style="margin-bottom:16px;padding:12px;border:1px solid #dbe2ea;border-radius:8px;background:#f8fafc;">
                <div style="font-size:12px;font-weight:600;color:#0f172a;margin-bottom:6px;">
                    Role Modules ({{ $legacyData['role'] }})
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach ($legacyData['modules'] as $module)
                        <a href="{{ $module['uri'] ?? '#' }}" style="font-size:12px;padding:6px 10px;border-radius:999px;background:#e2e8f0;color:#0f172a;text-decoration:none;">
                            {{ $module['label'] ?? 'Module' }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
        {!! $content !!}
    </div>
    <script>
        window.UNIDA_ROLE_ACCESS = @json($legacyData);
    </script>
@endsection
