<?php

namespace App\Services\Stakeholder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StakeholderWorkspaceService
{
    public function recommendationsData(int $userId): array
    {
        $businesses = [];
        $investors = [];
        if (Schema::hasTable('business_profiles')) {
            $businesses = DB::table('business_profiles')->orderBy('business_name')->limit(100)
                ->get(['id', 'business_name'])
                ->map(static fn ($row): array => ['id' => (int) $row->id, 'name' => (string) $row->business_name])
                ->all();
        }
        if (Schema::hasTable('users')) {
            $investors = DB::table('users')
                ->where('role', 'investor')
                ->orderBy('full_name')
                ->limit(100)
                ->get(['id', 'full_name'])
                ->map(static fn ($row): array => ['id' => (int) $row->id, 'name' => (string) $row->full_name])
                ->all();
        }

        $recommendations = [];
        if (Schema::hasTable('partner_connections')) {
            $recommendations = DB::table('partner_connections as pc')
                ->leftJoin('users as i', 'i.id', '=', 'pc.receiver_user_id')
                ->where('pc.requester_user_id', $userId)
                ->where('pc.connection_type', 'partnership')
                ->orderByDesc('pc.id')
                ->limit(100)
                ->get(['pc.id', 'pc.subject', 'pc.status', 'i.full_name as investor_name'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'subject' => (string) $row->subject,
                    'status' => (string) $row->status,
                    'investor_name' => (string) ($row->investor_name ?? 'Investor'),
                ])
                ->all();
        }

        return compact('businesses', 'investors', 'recommendations');
    }

    public function saveRecommendation(int $userId, array $payload): void
    {
        if ($userId <= 0 || ! Schema::hasTable('partner_connections')) {
            return;
        }
        $columns = Schema::getColumnListing('partner_connections');
        $record = [];
        foreach ([
            'requester_user_id' => $userId,
            'receiver_user_id' => (int) ($payload['investor_user_id'] ?? 0),
            'connection_type' => 'partnership',
            'subject' => $payload['subject'] ?? '',
            'message' => $payload['message'] ?? null,
            'status' => 'pending',
        ] as $key => $value) {
            if (in_array($key, $columns, true)) {
                $record[$key] = $value;
            }
        }
        if (in_array('id', $columns, true)) {
            $record['id'] = (int) DB::table('partner_connections')->max('id') + 1;
        }
        if (in_array('created_at', $columns, true)) {
            $record['created_at'] = now();
        }
        if (in_array('updated_at', $columns, true)) {
            $record['updated_at'] = now();
        }
        DB::table('partner_connections')->insert($record);
    }
}
