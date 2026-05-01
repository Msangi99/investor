<?php

namespace App\Http\Controllers;

use App\Support\LegacyRoleMatrix;
use App\Services\Admin\AdminModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(private readonly AdminModuleService $adminModuleService) {}

    public function dashboard(): View
    {
        return $this->renderModule('overview', 'Admin Dashboard', $this->adminModuleService->stats());
    }

    public function profile(): View
    {
        $sessionRole = (string) ($_SESSION['user_role'] ?? 'SUPER_ADMIN');

        return $this->renderModule('profile', 'Admin Profile', [
            'adminName' => (string) ($_SESSION['user_name'] ?? 'Administrator'),
            'adminEmail' => (string) ($_SESSION['user_email'] ?? ''),
            'adminRole' => LegacyRoleMatrix::normalizeRole($sessionRole),
        ]);
    }

    public function users(Request $request): View
    {
        return $this->renderModule('users', 'Users', [
            'rows' => $this->adminModuleService->listUsers($request->query('q')),
        ]);
    }

    public function roles(): View
    {
        return $this->renderModule('roles', 'Roles & Permissions', [
            'rows' => $this->adminModuleService->listRoles(),
            'users' => $this->adminModuleService->listRoleUsers(request()->query('q')),
            'assignableRoles' => $this->adminModuleService->assignableRoleKeys(),
        ]);
    }

    public function permissions(): View
    {
        return $this->renderModule('roles', 'Permissions', [
            'rows' => $this->adminModuleService->listRoles(),
        ]);
    }

    public function verifications(): View
    {
        return $this->renderModule('verifications', 'Verifications', [
            'rows' => $this->adminModuleService->listBusinesses(),
        ]);
    }

    public function verificationTrack(): View
    {
        return $this->renderModule('verification-track', 'Verification Track', [
            'rows' => $this->adminModuleService->listBusinesses(),
        ]);
    }

    public function businesses(): View
    {
        return $this->renderModule('businesses', 'Businesses', [
            'rows' => $this->adminModuleService->listBusinesses(),
        ]);
    }

    public function investors(): View
    {
        return $this->renderModule('investors', 'Investors', [
            'rows' => $this->adminModuleService->listInvestors(),
        ]);
    }

    public function stakeholders(): View
    {
        return $this->renderModule('stakeholders', 'Stakeholders', [
            'rows' => $this->adminModuleService->listStakeholders(),
        ]);
    }

    public function opportunities(): View
    {
        return $this->renderModule('opportunities', 'Opportunities', [
            'rows' => $this->adminModuleService->listOpportunities(),
        ]);
    }

    public function uploads(): View
    {
        return $this->renderModule('uploads', 'Uploads', [
            'rows' => $this->adminModuleService->listUploads(),
        ]);
    }

    public function insights(): View
    {
        return $this->renderModule('insights', 'Insights', [
            'stats' => $this->adminModuleService->stats(),
            'opportunities' => $this->adminModuleService->listOpportunities(),
        ]);
    }

    public function messages(): View
    {
        return $this->renderModule('messages', 'Messages', [
            'rows' => $this->adminModuleService->listMessages(),
        ]);
    }

    public function chatbot(): View
    {
        return $this->renderModule('chatbot', 'Chatbot Logs', [
            'rows' => $this->adminModuleService->listChatbotLogs(),
        ]);
    }

    public function aiAssistants(): View
    {
        return $this->renderModule('ai-assistants', 'AI Assistants', [
            'rows' => $this->adminModuleService->listAiAssistants(),
        ]);
    }

    public function aiTools(): View
    {
        return $this->renderModule('ai-tools', 'AI Tools', [
            'rows' => $this->adminModuleService->listAiTools(),
        ]);
    }

    public function legal(): View
    {
        return $this->renderModule('legal', 'Legal Documents', [
            'rows' => $this->adminModuleService->listLegalDocuments(),
        ]);
    }

    public function settings(): View
    {
        return $this->renderModule('settings', 'System Settings', [
            'rows' => $this->adminModuleService->listSystemSettings(),
        ]);
    }

    public function dashboards(): View
    {
        return $this->renderModule('role-dashboard', 'Dashboard Registry', [
            'rows' => $this->adminModuleService->listDashboardRegistry(),
        ]);
    }

    public function updateSetting(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'setting_key' => ['required', 'string', 'max:150'],
            'setting_value' => ['nullable', 'string'],
        ]);

        $this->adminModuleService->updateSystemSetting(
            $validated['setting_key'],
            (string) ($validated['setting_value'] ?? '')
        );

        return back()->with('status', 'Setting updated successfully.');
    }

    public function updateLegal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $this->adminModuleService->updateLegalDocument(
            (int) $validated['id'],
            $validated['title'],
            $validated['content']
        );

        return back()->with('status', 'Legal document updated successfully.');
    }

    public function updateUserStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $this->adminModuleService->updateUserStatus(
            (int) $validated['user_id'],
            $validated['status']
        );

        return back()->with('status', 'User status updated successfully.');
    }

    public function updateUserRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'role_key' => ['required', 'string', 'max:80'],
        ]);

        $this->adminModuleService->updateUserRole(
            (int) $validated['user_id'],
            $validated['role_key']
        );

        return back()->with('status', 'User role updated successfully.');
    }

    public function assignRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'role_key' => ['required', 'string', 'max:80'],
        ]);

        $this->adminModuleService->updateUserRole(
            (int) $validated['user_id'],
            $validated['role_key']
        );

        return back()->with('status', 'Role assignment saved.');
    }

    public function reviewVerification(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_profile_id' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:verified,needs_update,rejected'],
        ]);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $reviewedBy = (int) ($_SESSION['user_id'] ?? 0);

        $this->adminModuleService->reviewVerification(
            (int) $validated['business_profile_id'],
            $validated['status'],
            $reviewedBy
        );

        return back()->with('status', 'Verification review saved.');
    }

    public function reviewOpportunity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opportunity_id' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:published,rejected'],
        ]);

        $this->adminModuleService->reviewOpportunity(
            (int) $validated['opportunity_id'],
            $validated['status']
        );

        return back()->with('status', 'Opportunity review updated.');
    }

    public function updateConnectionStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'connection_id' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:accepted,declined,in_progress,completed,closed,pending'],
        ]);

        $this->adminModuleService->updateConnectionStatus(
            (int) $validated['connection_id'],
            $validated['status']
        );

        return back()->with('status', 'Connection status updated.');
    }

    private function renderModule(string $moduleKey, string $pageTitle, array $data = []): View
    {
        return view('pages.admin.module', array_merge($data, [
            'pageTitle' => $pageTitle,
            'pageName' => 'admin-'.$moduleKey,
            'activeSidebar' => $moduleKey,
            'moduleKey' => $moduleKey,
        ]));
    }
}

