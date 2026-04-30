<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    // -------------------------------------------------------------------------
    // Unauthenticated access
    // -------------------------------------------------------------------------

    public function test_legacy_public_route_is_registered(): void
    {
        $response = $this->get('/about.php');

        $response->assertStatus(200);
    }

    public function test_protected_business_route_redirects_when_not_authenticated(): void
    {
        $response = $this->get('/business/dashboard.php');

        $response->assertRedirect('/login.php');
    }

    public function test_protected_investor_route_redirects_when_not_authenticated(): void
    {
        $response = $this->get('/investor/discover.php');

        $response->assertRedirect('/login.php');
    }

    public function test_protected_stakeholder_route_redirects_when_not_authenticated(): void
    {
        $response = $this->get('/stakeholder/businesses.php');

        $response->assertRedirect('/login.php');
    }

    public function test_protected_admin_route_redirects_when_not_authenticated(): void
    {
        $response = $this->get('/admin/dashboard.php');

        $response->assertRedirect('/login.php');
    }

    // -------------------------------------------------------------------------
    // Dashboard redirect per role
    // -------------------------------------------------------------------------

    public function test_dashboard_redirect_business(): void
    {
        $this->setSessionRole('business');

        $this->get('/dashboard')->assertRedirect('/business/dashboard.php');
    }

    public function test_dashboard_redirect_investor(): void
    {
        $this->setSessionRole('investor');

        $this->get('/dashboard')->assertRedirect('/investor/dashboard.php');
    }

    public function test_dashboard_redirect_stakeholder(): void
    {
        $this->setSessionRole('stakeholder');

        $this->get('/dashboard')->assertRedirect('/stakeholder/dashboard.php');
    }

    public function test_dashboard_redirect_super_admin(): void
    {
        $this->setSessionRole('SUPER_ADMIN');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    public function test_dashboard_redirect_verification_admin(): void
    {
        $this->setSessionRole('VERIFICATION_ADMIN');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    public function test_dashboard_redirect_support_admin(): void
    {
        $this->setSessionRole('SUPPORT_ADMIN');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    public function test_dashboard_redirect_finance_admin(): void
    {
        $this->setSessionRole('FINANCE_ADMIN');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    public function test_dashboard_redirect_content_admin(): void
    {
        $this->setSessionRole('CONTENT_ADMIN');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    public function test_dashboard_redirect_partnership_admin(): void
    {
        $this->setSessionRole('PARTNERSHIP_ADMIN');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    public function test_dashboard_redirect_analytics_admin(): void
    {
        $this->setSessionRole('ANALYTICS_ADMIN');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    // -------------------------------------------------------------------------
    // Alias normalization (e.g. lowercase from DB)
    // -------------------------------------------------------------------------

    public function test_dashboard_redirect_uses_alias_for_lowercase_super_admin(): void
    {
        $this->setSessionRole('super_admin');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    public function test_dashboard_redirect_uses_alias_for_admin(): void
    {
        $this->setSessionRole('admin');

        $this->get('/dashboard')->assertRedirect('/admin/dashboard.php');
    }

    // -------------------------------------------------------------------------
    // Cross-role denial (wrong area)
    // -------------------------------------------------------------------------

    public function test_business_role_cannot_open_investor_route(): void
    {
        $this->setSessionRole('business');

        $this->get('/investor/discover.php')->assertRedirect('/login.php');
    }

    public function test_investor_role_cannot_open_business_route(): void
    {
        $this->setSessionRole('investor');

        $this->get('/business/profile.php')->assertRedirect('/login.php');
    }

    public function test_stakeholder_cannot_open_admin_route(): void
    {
        $this->setSessionRole('stakeholder');

        $this->get('/admin/dashboard.php')->assertRedirect('/login.php');
    }

    public function test_business_cannot_open_admin_route(): void
    {
        $this->setSessionRole('business');

        $this->get('/admin/user.php')->assertRedirect('/login.php');
    }

    // -------------------------------------------------------------------------
    // Module-level access: admin sub-role allowed paths
    // -------------------------------------------------------------------------

    public function test_content_admin_can_access_ai_tools(): void
    {
        $this->setSessionRole('CONTENT_ADMIN');

        $this->get('/admin/ai-tools.php')->assertStatus(200);
    }

    public function test_content_admin_can_access_ai_assistants(): void
    {
        $this->setSessionRole('CONTENT_ADMIN');

        $this->get('/admin/ai-assistants.php')->assertStatus(200);
    }

    public function test_content_admin_can_access_legal(): void
    {
        $this->setSessionRole('CONTENT_ADMIN');

        $this->get('/admin/legal.php')->assertStatus(200);
    }

    public function test_super_admin_can_access_all_admin_modules(): void
    {
        $this->setSessionRole('SUPER_ADMIN');

        foreach ([
            '/admin/dashboard.php',
            '/admin/profile.php',
            '/admin/user.php',
            '/admin/roles.php',
            '/admin/verifications.php',
            '/admin/verification-track.php',
            '/admin/businesses.php',
            '/admin/investors.php',
            '/admin/stakeholders.php',
            '/admin/opportunities.php',
            '/admin/uploads.php',
            '/admin/insights.php',
            '/admin/messages.php',
            '/admin/chatbot.php',
            '/admin/ai-assistants.php',
            '/admin/ai-tools.php',
            '/admin/legal.php',
            '/admin/settings.php',
        ] as $uri) {
            $this->get($uri)->assertStatus(200, "SUPER_ADMIN should access {$uri}");
        }
    }

    public function test_analytics_admin_can_access_role_dashboard(): void
    {
        $this->setSessionRole('ANALYTICS_ADMIN');

        $this->get('/admin/dashboards.php')->assertStatus(200);
    }

    public function test_verification_admin_can_access_verifications(): void
    {
        $this->setSessionRole('VERIFICATION_ADMIN');

        $this->get('/admin/verifications.php')->assertStatus(200);
    }

    public function test_verification_admin_can_access_verification_track(): void
    {
        $this->setSessionRole('VERIFICATION_ADMIN');

        $this->get('/admin/verification-track.php')->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Module-level access: admin sub-role denied paths
    // -------------------------------------------------------------------------

    public function test_support_admin_denied_ai_tools(): void
    {
        $this->setSessionRole('SUPPORT_ADMIN');

        $this->get('/admin/ai-tools.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_support_admin_denied_verifications(): void
    {
        $this->setSessionRole('SUPPORT_ADMIN');

        $this->get('/admin/verifications.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_support_admin_denied_legal(): void
    {
        $this->setSessionRole('SUPPORT_ADMIN');

        $this->get('/admin/legal.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_support_admin_denied_opportunities(): void
    {
        $this->setSessionRole('SUPPORT_ADMIN');

        $this->get('/admin/opportunities.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_verification_admin_denied_ai_tools(): void
    {
        $this->setSessionRole('VERIFICATION_ADMIN');

        $this->get('/admin/ai-tools.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_verification_admin_denied_roles(): void
    {
        $this->setSessionRole('VERIFICATION_ADMIN');

        $this->get('/admin/roles.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_finance_admin_denied_ai_tools(): void
    {
        $this->setSessionRole('FINANCE_ADMIN');

        $this->get('/admin/ai-tools.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_finance_admin_denied_legal(): void
    {
        $this->setSessionRole('FINANCE_ADMIN');

        $this->get('/admin/legal.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_finance_admin_denied_chatbot(): void
    {
        $this->setSessionRole('FINANCE_ADMIN');

        $this->get('/admin/chatbot.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_partnership_admin_denied_ai_tools(): void
    {
        $this->setSessionRole('PARTNERSHIP_ADMIN');

        $this->get('/admin/ai-tools.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_partnership_admin_denied_verifications(): void
    {
        $this->setSessionRole('PARTNERSHIP_ADMIN');

        $this->get('/admin/verifications.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_analytics_admin_denied_ai_tools(): void
    {
        $this->setSessionRole('ANALYTICS_ADMIN');

        $this->get('/admin/ai-tools.php')->assertRedirect('/admin/dashboard.php');
    }

    public function test_analytics_admin_denied_legal(): void
    {
        $this->setSessionRole('ANALYTICS_ADMIN');

        $this->get('/admin/legal.php')->assertRedirect('/admin/dashboard.php');
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function setSessionRole(string $role): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_role'] = $role;
    }
}
