<?php

namespace App\Services\Business;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BusinessDashboardService
{
    public function buildForUser(int $userId): array
    {
        if ($userId <= 0) {
            return $this->defaults();
        }

        try {
            $businessProfile = $this->fetchBusinessProfile($userId);
            $businessProfileId = (int) ($businessProfile?->id ?? 0);

            $readinessScore = (int) ($businessProfile?->readiness_score ?? 0);
            $approvedDocuments = $this->countApprovedDocuments($userId);
            $opportunities = $this->countInvestmentOpportunities($userId, $businessProfileId);
            $unreadMessages = $this->countUnreadMessages($userId);
            $nextActions = $this->buildNextActions($businessProfileId, $approvedDocuments, $readinessScore, $opportunities);
            $statusLabel = $this->deriveStatusLabel($businessProfile?->verification_status ?? null);

            return [
                'stats' => [
                    'readiness_score' => $readinessScore,
                    'approved_documents' => $approvedDocuments,
                    'investment_requests' => $opportunities,
                    'unread_messages' => $unreadMessages,
                ],
                'status_label' => $statusLabel,
                'next_actions' => $nextActions,
            ];
        } catch (\Throwable $exception) {
            Log::warning('Business dashboard fallback triggered', [
                'user_id' => $userId,
                'error' => $exception->getMessage(),
            ]);

            return $this->defaults();
        }
    }

    private function fetchBusinessProfile(int $userId): ?object
    {
        if (! Schema::hasTable('business_profiles')) {
            return null;
        }

        return DB::table('business_profiles')
            ->select(['id', 'readiness_score', 'verification_status'])
            ->where('user_id', $userId)
            ->first();
    }

    private function countApprovedDocuments(int $userId): int
    {
        if (! Schema::hasTable('uploads')) {
            return 0;
        }

        return DB::table('uploads')
            ->where('user_id', $userId)
            ->where('upload_status', 'approved')
            ->count();
    }

    private function countInvestmentOpportunities(int $userId, int $businessProfileId): int
    {
        if (! Schema::hasTable('investment_opportunities')) {
            return 0;
        }

        $query = DB::table('investment_opportunities');
        if ($businessProfileId > 0) {
            $query->where('business_profile_id', $businessProfileId);
        } else {
            $query->where('created_by', $userId);
        }

        return $query->count();
    }

    private function countUnreadMessages(int $userId): int
    {
        if (! Schema::hasTable('messages')) {
            return 0;
        }

        return DB::table('messages')
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->count();
    }

    private function buildNextActions(int $businessProfileId, int $approvedDocuments, int $readinessScore, int $opportunities): array
    {
        $actions = [
            [
                'title' => 'Profile',
                'description' => 'Update business information and sector details.',
                'href' => '/business/profile.php',
                'icon' => 'building',
                'completed' => $businessProfileId > 0,
            ],
            [
                'title' => 'Documents',
                'description' => 'Upload required documents for verification.',
                'href' => '/business/documents.php',
                'icon' => 'file-shield',
                'completed' => $approvedDocuments > 0,
            ],
            [
                'title' => 'Opportunities',
                'description' => 'Create investment requests and track responses.',
                'href' => '/business/opportunities.php',
                'icon' => 'briefcase',
                'completed' => $opportunities > 0,
            ],
        ];

        if ($readinessScore >= 70) {
            $actions[] = [
                'title' => 'Insights',
                'description' => 'Review analytics and funding readiness trends.',
                'href' => '/business/insights.php',
                'icon' => 'chart-pie',
                'completed' => false,
            ];
        }

        return $actions;
    }

    private function deriveStatusLabel(?string $verificationStatus): string
    {
        return match ($verificationStatus) {
            'verified' => 'Verified',
            'pending' => 'Pending Verification',
            'needs_update' => 'Needs Update',
            'rejected' => 'Action Required',
            default => 'Active',
        };
    }

    private function defaults(): array
    {
        return [
            'stats' => [
                'readiness_score' => 0,
                'approved_documents' => 0,
                'investment_requests' => 0,
                'unread_messages' => 0,
            ],
            'status_label' => 'Active',
            'next_actions' => [
                [
                    'title' => 'Profile',
                    'description' => 'Update business information and sector details.',
                    'href' => '/business/profile.php',
                    'icon' => 'building',
                    'completed' => false,
                ],
                [
                    'title' => 'Documents',
                    'description' => 'Upload required documents for verification.',
                    'href' => '/business/documents.php',
                    'icon' => 'file-shield',
                    'completed' => false,
                ],
                [
                    'title' => 'Opportunities',
                    'description' => 'Create investment requests and track responses.',
                    'href' => '/business/opportunities.php',
                    'icon' => 'briefcase',
                    'completed' => false,
                ],
            ],
        ];
    }
}
