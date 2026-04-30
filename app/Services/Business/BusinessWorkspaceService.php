<?php

namespace App\Services\Business;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BusinessWorkspaceService
{
    public function profileData(int $userId): array
    {
        $profile = $this->businessProfile($userId);

        return [
            'profile' => $profile,
            'sectors' => ['Agriculture', 'Technology', 'Manufacturing', 'Health', 'Education', 'Energy', 'Logistics', 'Retail'],
            'stages' => ['idea', 'prototype', 'mvp', 'early_revenue', 'growth', 'scale'],
        ];
    }

    public function saveProfile(int $userId, array $payload): void
    {
        if ($userId <= 0 || ! Schema::hasTable('business_profiles')) {
            return;
        }

        $existing = $this->businessProfile($userId);
        $columns = Schema::getColumnListing('business_profiles');

        $data = $this->mapColumns($columns, [
            'user_id' => $userId,
            'business_name' => $payload['business_name'] ?? null,
            'registration_number' => $payload['registration_number'] ?? null,
            'tax_identification_number' => $payload['tax_identification_number'] ?? null,
            'sector' => $payload['sector'] ?? null,
            'business_stage' => $payload['business_stage'] ?? null,
            'region' => $payload['region'] ?? null,
            'district' => $payload['district'] ?? null,
            'address' => $payload['address'] ?? null,
            'description' => $payload['description'] ?? null,
            'problem_statement' => $payload['problem_statement'] ?? null,
            'solution_summary' => $payload['solution_summary'] ?? null,
            'target_market' => $payload['target_market'] ?? null,
            'traction_summary' => $payload['traction_summary'] ?? null,
            'funding_need_amount' => $payload['funding_need_amount'] ?? null,
            'funding_currency' => $payload['funding_currency'] ?? 'TZS',
            'funding_purpose' => $payload['funding_purpose'] ?? null,
            'jobs_current' => $payload['jobs_current'] ?? 0,
            'jobs_potential' => $payload['jobs_potential'] ?? 0,
        ]);

        if (in_array('updated_at', $columns, true)) {
            $data['updated_at'] = now();
        }

        if ($existing === null) {
            if (in_array('id', $columns, true)) {
                $data['id'] = $this->nextId('business_profiles');
            }
            if (in_array('created_at', $columns, true)) {
                $data['created_at'] = now();
            }
            DB::table('business_profiles')->insert($data);
            $profileId = (int) ($data['id'] ?? 0);
            if ($profileId > 0) {
                $this->ensureVerificationRequest($userId, $profileId, 'business_profile');
            }

            return;
        }

        DB::table('business_profiles')->where('id', $existing->id)->update($data);
        $this->ensureVerificationRequest($userId, (int) $existing->id, 'business_profile');
    }

    public function readinessData(int $userId): array
    {
        $profile = $this->businessProfile($userId);
        $profileId = (int) ($profile?->id ?? 0);
        $checklist = $this->readinessChecklist();
        $completed = [];

        if ($profileId > 0 && Schema::hasTable('business_readiness_items')) {
            $completed = DB::table('business_readiness_items')
                ->where('business_profile_id', $profileId)
                ->pluck('status', 'checklist_item_id')
                ->all();
        }

        $items = array_map(function (array $item) use ($completed): array {
            $status = $completed[$item['id']] ?? 'not_started';
            $item['status'] = in_array($status, ['completed', 'needs_update', 'not_started'], true) ? $status : 'not_started';

            return $item;
        }, $checklist);

        $completedCount = count(array_filter($items, static fn (array $item): bool => $item['status'] === 'completed'));
        $score = count($items) > 0 ? (int) round(($completedCount / count($items)) * 100) : 0;

        return [
            'profile' => $profile,
            'items' => $items,
            'score' => $score,
            'completed_count' => $completedCount,
        ];
    }

    public function saveReadiness(int $userId, array $completedItemIds): void
    {
        $profile = $this->businessProfile($userId);
        if ($profile === null || ! Schema::hasTable('business_readiness_items')) {
            return;
        }

        $profileId = (int) $profile->id;
        $checklist = $this->readinessChecklist();
        $itemIds = array_map(static fn (array $item): int => (int) $item['id'], $checklist);
        $selected = array_map('intval', $completedItemIds);
        $columns = Schema::getColumnListing('business_readiness_items');

        foreach ($itemIds as $checklistId) {
            $status = in_array($checklistId, $selected, true) ? 'completed' : 'not_started';
            $payload = $this->mapColumns($columns, [
                'business_profile_id' => $profileId,
                'checklist_item_id' => $checklistId,
                'status' => $status,
                'score_awarded' => $status === 'completed' ? 10 : 0,
                'updated_by' => $userId,
            ]);

            if (in_array('updated_at', $columns, true)) {
                $payload['updated_at'] = now();
            }

            DB::table('business_readiness_items')->updateOrInsert(
                ['business_profile_id' => $profileId, 'checklist_item_id' => $checklistId],
                $payload
            );
        }

        $score = count($itemIds) > 0 ? (int) round((count(array_intersect($itemIds, $selected)) / count($itemIds)) * 100) : 0;
        $this->updateProfileReadinessScore($profileId, $score);
    }

    public function documentsData(int $userId): array
    {
        $types = [];
        if (Schema::hasTable('document_types')) {
            $types = DB::table('document_types')
                ->whereIn('applies_to', ['business', 'all'])
                ->orderBy('is_required', 'desc')
                ->orderBy('type_name')
                ->get(['id', 'type_name', 'is_required'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'type_name' => (string) $row->type_name,
                    'is_required' => (int) $row->is_required === 1,
                ])
                ->all();
        }

        $documents = [];
        if (Schema::hasTable('uploads')) {
            $documents = DB::table('uploads as u')
                ->leftJoin('document_types as dt', 'dt.id', '=', 'u.document_type_id')
                ->where('u.user_id', $userId)
                ->where('u.related_type', 'business')
                ->orderByDesc('u.id')
                ->limit(50)
                ->get(['u.id', 'u.original_name', 'u.upload_status', 'u.created_at', 'dt.type_name'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'name' => (string) $row->original_name,
                    'status' => (string) $row->upload_status,
                    'uploaded_at' => $row->created_at,
                    'type_name' => (string) ($row->type_name ?? 'Uncategorized'),
                ])
                ->all();
        }

        return [
            'types' => $types,
            'documents' => $documents,
        ];
    }

    public function saveDocument(int $userId, int $businessProfileId, array $payload): void
    {
        if ($userId <= 0 || ! Schema::hasTable('uploads')) {
            return;
        }

        /** @var UploadedFile $file */
        $file = $payload['document'];
        $storedPath = $file->store('business-documents', 'public');
        $columns = Schema::getColumnListing('uploads');

        $record = $this->mapColumns($columns, [
            'user_id' => $userId,
            'related_type' => 'business',
            'related_id' => $businessProfileId > 0 ? $businessProfileId : null,
            'document_type_id' => (int) ($payload['document_type_id'] ?? 0) ?: null,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => basename($storedPath),
            'file_path' => $storedPath,
            'file_ext' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'upload_status' => 'uploaded',
        ]);

        if (in_array('id', $columns, true)) {
            $record['id'] = $this->nextId('uploads');
        }
        if (in_array('created_at', $columns, true)) {
            $record['created_at'] = now();
        }
        if (in_array('updated_at', $columns, true)) {
            $record['updated_at'] = now();
        }

        DB::table('uploads')->insert($record);
        if ($businessProfileId > 0) {
            $this->ensureVerificationRequest($userId, $businessProfileId, 'document');
        }
    }

    public function opportunitiesData(int $userId): array
    {
        $profile = $this->businessProfile($userId);
        $profileId = (int) ($profile?->id ?? 0);

        $list = [];
        if (Schema::hasTable('investment_opportunities')) {
            $query = DB::table('investment_opportunities')->where('created_by', $userId);
            if ($profileId > 0) {
                $query->orWhere('business_profile_id', $profileId);
            }

            $list = $query->orderByDesc('id')
                ->limit(50)
                ->get(['id', 'title', 'sector', 'region', 'funding_amount', 'currency', 'status', 'verification_status', 'created_at'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'title' => (string) $row->title,
                    'sector' => (string) ($row->sector ?? ''),
                    'region' => (string) ($row->region ?? ''),
                    'funding_amount' => $row->funding_amount,
                    'currency' => (string) ($row->currency ?? 'TZS'),
                    'status' => (string) ($row->status ?? 'draft'),
                    'verification_status' => (string) ($row->verification_status ?? 'pending'),
                    'created_at' => $row->created_at,
                ])
                ->all();
        }

        return [
            'profile' => $profile,
            'opportunities' => $list,
            'stages' => ['idea', 'prototype', 'mvp', 'early_revenue', 'growth', 'scale'],
            'funding_types' => ['equity', 'debt', 'grant', 'partnership', 'asset_finance', 'other'],
        ];
    }

    public function saveOpportunity(int $userId, array $payload): void
    {
        if ($userId <= 0 || ! Schema::hasTable('investment_opportunities')) {
            return;
        }

        $profile = $this->businessProfile($userId);
        $profileId = (int) ($profile?->id ?? 0);
        $columns = Schema::getColumnListing('investment_opportunities');

        $record = $this->mapColumns($columns, [
            'business_profile_id' => $profileId > 0 ? $profileId : 0,
            'created_by' => $userId,
            'title' => $payload['title'] ?? null,
            'sector' => $payload['sector'] ?? null,
            'region' => $payload['region'] ?? null,
            'summary' => $payload['summary'] ?? null,
            'funding_amount' => $payload['funding_amount'] ?? null,
            'currency' => $payload['currency'] ?? 'TZS',
            'funding_type' => $payload['funding_type'] ?? 'equity',
            'stage' => $payload['stage'] ?? 'mvp',
            'status' => $payload['status'] ?? 'draft',
            'verification_status' => 'pending',
            'readiness_score' => (int) ($profile?->readiness_score ?? 0),
            'published_at' => ($payload['status'] ?? 'draft') === 'published' ? now() : null,
        ]);

        if (in_array('id', $columns, true)) {
            $record['id'] = $this->nextId('investment_opportunities');
        }
        if (in_array('created_at', $columns, true)) {
            $record['created_at'] = now();
        }
        if (in_array('updated_at', $columns, true)) {
            $record['updated_at'] = now();
        }

        DB::table('investment_opportunities')->insert($record);
    }

    public function connectionsData(int $userId): array
    {
        $contacts = [];
        if (Schema::hasTable('users')) {
            $contacts = DB::table('users')
                ->where('id', '!=', $userId)
                ->whereIn('role', ['investor', 'stakeholder', 'admin'])
                ->orderBy('full_name')
                ->limit(100)
                ->get(['id', 'full_name', 'email', 'role'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'name' => (string) $row->full_name,
                    'email' => (string) $row->email,
                    'role' => (string) $row->role,
                ])
                ->all();
        }

        $opportunities = [];
        if (Schema::hasTable('investment_opportunities')) {
            $opportunities = DB::table('investment_opportunities')
                ->where('created_by', $userId)
                ->orderByDesc('id')
                ->limit(100)
                ->get(['id', 'title'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'title' => (string) $row->title,
                ])
                ->all();
        }

        $connections = [];
        if (Schema::hasTable('partner_connections')) {
            $connections = DB::table('partner_connections as pc')
                ->leftJoin('users as receiver', 'receiver.id', '=', 'pc.receiver_user_id')
                ->leftJoin('investment_opportunities as io', 'io.id', '=', 'pc.opportunity_id')
                ->where(function ($query) use ($userId): void {
                    $query->where('pc.requester_user_id', $userId)
                        ->orWhere('pc.receiver_user_id', $userId);
                })
                ->orderByDesc('pc.id')
                ->limit(50)
                ->get(['pc.id', 'pc.connection_type', 'pc.subject', 'pc.status', 'pc.created_at', 'pc.receiver_user_id', 'receiver.full_name as receiver_name', 'io.title as opportunity_title'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'type' => (string) $row->connection_type,
                    'subject' => (string) $row->subject,
                    'status' => (string) $row->status,
                    'created_at' => $row->created_at,
                    'receiver_name' => (string) ($row->receiver_name ?? 'Open connection'),
                    'opportunity_title' => (string) ($row->opportunity_title ?? '-'),
                    'is_incoming' => (int) ($row->receiver_user_id ?? 0) === $userId,
                ])
                ->all();
        }

        return [
            'contacts' => $contacts,
            'opportunities' => $opportunities,
            'connections' => $connections,
            'connection_types' => ['investment', 'bank_finance', 'government_support', 'mentorship', 'partnership', 'verification_support', 'other'],
        ];
    }

    public function saveConnection(int $userId, array $payload): void
    {
        if ($userId <= 0 || ! Schema::hasTable('partner_connections')) {
            return;
        }

        $columns = Schema::getColumnListing('partner_connections');
        $record = $this->mapColumns($columns, [
            'requester_user_id' => $userId,
            'receiver_user_id' => (int) ($payload['receiver_user_id'] ?? 0) ?: null,
            'opportunity_id' => (int) ($payload['opportunity_id'] ?? 0) ?: null,
            'connection_type' => $payload['connection_type'] ?? 'partnership',
            'subject' => $payload['subject'] ?? null,
            'message' => $payload['message'] ?? null,
            'status' => 'pending',
        ]);

        if (in_array('id', $columns, true)) {
            $record['id'] = $this->nextId('partner_connections');
        }
        if (in_array('created_at', $columns, true)) {
            $record['created_at'] = now();
        }
        if (in_array('updated_at', $columns, true)) {
            $record['updated_at'] = now();
        }

        DB::table('partner_connections')->insert($record);
    }

    public function insightsData(int $userId): array
    {
        $latestInsights = [];
        if (Schema::hasTable('insights')) {
            $latestInsights = DB::table('insights')
                ->where('status', 'published')
                ->whereIn('visibility', ['public', 'logged_in', 'investors'])
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(8)
                ->get(['title', 'summary', 'sector', 'region', 'published_at'])
                ->map(static fn ($row): array => [
                    'title' => (string) $row->title,
                    'summary' => (string) ($row->summary ?? ''),
                    'sector' => (string) ($row->sector ?? ''),
                    'region' => (string) ($row->region ?? ''),
                    'published_at' => $row->published_at,
                ])
                ->all();
        }

        $metrics = [];
        if (Schema::hasTable('insight_metrics')) {
            $metrics = DB::table('insight_metrics')
                ->orderByDesc('id')
                ->limit(6)
                ->get(['metric_name', 'metric_value', 'metric_unit'])
                ->map(static fn ($row): array => [
                    'name' => (string) $row->metric_name,
                    'value' => (float) $row->metric_value,
                    'unit' => (string) ($row->metric_unit ?? ''),
                ])
                ->all();
        }

        $myStats = [
            'requests' => Schema::hasTable('investment_opportunities') ? (int) DB::table('investment_opportunities')->where('created_by', $userId)->count() : 0,
            'connections' => Schema::hasTable('partner_connections') ? (int) DB::table('partner_connections')->where('requester_user_id', $userId)->count() : 0,
            'messages' => Schema::hasTable('messages') ? (int) DB::table('messages')->where('receiver_id', $userId)->where('is_read', 0)->count() : 0,
        ];

        return [
            'latest_insights' => $latestInsights,
            'metrics' => $metrics,
            'my_stats' => $myStats,
        ];
    }

    public function messagesData(int $userId): array
    {
        $contacts = [];
        if (Schema::hasTable('users')) {
            $contacts = DB::table('users')
                ->where('id', '!=', $userId)
                ->whereIn('role', ['investor', 'stakeholder', 'admin'])
                ->orderBy('full_name')
                ->limit(100)
                ->get(['id', 'full_name', 'email'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'name' => (string) $row->full_name,
                    'email' => (string) $row->email,
                ])
                ->all();
        }

        $inbox = [];
        if (Schema::hasTable('messages')) {
            $inbox = DB::table('messages as m')
                ->leftJoin('users as sender', 'sender.id', '=', 'm.sender_id')
                ->where('m.receiver_id', $userId)
                ->orderByDesc('m.id')
                ->limit(50)
                ->get(['m.id', 'm.subject', 'm.message', 'm.is_read', 'm.created_at', 'sender.full_name as sender_name'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'subject' => (string) ($row->subject ?? 'No subject'),
                    'message' => (string) $row->message,
                    'is_read' => (int) $row->is_read === 1,
                    'created_at' => $row->created_at,
                    'sender_name' => (string) ($row->sender_name ?? 'Unknown'),
                ])
                ->all();
        }

        $connections = [];
        if (Schema::hasTable('partner_connections')) {
            $connections = DB::table('partner_connections')
                ->where('requester_user_id', $userId)
                ->orWhere('receiver_user_id', $userId)
                ->orderByDesc('id')
                ->limit(100)
                ->get(['id', 'subject'])
                ->map(static fn ($row): array => ['id' => (int) $row->id, 'subject' => (string) $row->subject])
                ->all();
        }

        return [
            'contacts' => $contacts,
            'connections' => $connections,
            'inbox' => $inbox,
        ];
    }

    public function saveMessage(int $userId, array $payload): void
    {
        if ($userId <= 0 || ! Schema::hasTable('messages')) {
            return;
        }

        $connectionId = (int) ($payload['connection_id'] ?? 0);
        if ($connectionId > 0 && Schema::hasTable('partner_connections')) {
            $allowed = DB::table('partner_connections')
                ->where('id', $connectionId)
                ->where(function ($query) use ($userId): void {
                    $query->where('requester_user_id', $userId)
                        ->orWhere('receiver_user_id', $userId);
                })
                ->whereIn('status', ['accepted', 'in_progress', 'completed'])
                ->exists();
            if (! $allowed) {
                return;
            }
        }

        $columns = Schema::getColumnListing('messages');
        $record = $this->mapColumns($columns, [
            'sender_id' => $userId,
            'receiver_id' => (int) ($payload['receiver_user_id'] ?? 0),
            'connection_id' => $connectionId ?: null,
            'subject' => $payload['subject'] ?? null,
            'message' => $payload['message'] ?? null,
            'is_read' => 0,
        ]);

        if (in_array('id', $columns, true)) {
            $record['id'] = $this->nextId('messages');
        }
        if (in_array('created_at', $columns, true)) {
            $record['created_at'] = now();
        }

        DB::table('messages')->insert($record);
    }

    public function updateConnectionStatus(int $userId, int $connectionId, string $status): void
    {
        if ($userId <= 0 || $connectionId <= 0 || ! Schema::hasTable('partner_connections')) {
            return;
        }

        DB::table('partner_connections')
            ->where('id', $connectionId)
            ->where('receiver_user_id', $userId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }

    public function settingsData(int $userId): array
    {
        $user = null;
        if ($userId > 0 && Schema::hasTable('users')) {
            $user = DB::table('users')->where('id', $userId)->first();
        }

        return [
            'user' => $user,
            'notification_email' => $this->settingFlag($userId, 'biz_notify_email', true),
            'notification_messages' => $this->settingFlag($userId, 'biz_notify_messages', true),
            'notification_connections' => $this->settingFlag($userId, 'biz_notify_connections', true),
        ];
    }

    public function saveSettings(int $userId, array $payload): void
    {
        if ($userId <= 0) {
            return;
        }

        if (Schema::hasTable('users')) {
            $columns = Schema::getColumnListing('users');
            $updates = $this->mapColumns($columns, [
                'full_name' => $payload['full_name'] ?? null,
                'organization' => $payload['organization'] ?? null,
                'phone' => $payload['phone'] ?? null,
            ]);
            if (in_array('updated_at', $columns, true)) {
                $updates['updated_at'] = now();
            }
            if ($updates !== []) {
                DB::table('users')->where('id', $userId)->update($updates);
            }
        }

        $this->updateSettingFlag($userId, 'biz_notify_email', ($payload['notification_email'] ?? '0') === '1');
        $this->updateSettingFlag($userId, 'biz_notify_messages', ($payload['notification_messages'] ?? '0') === '1');
        $this->updateSettingFlag($userId, 'biz_notify_connections', ($payload['notification_connections'] ?? '0') === '1');
    }

    private function businessProfile(int $userId): ?object
    {
        if ($userId <= 0 || ! Schema::hasTable('business_profiles')) {
            return null;
        }

        return DB::table('business_profiles')->where('user_id', $userId)->first();
    }

    /**
     * @return array<int, array{id:int,name:string,description:string}>
     */
    private function readinessChecklist(): array
    {
        if (Schema::hasTable('readiness_checklist_items')) {
            $rows = DB::table('readiness_checklist_items')
                ->where('is_active', 1)
                ->orderBy('id')
                ->get(['id', 'item_name', 'description'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'name' => (string) $row->item_name,
                    'description' => (string) ($row->description ?? ''),
                ])
                ->all();
            if ($rows !== []) {
                return $rows;
            }
        }

        return [
            ['id' => 1, 'name' => 'Business registration', 'description' => 'Business is legally registered and identifiable.'],
            ['id' => 2, 'name' => 'Financial records', 'description' => 'At least 6 months of clean financial records are available.'],
            ['id' => 3, 'name' => 'Team capability', 'description' => 'Core team roles and ownership are clearly documented.'],
            ['id' => 4, 'name' => 'Market validation', 'description' => 'Customer demand and target market are validated.'],
            ['id' => 5, 'name' => 'Growth plan', 'description' => 'Business has a practical growth and execution roadmap.'],
        ];
    }

    private function updateProfileReadinessScore(int $profileId, int $score): void
    {
        if ($profileId <= 0 || ! Schema::hasTable('business_profiles')) {
            return;
        }

        $columns = Schema::getColumnListing('business_profiles');
        if (! in_array('readiness_score', $columns, true)) {
            return;
        }

        $payload = ['readiness_score' => max(0, min(100, $score))];
        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        DB::table('business_profiles')->where('id', $profileId)->update($payload);
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function mapColumns(array $columns, array $payload): array
    {
        $result = [];
        foreach ($payload as $column => $value) {
            if (in_array($column, $columns, true)) {
                $result[$column] = $value;
            }
        }

        return $result;
    }

    private function nextId(string $table): int
    {
        $max = DB::table($table)->max('id');

        return (int) $max + 1;
    }

    private function settingFlag(int $userId, string $key, bool $default): bool
    {
        if (! Schema::hasTable('system_settings')) {
            return $default;
        }

        $value = DB::table('system_settings')
            ->where('setting_key', $this->userSettingKey($userId, $key))
            ->value('setting_value');

        if ($value === null) {
            return $default;
        }

        return in_array((string) $value, ['1', 'true', 'yes'], true);
    }

    private function updateSettingFlag(int $userId, string $key, bool $enabled): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        $columns = Schema::getColumnListing('system_settings');
        $settingKey = $this->userSettingKey($userId, $key);
        $payload = $this->mapColumns($columns, [
            'setting_key' => $settingKey,
            'setting_value' => $enabled ? '1' : '0',
            'description' => 'Business user preference',
        ]);
        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }
        if (in_array('created_at', $columns, true)) {
            $payload['created_at'] = now();
        }

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => $settingKey],
            $payload
        );
    }

    private function userSettingKey(int $userId, string $key): string
    {
        return 'user_'.$userId.'_'.$key;
    }

    private function ensureVerificationRequest(int $userId, int $businessProfileId, string $requestType): void
    {
        if (! Schema::hasTable('verification_requests')) {
            return;
        }

        $columns = Schema::getColumnListing('verification_requests');
        $payload = $this->mapColumns($columns, [
            'user_id' => $userId,
            'business_profile_id' => $businessProfileId,
            'request_type' => $requestType,
            'status' => 'pending',
            'readiness_score_before' => (int) (DB::table('business_profiles')->where('id', $businessProfileId)->value('readiness_score') ?? 0),
            'submitted_at' => now(),
        ]);
        if (in_array('id', $columns, true)) {
            $payload['id'] = $this->nextId('verification_requests');
        }
        if (in_array('created_at', $columns, true)) {
            $payload['created_at'] = now();
        }
        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        DB::table('verification_requests')->insert($payload);
    }
}
