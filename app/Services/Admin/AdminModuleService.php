<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminModuleService
{
    private const PUBLIC_ROLES = ['business', 'investor', 'stakeholder'];

    public function stats(): array
    {
        return [
            'users' => $this->safeCount('users'),
            'businesses' => $this->safeCount('business_profiles'),
            'investors' => $this->safeCount('investor_profiles'),
            'stakeholders' => $this->safeCount('stakeholder_profiles'),
            'opportunities' => $this->safeCount('investment_opportunities'),
            'uploads_pending' => $this->safeCount('uploads', ['upload_status', 'pending']),
            'messages_unread' => $this->safeCount('messages', ['is_read', 0]),
            'verifications_pending' => $this->safeCount('business_profiles', ['verification_status', 'pending']),
        ];
    }

    /**
     * @return array<int, object>
     */
    public function listUsers(?string $search = null): array
    {
        if (! $this->hasTable('users')) {
            return [];
        }

        $columns = Schema::getColumnListing('users');
        $query = DB::table('users as u');
        $this->applyUserListSelect($query, $columns, 'u');

        if (is_string($search) && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term, $columns): void {
                $q->where('u.email', 'like', $term);
                if (in_array('full_name', $columns, true)) {
                    $q->orWhere('u.full_name', 'like', $term);
                }
                if (in_array('name', $columns, true)) {
                    $q->orWhere('u.name', 'like', $term);
                }
            });
        }

        return $query->orderByDesc('u.id')->limit(100)->get()->all();
    }

    /**
     * @return array<int, object>
     */
    public function listRoles(): array
    {
        if (! $this->hasTable('roles')) {
            return [];
        }

        return DB::table('roles')
            ->select('id', 'role_key', 'role_name', 'role_type', 'is_active')
            ->orderBy('role_name')
            ->get()
            ->all();
    }

    /**
     * @return array<int, object>
     */
    public function listRoleUsers(?string $search = null): array
    {
        if (! $this->hasTable('users')) {
            return [];
        }

        $columns = Schema::getColumnListing('users');
        $hasRoleId = in_array('role_id', $columns, true) && $this->hasTable('roles');

        $query = DB::table('users as u');
        if ($hasRoleId) {
            $query->leftJoin('roles as r', 'r.id', '=', 'u.role_id');
        }

        $nameSql = $this->userDisplayNameSql('u', $columns);
        $inferred = $this->inferredRoleKeySql('u', $columns);

        if ($hasRoleId && in_array('role', $columns, true)) {
            $roleKeySql = "COALESCE(r.role_key, u.role, {$inferred})";
            $roleNameSql = "COALESCE(r.role_name, r.role_key, u.role, {$inferred})";
        } elseif ($hasRoleId) {
            $roleKeySql = "COALESCE(r.role_key, {$inferred})";
            $roleNameSql = "COALESCE(r.role_name, r.role_key, {$inferred})";
        } elseif (in_array('role', $columns, true)) {
            $roleKeySql = "COALESCE(u.role, {$inferred})";
            $roleNameSql = "COALESCE(u.role, {$inferred})";
        } else {
            $roleKeySql = $inferred;
            $roleNameSql = $inferred;
        }

        $statusSql = in_array('status', $columns, true) ? 'u.status' : "'active'";

        $query->selectRaw("
            u.id,
            {$nameSql} as full_name,
            u.email,
            {$roleKeySql} as role_key,
            {$roleNameSql} as role_name,
            {$statusSql} as status
        ");

        if (is_string($search) && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term, $columns): void {
                $q->where('u.email', 'like', $term);
                if (in_array('full_name', $columns, true)) {
                    $q->orWhere('u.full_name', 'like', $term);
                }
                if (in_array('name', $columns, true)) {
                    $q->orWhere('u.name', 'like', $term);
                }
            });
        }

        return $query->orderByDesc('u.id')->limit(100)->get()->all();
    }

    /**
     * @return array<int, string>
     */
    public function assignableRoleKeys(): array
    {
        $configured = array_keys((array) config('legacy_roles.roles', []));
        if ($configured !== []) {
            return $configured;
        }

        return ['SUPER_ADMIN', 'VERIFICATION_ADMIN', 'SUPPORT_ADMIN', 'FINANCE_ADMIN', 'CONTENT_ADMIN', 'PARTNERSHIP_ADMIN', 'ANALYTICS_ADMIN', ...self::PUBLIC_ROLES];
    }

    /**
     * @return array<int, object>
     */
    public function listBusinesses(): array
    {
        if (! $this->hasTable('business_profiles')) {
            return [];
        }

        return $this->selectFromTableWithFallback('business_profiles', [
            'id' => 'NULL',
            'business_name' => "''",
            'industry' => "''",
            'region' => "''",
            'verification_status' => "''",
            'readiness_score' => 'NULL',
            'created_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listInvestors(): array
    {
        if (! $this->hasTable('investor_profiles')) {
            return [];
        }

        return $this->selectFromTableWithFallback('investor_profiles', [
            'id' => 'NULL',
            'investor_name' => "''",
            'investor_type' => "''",
            'sector_focus' => "''",
            'region_focus' => "''",
            'profile_status' => "''",
            'created_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listStakeholders(): array
    {
        if (! $this->hasTable('stakeholder_profiles')) {
            return [];
        }

        return $this->selectFromTableWithFallback('stakeholder_profiles', [
            'id' => 'NULL',
            'organization_name' => "''",
            'stakeholder_type' => "''",
            'coverage_area' => "''",
            'profile_status' => "''",
            'created_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listOpportunities(): array
    {
        if (! $this->hasTable('investment_opportunities')) {
            return [];
        }

        return $this->selectFromTableWithFallback('investment_opportunities', [
            'id' => 'NULL',
            'title' => "''",
            'sector' => "''",
            'region' => "''",
            'status' => "''",
            'verification_status' => "''",
            'created_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listUploads(): array
    {
        if (! $this->hasTable('uploads')) {
            return [];
        }

        return $this->selectFromTableWithFallback('uploads', [
            'id' => 'NULL',
            'original_name' => "''",
            'upload_status' => "''",
            'created_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listMessages(): array
    {
        if (! $this->hasTable('messages')) {
            return [];
        }

        return $this->selectFromTableWithFallback('messages', [
            'id' => 'NULL',
            'subject' => "''",
            'message' => "''",
            'is_read' => '0',
            'created_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listChatbotLogs(): array
    {
        $table = $this->hasTable('chatbot_logs') ? 'chatbot_logs' : ($this->hasTable('chatbot_conversations') ? 'chatbot_conversations' : null);
        if ($table === null) {
            return [];
        }

        return $this->selectFromTableWithFallback($table, [
            'id' => 'NULL',
            'user_message' => "''",
            'bot_response' => "''",
            'created_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listAiAssistants(): array
    {
        if (! $this->hasTable('ai_assistants')) {
            return [];
        }

        return $this->selectFromTableWithFallback('ai_assistants', [
            'id' => 'NULL',
            'assistant_name' => "''",
            'display_name' => "''",
            'status' => "''",
            'updated_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listAiTools(): array
    {
        if (! $this->hasTable('ai_settings')) {
            return [];
        }

        return $this->selectFromTableWithFallback('ai_settings', [
            'id' => 'NULL',
            'setting_key' => "''",
            'setting_value' => "''",
            'updated_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listLegalDocuments(): array
    {
        if (! $this->hasTable('legal_documents')) {
            return [];
        }

        return $this->selectFromTableWithFallback('legal_documents', [
            'id' => 'NULL',
            'title' => "''",
            'content' => "''",
            'updated_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listSystemSettings(): array
    {
        if (! $this->hasTable('system_settings')) {
            return [];
        }

        return $this->selectFromTableWithFallback('system_settings', [
            'setting_key' => "''",
            'setting_value' => "''",
            'updated_at' => 'NULL',
        ]);
    }

    /**
     * @return array<int, object>
     */
    public function listDashboardRegistry(): array
    {
        if (! $this->hasTable('dashboard_registry')) {
            return [];
        }

        return $this->selectFromTableWithFallback('dashboard_registry', [
            'id' => 'NULL',
            'role_key' => "''",
            'module_key' => "''",
            'label' => "''",
            'uri' => "''",
            'updated_at' => 'NULL',
        ]);
    }

    public function updateSystemSetting(string $settingKey, string $settingValue): void
    {
        if (! $this->hasTable('system_settings')) {
            return;
        }

        $columns = Schema::getColumnListing('system_settings');
        if (! in_array('setting_key', $columns, true) || ! in_array('setting_value', $columns, true)) {
            return;
        }

        $payload = ['setting_value' => $settingValue];
        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        DB::table('system_settings')->updateOrInsert(['setting_key' => $settingKey], $payload);
    }

    public function updateLegalDocument(int $id, string $title, string $content): void
    {
        if (! $this->hasTable('legal_documents')) {
            return;
        }

        $columns = Schema::getColumnListing('legal_documents');
        if (! in_array('id', $columns, true)) {
            return;
        }

        $payload = [];
        if (in_array('title', $columns, true)) {
            $payload['title'] = $title;
        }
        if (in_array('content', $columns, true)) {
            $payload['content'] = $content;
        }
        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }
        if ($payload === []) {
            return;
        }

        DB::table('legal_documents')->where('id', $id)->update($payload);
    }

    public function updateUserStatus(int $userId, string $status): void
    {
        if (! $this->hasTable('users')) {
            return;
        }

        $columns = Schema::getColumnListing('users');
        if (! in_array('status', $columns, true)) {
            return;
        }

        $payload = ['status' => $status];
        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        DB::table('users')
            ->where('id', $userId)
            ->update($payload);
    }

    public function updateUserRole(int $userId, string $roleKey): void
    {
        if (! $this->hasTable('users')) {
            return;
        }

        $roleKey = trim($roleKey);
        if ($roleKey === '') {
            return;
        }

        $columns = Schema::getColumnListing('users');
        if (! in_array('role', $columns, true) && ! in_array('role_id', $columns, true)) {
            return;
        }

        $normalizedRole = in_array($roleKey, self::PUBLIC_ROLES, true) ? $roleKey : strtoupper($roleKey);

        $payload = [];
        if (in_array('role', $columns, true)) {
            $payload['role'] = $normalizedRole;
        }

        if ($this->hasTable('roles') && in_array('role_id', $columns, true)) {
            $roleId = DB::table('roles')
                ->where(function ($query) use ($normalizedRole): void {
                    $query->where('role_key', $normalizedRole)
                        ->orWhereRaw('LOWER(role_key) = ?', [strtolower($normalizedRole)]);
                })
                ->value('id');
            if (is_numeric($roleId)) {
                $payload['role_id'] = (int) $roleId;
            }
        }

        if ($payload === []) {
            return;
        }

        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        DB::table('users')->where('id', $userId)->update($payload);
    }

    public function reviewVerification(int $businessProfileId, string $status, int $reviewedBy): void
    {
        if (! $this->hasTable('business_profiles')) {
            return;
        }

        DB::table('business_profiles')
            ->where('id', $businessProfileId)
            ->update([
                'verification_status' => $status,
                'updated_at' => now(),
            ]);

        if ($this->hasTable('verification_requests')) {
            $query = DB::table('verification_requests')
                ->where('business_profile_id', $businessProfileId)
                ->orderByDesc('id');

            $latest = $query->first();
            if ($latest !== null) {
                DB::table('verification_requests')->where('id', $latest->id)->update([
                    'status' => $status === 'verified' ? 'verified' : ($status === 'rejected' ? 'rejected' : 'needs_update'),
                    'reviewed_by' => $reviewedBy,
                    'reviewed_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function reviewOpportunity(int $opportunityId, string $status): void
    {
        if (! $this->hasTable('investment_opportunities')) {
            return;
        }

        $update = [
            'status' => $status,
            'updated_at' => now(),
        ];
        if ($status === 'published') {
            $update['verification_status'] = 'verified';
            $update['published_at'] = now();
        } elseif ($status === 'under_review') {
            $update['verification_status'] = 'needs_update';
        }

        DB::table('investment_opportunities')
            ->where('id', $opportunityId)
            ->update($update);
    }

    public function updateConnectionStatus(int $connectionId, string $status): void
    {
        if (! $this->hasTable('partner_connections')) {
            return;
        }

        DB::table('partner_connections')
            ->where('id', $connectionId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }

    private function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }

    private function safeCount(string $table, ?array $where = null): int
    {
        if (! $this->hasTable($table)) {
            return 0;
        }

        $query = DB::table($table);
        if (is_array($where) && count($where) === 2) {
            if (! Schema::hasColumn($table, (string) $where[0])) {
                return 0;
            }
            $query->where($where[0], $where[1]);
        }

        return (int) $query->count();
    }

    /**
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    private function applyUserListSelect($query, array $columns, string $alias): void
    {
        $parts = ["{$alias}.id", "{$alias}.email"];

        $parts[] = $this->userDisplayNameSql($alias, $columns).' as full_name';

        if (in_array('name', $columns, true)) {
            $parts[] = "{$alias}.name";
        }

        if (in_array('role', $columns, true)) {
            $parts[] = "{$alias}.role";
        } elseif (in_array('role_id', $columns, true) && $this->hasTable('roles')) {
            $query->leftJoin('roles as ru', 'ru.id', '=', "{$alias}.role_id");
            $parts[] = 'COALESCE(ru.role_key, '.$this->inferredRoleKeySql($alias, $columns).") as role";
        } else {
            $parts[] = $this->inferredRoleKeySql($alias, $columns).' as role';
        }

        if (in_array('status', $columns, true)) {
            $parts[] = "{$alias}.status";
        } else {
            $parts[] = "'active' as status";
        }

        if (in_array('created_at', $columns, true)) {
            $parts[] = "{$alias}.created_at";
        } else {
            $parts[] = 'NULL as created_at';
        }

        $query->selectRaw(implode(', ', $parts));
    }

    private function userDisplayNameSql(string $alias, array $columns): string
    {
        if (in_array('full_name', $columns, true) && in_array('name', $columns, true)) {
            return "COALESCE({$alias}.full_name, {$alias}.name, '')";
        }
        if (in_array('full_name', $columns, true)) {
            return "COALESCE({$alias}.full_name, '')";
        }
        if (in_array('name', $columns, true)) {
            return "COALESCE({$alias}.name, '')";
        }

        return "''";
    }

    private function inferredRoleKeySql(string $alias, array $columns): string
    {
        $hints = ["LOWER(COALESCE({$alias}.email, ''))"];
        if (in_array('full_name', $columns, true)) {
            $hints[] = "LOWER(COALESCE({$alias}.full_name, ''))";
        }
        if (in_array('name', $columns, true)) {
            $hints[] = "LOWER(COALESCE({$alias}.name, ''))";
        }
        $concat = implode(", ' ', ", $hints);

        return "CASE
            WHEN CONCAT({$concat}) LIKE '%super admin%' OR CONCAT({$concat}) LIKE '%superadmin%' THEN 'SUPER_ADMIN'
            WHEN CONCAT({$concat}) LIKE '%admin%' THEN 'admin'
            WHEN CONCAT({$concat}) LIKE '%investor%' THEN 'investor'
            WHEN CONCAT({$concat}) LIKE '%stakeholder%' THEN 'stakeholder'
            ELSE 'business'
        END";
    }

    /**
     * @param  array<string, string>  $columnsWithFallbackSql
     * @return array<int, object>
     */
    private function selectFromTableWithFallback(string $table, array $columnsWithFallbackSql): array
    {
        $columns = Schema::getColumnListing($table);
        $selectParts = [];

        foreach ($columnsWithFallbackSql as $column => $fallbackSql) {
            $selectParts[] = in_array($column, $columns, true)
                ? $column
                : sprintf('%s as %s', $fallbackSql, $column);
        }

        $orderByColumn = in_array('id', $columns, true)
            ? 'id'
            : ($columns[0] ?? null);

        $query = DB::table($table)
            ->selectRaw(implode(', ', $selectParts))
            ->limit(100);

        if (is_string($orderByColumn) && $orderByColumn !== '') {
            $query->orderByDesc($orderByColumn);
        }

        return $query->get()->all();
    }
}

