<?php

namespace Tests\Unit;

use App\Support\LegacyRoleMatrix;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LegacyRoleMatrixTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Alias / normalization
    // -------------------------------------------------------------------------

    public function test_role_aliases_normalize_all_admin_variants(): void
    {
        $this->assertSame('SUPER_ADMIN', LegacyRoleMatrix::normalizeRole('admin'));
        $this->assertSame('SUPER_ADMIN', LegacyRoleMatrix::normalizeRole('super_admin'));
        $this->assertSame('SUPER_ADMIN', LegacyRoleMatrix::normalizeRole('SUPER_ADMIN'));
        $this->assertSame('VERIFICATION_ADMIN', LegacyRoleMatrix::normalizeRole('verification_admin'));
        $this->assertSame('VERIFICATION_ADMIN', LegacyRoleMatrix::normalizeRole('VERIFICATION_ADMIN'));
        $this->assertSame('SUPPORT_ADMIN', LegacyRoleMatrix::normalizeRole('support_admin'));
        $this->assertSame('FINANCE_ADMIN', LegacyRoleMatrix::normalizeRole('finance_admin'));
        $this->assertSame('CONTENT_ADMIN', LegacyRoleMatrix::normalizeRole('content_admin'));
        $this->assertSame('PARTNERSHIP_ADMIN', LegacyRoleMatrix::normalizeRole('partnership_admin'));
        $this->assertSame('ANALYTICS_ADMIN', LegacyRoleMatrix::normalizeRole('analytics_admin'));
    }

    public function test_public_roles_normalize_to_themselves(): void
    {
        $this->assertSame('business', LegacyRoleMatrix::normalizeRole('business'));
        $this->assertSame('investor', LegacyRoleMatrix::normalizeRole('investor'));
        $this->assertSame('stakeholder', LegacyRoleMatrix::normalizeRole('stakeholder'));
    }

    public function test_normalization_is_idempotent(): void
    {
        $roles = ['business', 'investor', 'stakeholder', 'SUPER_ADMIN', 'VERIFICATION_ADMIN',
            'SUPPORT_ADMIN', 'FINANCE_ADMIN', 'CONTENT_ADMIN', 'PARTNERSHIP_ADMIN', 'ANALYTICS_ADMIN'];

        foreach ($roles as $role) {
            $once = LegacyRoleMatrix::normalizeRole($role);
            $twice = LegacyRoleMatrix::normalizeRole($once);
            $this->assertSame($once, $twice, "Normalization must be idempotent for {$role}");
        }
    }

    // -------------------------------------------------------------------------
    // Module counts per role (exact spec match)
    // -------------------------------------------------------------------------

    #[DataProvider('roleModuleCountProvider')]
    public function test_role_has_correct_module_count(string $role, int $expectedCount): void
    {
        $modules = LegacyRoleMatrix::modulesForRole($role);
        $this->assertCount($expectedCount, $modules, "Role {$role} should have {$expectedCount} modules");
    }

    /** @return array<string, array{string, int}> */
    public static function roleModuleCountProvider(): array
    {
        return [
            'business'          => ['business', 9],
            'investor'          => ['investor', 10],
            'stakeholder'       => ['stakeholder', 10],
            'SUPER_ADMIN'       => ['SUPER_ADMIN', 18],
            'VERIFICATION_ADMIN'=> ['VERIFICATION_ADMIN', 7],
            'SUPPORT_ADMIN'     => ['SUPPORT_ADMIN', 8],
            'FINANCE_ADMIN'     => ['FINANCE_ADMIN', 8],
            'CONTENT_ADMIN'     => ['CONTENT_ADMIN', 10],
            'PARTNERSHIP_ADMIN' => ['PARTNERSHIP_ADMIN', 8],
            'ANALYTICS_ADMIN'   => ['ANALYTICS_ADMIN', 9],
        ];
    }

    // -------------------------------------------------------------------------
    // Required module keys per role
    // -------------------------------------------------------------------------

    #[DataProvider('roleRequiredModulesProvider')]
    public function test_role_has_required_module_keys(string $role, string $moduleKey): void
    {
        $modules = LegacyRoleMatrix::modulesForRole($role);
        $this->assertArrayHasKey($moduleKey, $modules, "Role {$role} should have module '{$moduleKey}'");
    }

    /** @return array<string, array{string, string}> */
    public static function roleRequiredModulesProvider(): array
    {
        return [
            'business:readiness'             => ['business', 'readiness'],
            'business:documents'             => ['business', 'documents'],
            'business:connections'           => ['business', 'connections'],
            'investor:discover'              => ['investor', 'discover'],
            'investor:verified-businesses'   => ['investor', 'verified-businesses'],
            'investor:shortlist'             => ['investor', 'shortlist'],
            'investor:pipeline'              => ['investor', 'pipeline'],
            'investor:meetings'              => ['investor', 'meetings'],
            'stakeholder:recommendations'    => ['stakeholder', 'recommendations'],
            'stakeholder:follow-ups'         => ['stakeholder', 'follow-ups'],
            'stakeholder:reports'            => ['stakeholder', 'reports'],
            'SUPER_ADMIN:roles'              => ['SUPER_ADMIN', 'roles'],
            'SUPER_ADMIN:verification-track' => ['SUPER_ADMIN', 'verification-track'],
            'SUPER_ADMIN:ai-assistants'      => ['SUPER_ADMIN', 'ai-assistants'],
            'SUPER_ADMIN:ai-tools'           => ['SUPER_ADMIN', 'ai-tools'],
            'SUPER_ADMIN:legal'              => ['SUPER_ADMIN', 'legal'],
            'SUPER_ADMIN:chatbot'            => ['SUPER_ADMIN', 'chatbot'],
            'VERIFICATION_ADMIN:verifications'       => ['VERIFICATION_ADMIN', 'verifications'],
            'VERIFICATION_ADMIN:verification-track'  => ['VERIFICATION_ADMIN', 'verification-track'],
            'VERIFICATION_ADMIN:uploads'             => ['VERIFICATION_ADMIN', 'uploads'],
            'SUPPORT_ADMIN:chatbot'          => ['SUPPORT_ADMIN', 'chatbot'],
            'SUPPORT_ADMIN:users'            => ['SUPPORT_ADMIN', 'users'],
            'FINANCE_ADMIN:opportunities'    => ['FINANCE_ADMIN', 'opportunities'],
            'FINANCE_ADMIN:uploads'          => ['FINANCE_ADMIN', 'uploads'],
            'CONTENT_ADMIN:ai-tools'         => ['CONTENT_ADMIN', 'ai-tools'],
            'CONTENT_ADMIN:ai-assistants'    => ['CONTENT_ADMIN', 'ai-assistants'],
            'CONTENT_ADMIN:legal'            => ['CONTENT_ADMIN', 'legal'],
            'CONTENT_ADMIN:chatbot'          => ['CONTENT_ADMIN', 'chatbot'],
            'PARTNERSHIP_ADMIN:stakeholders' => ['PARTNERSHIP_ADMIN', 'stakeholders'],
            'ANALYTICS_ADMIN:role-dashboard' => ['ANALYTICS_ADMIN', 'role-dashboard'],
            'ANALYTICS_ADMIN:verifications'  => ['ANALYTICS_ADMIN', 'verifications'],
        ];
    }

    // -------------------------------------------------------------------------
    // Denied modules (spec exclusions)
    // -------------------------------------------------------------------------

    #[DataProvider('roleDeniedModulesProvider')]
    public function test_role_does_not_have_denied_module(string $role, string $moduleKey): void
    {
        $this->assertFalse(
            LegacyRoleMatrix::canAccessModule($role, $moduleKey),
            "Role {$role} must NOT have access to module '{$moduleKey}'"
        );
    }

    /** @return array<string, array{string, string}> */
    public static function roleDeniedModulesProvider(): array
    {
        return [
            'SUPPORT_ADMIN:ai-tools'          => ['SUPPORT_ADMIN', 'ai-tools'],
            'SUPPORT_ADMIN:ai-assistants'     => ['SUPPORT_ADMIN', 'ai-assistants'],
            'SUPPORT_ADMIN:legal'             => ['SUPPORT_ADMIN', 'legal'],
            'SUPPORT_ADMIN:verifications'     => ['SUPPORT_ADMIN', 'verifications'],
            'SUPPORT_ADMIN:roles'             => ['SUPPORT_ADMIN', 'roles'],
            'SUPPORT_ADMIN:insights'          => ['SUPPORT_ADMIN', 'insights'],
            'SUPPORT_ADMIN:opportunities'     => ['SUPPORT_ADMIN', 'opportunities'],
            'VERIFICATION_ADMIN:ai-tools'     => ['VERIFICATION_ADMIN', 'ai-tools'],
            'VERIFICATION_ADMIN:legal'        => ['VERIFICATION_ADMIN', 'legal'],
            'VERIFICATION_ADMIN:roles'        => ['VERIFICATION_ADMIN', 'roles'],
            'VERIFICATION_ADMIN:insights'     => ['VERIFICATION_ADMIN', 'insights'],
            'VERIFICATION_ADMIN:opportunities'=> ['VERIFICATION_ADMIN', 'opportunities'],
            'FINANCE_ADMIN:ai-tools'          => ['FINANCE_ADMIN', 'ai-tools'],
            'FINANCE_ADMIN:legal'             => ['FINANCE_ADMIN', 'legal'],
            'FINANCE_ADMIN:chatbot'           => ['FINANCE_ADMIN', 'chatbot'],
            'FINANCE_ADMIN:roles'             => ['FINANCE_ADMIN', 'roles'],
            'PARTNERSHIP_ADMIN:ai-tools'      => ['PARTNERSHIP_ADMIN', 'ai-tools'],
            'PARTNERSHIP_ADMIN:legal'         => ['PARTNERSHIP_ADMIN', 'legal'],
            'PARTNERSHIP_ADMIN:verifications' => ['PARTNERSHIP_ADMIN', 'verifications'],
            'ANALYTICS_ADMIN:ai-tools'        => ['ANALYTICS_ADMIN', 'ai-tools'],
            'ANALYTICS_ADMIN:legal'           => ['ANALYTICS_ADMIN', 'legal'],
            'ANALYTICS_ADMIN:roles'           => ['ANALYTICS_ADMIN', 'roles'],
            'SUPER_ADMIN:role-dashboard'      => ['SUPER_ADMIN', 'role-dashboard'],
            'business:discover'               => ['business', 'discover'],
            'investor:readiness'              => ['investor', 'readiness'],
            'stakeholder:pipeline'            => ['stakeholder', 'pipeline'],
        ];
    }

    // -------------------------------------------------------------------------
    // URI → module-key mapping
    // -------------------------------------------------------------------------

    #[DataProvider('uriModuleMapProvider')]
    public function test_uri_resolves_to_expected_module_key(string $uri, string $expectedKey): void
    {
        $this->assertSame($expectedKey, LegacyRoleMatrix::moduleKeyFromUri($uri));
    }

    /** @return array<string, array{string, string}> */
    public static function uriModuleMapProvider(): array
    {
        return [
            '/business/dashboard.php'          => ['/business/dashboard.php', 'overview'],
            '/business/readiness.php'          => ['/business/readiness.php', 'readiness'],
            '/business/documents.php'          => ['/business/documents.php', 'documents'],
            '/investor/discover.php'           => ['/investor/discover.php', 'discover'],
            '/investor/verified-businesses.php'=> ['/investor/verified-businesses.php', 'verified-businesses'],
            '/investor/pipeline.php'           => ['/investor/pipeline.php', 'pipeline'],
            '/investor/meetings.php'           => ['/investor/meetings.php', 'meetings'],
            '/stakeholder/follow-ups.php'      => ['/stakeholder/follow-ups.php', 'follow-ups'],
            '/stakeholder/recommendations.php' => ['/stakeholder/recommendations.php', 'recommendations'],
            '/admin/dashboard.php'             => ['/admin/dashboard.php', 'overview'],
            '/admin/verifications.php'         => ['/admin/verifications.php', 'verifications'],
            '/admin/verification-track.php'    => ['/admin/verification-track.php', 'verification-track'],
            '/admin/ai-tools.php'              => ['/admin/ai-tools.php', 'ai-tools'],
            '/admin/ai-assistants.php'         => ['/admin/ai-assistants.php', 'ai-assistants'],
            '/admin/chatbot.php'               => ['/admin/chatbot.php', 'chatbot'],
            '/admin/legal.php'                 => ['/admin/legal.php', 'legal'],
            '/admin/roles.php'                 => ['/admin/roles.php', 'roles'],
            '/admin/permissions.php'           => ['/admin/permissions.php', 'roles'],
            '/admin/dashboards.php'            => ['/admin/dashboards.php', 'role-dashboard'],
            '/admin/user.php'                  => ['/admin/user.php', 'users'],
        ];
    }

    // -------------------------------------------------------------------------
    // Dashboard URI per role
    // -------------------------------------------------------------------------

    #[DataProvider('dashboardUriProvider')]
    public function test_dashboard_uri_for_role(string $role, string $expectedUri): void
    {
        $this->assertSame($expectedUri, LegacyRoleMatrix::dashboardUriForRole($role));
    }

    /** @return array<string, array{string, string}> */
    public static function dashboardUriProvider(): array
    {
        return [
            'business'          => ['business', '/business/dashboard.php'],
            'investor'          => ['investor', '/investor/dashboard.php'],
            'stakeholder'       => ['stakeholder', '/stakeholder/dashboard.php'],
            'SUPER_ADMIN'       => ['SUPER_ADMIN', '/admin/dashboard.php'],
            'VERIFICATION_ADMIN'=> ['VERIFICATION_ADMIN', '/admin/dashboard.php'],
            'SUPPORT_ADMIN'     => ['SUPPORT_ADMIN', '/admin/dashboard.php'],
            'FINANCE_ADMIN'     => ['FINANCE_ADMIN', '/admin/dashboard.php'],
            'CONTENT_ADMIN'     => ['CONTENT_ADMIN', '/admin/dashboard.php'],
            'PARTNERSHIP_ADMIN' => ['PARTNERSHIP_ADMIN', '/admin/dashboard.php'],
            'ANALYTICS_ADMIN'   => ['ANALYTICS_ADMIN', '/admin/dashboard.php'],
            'alias:admin'       => ['admin', '/admin/dashboard.php'],
            'alias:super_admin' => ['super_admin', '/admin/dashboard.php'],
        ];
    }

    // -------------------------------------------------------------------------
    // allRoles() completeness
    // -------------------------------------------------------------------------

    public function test_all_roles_returns_all_10_role_keys(): void
    {
        $roles = LegacyRoleMatrix::allRoles();

        $this->assertCount(10, $roles);

        $expected = [
            'business', 'investor', 'stakeholder',
            'SUPER_ADMIN', 'VERIFICATION_ADMIN', 'SUPPORT_ADMIN',
            'FINANCE_ADMIN', 'CONTENT_ADMIN', 'PARTNERSHIP_ADMIN', 'ANALYTICS_ADMIN',
        ];

        foreach ($expected as $roleKey) {
            $this->assertContains($roleKey, $roles, "allRoles() must include '{$roleKey}'");
        }
    }
}
