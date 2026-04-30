<?php

namespace App\Services\Investor;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InvestorWorkspaceService
{
    public function discoverData(int $userId): array
    {
        $opportunities = [];
        if (Schema::hasTable('investment_opportunities')) {
            $opportunities = DB::table('investment_opportunities as io')
                ->leftJoin('business_profiles as bp', 'bp.id', '=', 'io.business_profile_id')
                ->where('io.status', 'published')
                ->where('io.verification_status', 'verified')
                ->orderByDesc('io.id')
                ->limit(100)
                ->get(['io.id', 'io.title', 'io.sector', 'io.region', 'io.funding_amount', 'io.currency', 'io.stage', 'bp.business_name'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'title' => (string) $row->title,
                    'business_name' => (string) ($row->business_name ?? 'Business'),
                    'sector' => (string) ($row->sector ?? ''),
                    'region' => (string) ($row->region ?? ''),
                    'funding_amount' => (float) ($row->funding_amount ?? 0),
                    'currency' => (string) ($row->currency ?? 'TZS'),
                    'stage' => (string) ($row->stage ?? 'mvp'),
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

        DB::table('investor_shortlists')->updateOrInsert(
            ['investor_user_id' => $userId, 'opportunity_id' => $opportunityId],
            $payload
        );
    }

    public function shortlistData(int $userId): array
    {
        $rows = [];
        if (Schema::hasTable('investor_shortlists')) {
            $rows = DB::table('investor_shortlists as s')
                ->leftJoin('investment_opportunities as io', 'io.id', '=', 's.opportunity_id')
                ->where('s.investor_user_id', $userId)
                ->orderByDesc('s.id')
                ->limit(100)
                ->get(['s.id', 's.status', 's.note', 'io.id as opportunity_id', 'io.title', 'io.sector', 'io.region', 'io.funding_amount', 'io.currency'])
                ->map(static fn ($row): array => [
                    'id' => (int) $row->id,
                    'opportunity_id' => (int) ($row->opportunity_id ?? 0),
                    'title' => (string) ($row->title ?? ''),
                    'sector' => (string) ($row->sector ?? ''),
                    'region' => (string) ($row->region ?? ''),
                    'funding_amount' => (float) ($row->funding_amount ?? 0),
                    'currency' => (string) ($row->currency ?? 'TZS'),
                    'status' => (string) ($row->status ?? 'saved'),
                    'note' => (string) ($row->note ?? ''),
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
}
