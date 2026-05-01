<?php

namespace App\Services\Investor;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InvestorWorkspaceService
{
    public function dashboardData(int $userId): array
    {
        $published = 0;
        if (Schema::hasTable('investment_opportunities')) {
            $published = (int) DB::table('investment_opportunities')->where('status', 'published')->count();
        }

        $verified = 0;
        if (Schema::hasTable('investment_opportunities')) {
            $verified = (int) DB::table('investment_opportunities')
                ->where('status', 'published')
                ->where('verification_status', 'verified')
                ->count();
        }

        $shortlisted = 0;
        $meetings = 0;
        if (Schema::hasTable('investor_shortlists')) {
            $shortlisted = (int) DB::table('investor_shortlists')
                ->where('investor_user_id', $userId)
                ->count();
            $meetings = (int) DB::table('investor_shortlists')
                ->where('investor_user_id', $userId)
                ->where('status', 'meeting_requested')
                ->count();
        }

        return [
            'stats' => [
                'published' => $published,
                'verified' => $verified,
                'shortlisted' => $shortlisted,
                'meetings' => $meetings,
            ],
        ];
    }

    public function profileData(int $userId): array
    {
        $profile = null;
        if ($userId > 0 && Schema::hasTable('investor_profiles')) {
            $profile = DB::table('investor_profiles')->where('user_id', $userId)->first();
        }

        return [
            'profile' => $profile,
            'investor_types' => ['individual', 'angel', 'venture_capital', 'private_equity', 'corporate', 'foundation', 'development_partner', 'bank', 'other'],
        ];
    }

    public function saveProfile(int $userId, array $payload): void
    {
        if ($userId <= 0 || ! Schema::hasTable('investor_profiles')) {
            return;
        }

        $existing = DB::table('investor_profiles')->where('user_id', $userId)->first();
        $columns = Schema::getColumnListing('investor_profiles');
        $record = $this->mapColumns($columns, [
            'user_id' => $userId,
            'investor_name' => $payload['investor_name'] ?? null,
            'investor_type' => $payload['investor_type'] ?? 'individual',
            'preferred_sectors' => $payload['preferred_sectors'] ?? null,
            'preferred_regions' => $payload['preferred_regions'] ?? null,
            'ticket_min' => $payload['ticket_min'] ?? null,
            'ticket_max' => $payload['ticket_max'] ?? null,
            'currency' => $payload['currency'] ?? 'TZS',
            'investment_stage_interest' => $payload['investment_stage_interest'] ?? null,
        ]);
        if (in_array('updated_at', $columns, true)) {
            $record['updated_at'] = now();
        }

        if ($existing === null) {
            if (in_array('id', $columns, true)) {
                $record['id'] = $this->nextId('investor_profiles');
            }
            if (in_array('created_at', $columns, true)) {
                $record['created_at'] = now();
            }
            DB::table('investor_profiles')->insert($record);
            return;
        }

        DB::table('investor_profiles')->where('id', $existing->id)->update($record);
    }

    public function discoverData(int $userId): array
    {
        $opportunities = [];
        if (Schema::hasTable('investment_opportunities')) {
            $query = DB::table('investment_opportunities as io')
                ->leftJoin('business_profiles as bp', 'bp.id', '=', 'io.business_profile_id')
                ->leftJoin('uploads as u', function ($join): void {
                    $join->on('u.related_id', '=', 'io.id')
                        ->where('u.related_type', '=', 'opportunity');
                })
                ->where('io.status', 'published')
                ->where('io.verification_status', 'verified')
                ->where(function ($query): void {
                    $query->whereNull('u.upload_status')
                        ->orWhereIn('u.upload_status', ['uploaded', 'under_review', 'approved']);
                })
                ->orderByDesc('io.id')
                ->limit(100);

            $shortlistSelect = DB::raw('0 as shortlist_id');
            if (Schema::hasTable('investor_shortlists')) {
                $query->leftJoin('investor_shortlists as s', function ($join) use ($userId): void {
                    $join->on('s.opportunity_id', '=', 'io.id')
                        ->where('s.investor_user_id', '=', $userId);
                });
                $shortlistSelect = DB::raw('COALESCE(s.id, 0) as shortlist_id');
            }

            $opportunities = $query
                ->get([
                    'io.id',
                    'io.title',
                    'io.sector',
                    'io.region',
                    'io.funding_amount',
                    'io.currency',
                    'io.stage',
                    'bp.business_name',
                    'u.file_path as document_path',
                    'u.original_name as document_name',
                    $shortlistSelect,
                ])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'title' => (string) $row->title,
                    'business_name' => (string) ($row->business_name ?? 'Business'),
                    'sector' => (string) ($row->sector ?? ''),
                    'region' => (string) ($row->region ?? ''),
                    'funding_amount' => (float) ($row->funding_amount ?? 0),
                    'currency' => (string) ($row->currency ?? 'TZS'),
                    'stage' => (string) ($row->stage ?? 'mvp'),
                    'document_path' => (string) ($row->document_path ?? ''),
                    'document_name' => (string) ($row->document_name ?? ''),
                    'is_shortlisted' => (int) ($row->shortlist_id ?? 0) > 0,
                ])
                ->all();
        }

        return ['opportunities' => $opportunities];
    }

    public function saveShortlist(int $userId, int $opportunityId): void
    {
        if ($userId <= 0 || $opportunityId <= 0 || ! Schema::hasTable('investor_shortlists')) {
            return;
        }

        $columns = Schema::getColumnListing('investor_shortlists');
        $payload = [
            'status' => 'saved',
            'updated_at' => now(),
        ];
        if (in_array('note', $columns, true)) {
            $payload['note'] = 'Saved from discover page';
        }
        if (in_array('created_at', $columns, true)) {
            $payload['created_at'] = now();
        }
        $existing = DB::table('investor_shortlists')
            ->where('investor_user_id', $userId)
            ->where('opportunity_id', $opportunityId)
            ->first(['id']);

        if ($existing !== null) {
            DB::table('investor_shortlists')
                ->where('id', (int) $existing->id)
                ->update([
                    'status' => $payload['status'],
                    'updated_at' => $payload['updated_at'],
                    'note' => $payload['note'] ?? null,
                ]);
            return;
        }

        $insert = [
            'investor_user_id' => $userId,
            'opportunity_id' => $opportunityId,
            'status' => $payload['status'],
            'updated_at' => $payload['updated_at'],
        ];
        if (in_array('note', $columns, true)) {
            $insert['note'] = $payload['note'] ?? null;
        }
        if (in_array('created_at', $columns, true)) {
            $insert['created_at'] = $payload['created_at'] ?? now();
        }
        if (in_array('id', $columns, true)) {
            $insert['id'] = (int) DB::table('investor_shortlists')->max('id') + 1;
        }

        DB::table('investor_shortlists')->insert($insert);
    }

    public function shortlistData(int $userId): array
    {
        $rows = [];
        if (Schema::hasTable('investor_shortlists')) {
            $rows = DB::table('investor_shortlists as s')
                ->leftJoin('investment_opportunities as io', 'io.id', '=', 's.opportunity_id')
                ->leftJoin('uploads as u', function ($join): void {
                    $join->on('u.related_id', '=', 'io.id')
                        ->where('u.related_type', '=', 'opportunity');
                })
                ->where('s.investor_user_id', $userId)
                ->orderByDesc('s.id')
                ->limit(100)
                ->get(['s.id', 's.status', 's.note', 'io.id as opportunity_id', 'io.title', 'io.sector', 'io.region', 'io.funding_amount', 'io.currency', 'io.stage', 'u.file_path as document_path', 'u.original_name as document_name'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'opportunity_id' => (int) ($row->opportunity_id ?? 0),
                    'title' => (string) ($row->title ?? ''),
                    'sector' => (string) ($row->sector ?? ''),
                    'region' => (string) ($row->region ?? ''),
                    'funding_amount' => (float) ($row->funding_amount ?? 0),
                    'currency' => (string) ($row->currency ?? 'TZS'),
                    'stage' => (string) ($row->stage ?? 'mvp'),
                    'status' => (string) ($row->status ?? 'saved'),
                    'note' => (string) ($row->note ?? ''),
                    'document_path' => (string) ($row->document_path ?? ''),
                    'document_name' => (string) ($row->document_name ?? ''),
                ])
                ->all();
        }

        return ['shortlist' => $rows];
    }

    public function updateShortlistStage(int $userId, int $shortlistId, string $status): void
    {
        if ($userId <= 0 || $shortlistId <= 0 || ! Schema::hasTable('investor_shortlists')) {
            return;
        }

        DB::table('investor_shortlists')
            ->where('id', $shortlistId)
            ->where('investor_user_id', $userId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }

    public function acceptProject(int $userId, int $opportunityId): void
    {
        if ($userId <= 0 || $opportunityId <= 0) {
            return;
        }

        $this->saveShortlist($userId, $opportunityId);

        if (! Schema::hasTable('investor_shortlists')) {
            return;
        }

        DB::table('investor_shortlists')
            ->where('investor_user_id', $userId)
            ->where('opportunity_id', $opportunityId)
            ->update([
                'status' => 'interested',
                'updated_at' => now(),
            ]);
    }

    public function pipelineData(int $userId): array
    {
        $rows = $this->shortlistData($userId)['shortlist'];
        $groups = [
            'reviewing' => [],
            'contacted' => [],
            'meeting' => [],
            'decided' => [],
        ];

        foreach ($rows as $row) {
            $status = $row['status'];
            if (in_array($status, ['saved', 'in_review'], true)) {
                $groups['reviewing'][] = $row;
            } elseif (in_array($status, ['interested', 'contacted'], true)) {
                $groups['contacted'][] = $row;
            } elseif ($status === 'meeting_requested') {
                $groups['meeting'][] = $row;
            } else {
                $groups['decided'][] = $row;
            }
        }

        return ['pipeline' => $groups];
    }

    public function myProjectsData(int $userId, int $shortlistId = 0): array
    {
        $projects = [];
        if (Schema::hasTable('investor_shortlists')) {
            $projects = DB::table('investor_shortlists as s')
                ->leftJoin('investment_opportunities as io', 'io.id', '=', 's.opportunity_id')
                ->leftJoin('business_profiles as bp', 'bp.id', '=', 'io.business_profile_id')
                ->where('s.investor_user_id', $userId)
                ->whereIn('s.status', ['interested', 'contacted', 'meeting_requested', 'in_review'])
                ->orderByDesc('s.updated_at')
                ->limit(100)
                ->get([
                    's.id',
                    's.status',
                    's.updated_at',
                    'io.id as opportunity_id',
                    'io.title',
                    'io.summary',
                    'io.funding_amount',
                    'io.currency',
                    'io.stage',
                    'bp.business_name',
                    'bp.sector',
                    'bp.region',
                    'bp.user_id as business_user_id',
                ])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'opportunity_id' => (int) ($row->opportunity_id ?? 0),
                    'title' => (string) ($row->title ?? ''),
                    'summary' => (string) ($row->summary ?? ''),
                    'funding_amount' => (float) ($row->funding_amount ?? 0),
                    'currency' => (string) ($row->currency ?? 'TZS'),
                    'stage' => (string) ($row->stage ?? 'mvp'),
                    'status' => (string) ($row->status ?? 'interested'),
                    'updated_at' => $row->updated_at,
                    'business_name' => (string) ($row->business_name ?? 'Business'),
                    'sector' => (string) ($row->sector ?? ''),
                    'region' => (string) ($row->region ?? ''),
                    'business_user_id' => (int) ($row->business_user_id ?? 0),
                ])
                ->all();
        }

        $selected = null;
        if ($shortlistId > 0) {
            foreach ($projects as $project) {
                if ((int) $project['id'] === $shortlistId) {
                    $selected = $project;
                    break;
                }
            }
        } elseif ($projects !== []) {
            $selected = $projects[0];
        }

        $businessContact = null;
        if ($selected !== null && ($selected['business_user_id'] ?? 0) > 0 && Schema::hasTable('users')) {
            $businessContact = DB::table('users')
                ->where('id', (int) $selected['business_user_id'])
                ->first(['full_name', 'email', 'phone', 'organization']);
        }

        return [
            'projects' => $projects,
            'selected_project' => $selected,
            'business_contact' => $businessContact,
        ];
    }

    public function verifiedBusinessesData(int $userId): array
    {
        $rows = [];
        if (Schema::hasTable('business_profiles')) {
            $query = DB::table('business_profiles as bp')
                ->where('bp.verification_status', 'verified')
                ->orderByDesc('bp.id')
                ->limit(100);

            if (Schema::hasTable('investment_opportunities')) {
                $query->leftJoin('investment_opportunities as io', function ($join): void {
                    $join->on('io.business_profile_id', '=', 'bp.id')
                        ->where('io.status', '=', 'published')
                        ->where('io.verification_status', '=', 'verified');
                })->groupBy('bp.id', 'bp.business_name', 'bp.sector', 'bp.region', 'bp.readiness_score');

                $rows = $query->get([
                    'bp.id',
                    'bp.business_name',
                    'bp.sector',
                    'bp.region',
                    'bp.readiness_score',
                    DB::raw('COUNT(io.id) as active_opportunities'),
                ])
                    ->map(static fn ($row): array => [
                        'id' => (int) $row->id,
                        'business_name' => (string) ($row->business_name ?? 'Business'),
                        'sector' => (string) ($row->sector ?? ''),
                        'region' => (string) ($row->region ?? ''),
                        'readiness_score' => (int) ($row->readiness_score ?? 0),
                        'active_opportunities' => (int) ($row->active_opportunities ?? 0),
                    ])->all();
            } else {
                $rows = $query->get(['bp.id', 'bp.business_name', 'bp.sector', 'bp.region', 'bp.readiness_score'])
                    ->map(static fn ($row): array => [
                        'id' => (int) $row->id,
                        'business_name' => (string) ($row->business_name ?? 'Business'),
                        'sector' => (string) ($row->sector ?? ''),
                        'region' => (string) ($row->region ?? ''),
                        'readiness_score' => (int) ($row->readiness_score ?? 0),
                        'active_opportunities' => 0,
                    ])->all();
            }
        }

        return ['businesses' => $rows];
    }

    public function meetingsData(int $userId): array
    {
        $rows = [];
        if (Schema::hasTable('investor_shortlists')) {
            $rows = DB::table('investor_shortlists as s')
                ->leftJoin('investment_opportunities as io', 'io.id', '=', 's.opportunity_id')
                ->leftJoin('business_profiles as bp', 'bp.id', '=', 'io.business_profile_id')
                ->where('s.investor_user_id', $userId)
                ->where('s.status', 'meeting_requested')
                ->orderByDesc('s.updated_at')
                ->limit(100)
                ->get(['s.id', 's.status', 's.updated_at', 'io.title', 'bp.business_name'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'title' => (string) ($row->title ?? ''),
                    'business_name' => (string) ($row->business_name ?? 'Business'),
                    'status' => (string) ($row->status ?? 'meeting_requested'),
                    'updated_at' => $row->updated_at,
                ])->all();
        }

        return ['meetings' => $rows];
    }

    public function insightsData(int $userId): array
    {
        $rows = [];
        if (Schema::hasTable('insights')) {
            $rows = DB::table('insights')
                ->where('status', 'published')
                ->whereIn('visibility', ['public', 'logged_in', 'investors'])
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(20)
                ->get(['title', 'summary', 'sector', 'region', 'published_at'])
                ->map(static fn ($row): array => [
                    'title' => (string) $row->title,
                    'summary' => (string) ($row->summary ?? ''),
                    'sector' => (string) ($row->sector ?? ''),
                    'region' => (string) ($row->region ?? ''),
                    'published_at' => $row->published_at,
                ])->all();
        }

        return ['insights' => $rows];
    }

    public function messagesData(int $userId): array
    {
        $inbox = [];
        if (Schema::hasTable('messages')) {
            $inbox = DB::table('messages as m')
                ->leftJoin('users as sender', 'sender.id', '=', 'm.sender_id')
                ->where('m.receiver_id', $userId)
                ->orderByDesc('m.id')
                ->limit(100)
                ->get(['m.id', 'm.subject', 'm.message', 'm.is_read', 'm.created_at', 'sender.full_name as sender_name'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'subject' => (string) ($row->subject ?? 'No subject'),
                    'message' => (string) ($row->message ?? ''),
                    'is_read' => (int) ($row->is_read ?? 0) === 1,
                    'created_at' => $row->created_at,
                    'sender_name' => (string) ($row->sender_name ?? 'Unknown'),
                ])->all();
        }

        return ['inbox' => $inbox];
    }

    public function settingsData(int $userId): array
    {
        $user = null;
        if ($userId > 0 && Schema::hasTable('users')) {
            $user = DB::table('users')->where('id', $userId)->first();
        }

        return [
            'user' => $user,
            'notification_email' => $this->settingFlag($userId, 'inv_notify_email', true),
            'notification_messages' => $this->settingFlag($userId, 'inv_notify_messages', true),
            'notification_deals' => $this->settingFlag($userId, 'inv_notify_deals', true),
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

        $this->updateSettingFlag($userId, 'inv_notify_email', ($payload['notification_email'] ?? '0') === '1');
        $this->updateSettingFlag($userId, 'inv_notify_messages', ($payload['notification_messages'] ?? '0') === '1');
        $this->updateSettingFlag($userId, 'inv_notify_deals', ($payload['notification_deals'] ?? '0') === '1');
    }

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
            'description' => 'Investor user preference',
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
}
