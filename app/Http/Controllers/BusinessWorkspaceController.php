<?php

namespace App\Http\Controllers;

use App\Services\Business\BusinessWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessWorkspaceController extends Controller
{
    public function __construct(
        private readonly BusinessWorkspaceService $workspaceService
    ) {
    }

    public function profile(): View
    {
        return view('pages.business.profile', $this->workspaceService->profileData($this->userId()));
    }

    public function saveProfile(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'business_name' => ['required', 'string', 'max:180'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'tax_identification_number' => ['nullable', 'string', 'max:100'],
            'sector' => ['nullable', 'string', 'max:120'],
            'business_stage' => ['required', 'string', 'in:idea,prototype,mvp,early_revenue,growth,scale'],
            'region' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'problem_statement' => ['nullable', 'string'],
            'solution_summary' => ['nullable', 'string'],
            'target_market' => ['nullable', 'string'],
            'traction_summary' => ['nullable', 'string'],
            'funding_need_amount' => ['nullable', 'numeric', 'min:0'],
            'funding_currency' => ['nullable', 'string', 'max:10'],
            'funding_purpose' => ['nullable', 'string'],
            'jobs_current' => ['nullable', 'integer', 'min:0'],
            'jobs_potential' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->workspaceService->saveProfile($this->userId(), $payload);

        return back()->with('success', 'Business profile updated.');
    }

    public function readiness(): View
    {
        return view('pages.business.readiness', $this->workspaceService->readinessData($this->userId()));
    }

    public function saveReadiness(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'completed_items' => ['array'],
            'completed_items.*' => ['integer'],
        ]);

        $this->workspaceService->saveReadiness($this->userId(), $payload['completed_items'] ?? []);

        return back()->with('success', 'Readiness progress saved.');
    }

    public function documents(): View
    {
        return view('pages.business.documents', $this->workspaceService->documentsData($this->userId()));
    }

    public function saveDocument(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'document_type_id' => ['nullable', 'integer'],
            'document' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,png,jpg,jpeg'],
        ]);

        $profileId = (int) ($this->workspaceService->profileData($this->userId())['profile']->id ?? 0);
        $this->workspaceService->saveDocument($this->userId(), $profileId, $payload);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function opportunities(Request $request): View
    {
        return view('pages.business.opportunities', $this->workspaceService->opportunitiesData(
            $this->userId(),
            (int) $request->query('edit', 0)
        ));
    }

    public function saveOpportunity(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'opportunity_id' => ['nullable', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:220'],
            'summary' => ['required', 'string'],
            'document' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,png,jpg,jpeg'],
            'sector' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'funding_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'funding_type' => ['nullable', 'string', 'in:equity,debt,grant,partnership,asset_finance,other'],
            'stage' => ['nullable', 'string', 'in:idea,prototype,mvp,early_revenue,growth,scale'],
        ]);

        $isEdit = (int) ($payload['opportunity_id'] ?? 0) > 0;
        if (! $isEdit && ! $request->hasFile('document')) {
            return back()->withErrors(['document' => 'Proposal document is required when creating an opportunity.'])->withInput();
        }

        $payload['status'] = 'published';
        if ($isEdit) {
            $this->workspaceService->updateOpportunity($this->userId(), (int) $payload['opportunity_id'], $payload);
            return back()->with('success', 'Project proposal updated.');
        }

        $this->workspaceService->saveOpportunity($this->userId(), $payload);

        return back()->with('success', 'Project proposal submitted. It will be active after Super Admin verification.');
    }

    public function connections(): View
    {
        return view('pages.business.connections', $this->workspaceService->connectionsData($this->userId()));
    }

    public function saveConnection(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'receiver_user_id' => ['nullable', 'integer'],
            'opportunity_id' => ['nullable', 'integer'],
            'connection_type' => ['required', 'string', 'in:investment,bank_finance,government_support,mentorship,partnership,verification_support,other'],
            'subject' => ['required', 'string', 'max:220'],
            'message' => ['nullable', 'string'],
        ]);

        $this->workspaceService->saveConnection($this->userId(), $payload);

        return back()->with('success', 'Connection request sent.');
    }

    public function updateConnectionStatus(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'connection_id' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:accepted,declined'],
        ]);

        $this->workspaceService->updateConnectionStatus(
            $this->userId(),
            (int) $payload['connection_id'],
            $payload['status']
        );

        return back()->with('success', 'Connection response saved.');
    }

    public function insights(): View
    {
        return view('pages.business.insights', $this->workspaceService->insightsData($this->userId()));
    }

    public function messages(): View
    {
        return view('pages.business.messages', $this->workspaceService->messagesData($this->userId()));
    }

    public function saveMessage(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'receiver_user_id' => ['required', 'integer'],
            'connection_id' => ['nullable', 'integer'],
            'subject' => ['nullable', 'string', 'max:220'],
            'message' => ['required', 'string'],
        ]);

        $this->workspaceService->saveMessage($this->userId(), $payload);

        return back()->with('success', 'Message sent.');
    }

    public function settings(): View
    {
        return view('pages.business.settings', $this->workspaceService->settingsData($this->userId()));
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'full_name' => ['nullable', 'string', 'max:150'],
            'organization' => ['nullable', 'string', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'notification_email' => ['nullable', 'in:0,1'],
            'notification_messages' => ['nullable', 'in:0,1'],
            'notification_connections' => ['nullable', 'in:0,1'],
        ]);

        $this->workspaceService->saveSettings($this->userId(), $payload);

        return back()->with('success', 'Settings updated.');
    }

    private function userId(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) ($_SESSION['user_id'] ?? 0);
    }
}
