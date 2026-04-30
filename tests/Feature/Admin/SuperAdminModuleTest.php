<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class SuperAdminModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    public function test_super_admin_can_open_all_admin_modules(): void
    {
        $this->setSessionRole('SUPER_ADMIN');

        $uris = [
            '/admin/dashboard.php',
            '/admin/profile.php',
            '/admin/user.php',
            '/admin/roles.php',
            '/admin/permissions.php',
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
        ];

        foreach ($uris as $uri) {
            $response = $this->get($uri);
            $response->assertStatus(200);
        }
    }

    public function test_super_admin_is_redirected_from_analytics_only_dashboard_registry(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->get('/admin/dashboards.php');
        $response->assertRedirect('/admin/dashboard.php');
    }

    public function test_super_admin_users_page_supports_search_query(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->get('/admin/user.php?q=demo');
        $response->assertStatus(200);
    }

    public function test_settings_update_requires_setting_key(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->post('/admin/settings.php', ['setting_value' => 'x']);
        $response->assertSessionHasErrors('setting_key');
    }

    public function test_legal_update_requires_required_fields(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->post('/admin/legal.php', ['id' => 1]);
        $response->assertSessionHasErrors(['title', 'content']);
    }

    public function test_user_status_update_requires_allowed_status(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->post('/admin/user.php/status', [
            'user_id' => 1,
            'status' => 'archived',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_user_role_update_requires_role_key(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->post('/admin/user.php/role', [
            'user_id' => 1,
        ]);

        $response->assertSessionHasErrors('role_key');
    }

    public function test_roles_page_renders_role_assignment_sections(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->get('/admin/roles.php');

        $response->assertStatus(200);
        $response->assertSeeText('Role Catalog');
        $response->assertSeeText('User Role Assignment');
    }

    public function test_roles_assign_endpoint_requires_user_and_role(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->post('/admin/roles.php/assign', []);

        $response->assertSessionHasErrors(['user_id', 'role_key']);
    }

    public function test_roles_assign_endpoint_accepts_valid_payload(): void
    {
        $this->setSessionRole('SUPER_ADMIN');
        $response = $this->post('/admin/roles.php/assign', [
            'user_id' => 1,
            'role_key' => 'SUPER_ADMIN',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status', 'Role assignment saved.');
    }

    private function setSessionRole(string $role): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_role'] = $role;
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Super Admin';
        $_SESSION['user_email'] = 'superadmin@example.test';
    }
}

