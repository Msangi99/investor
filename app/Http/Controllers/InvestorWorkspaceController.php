<?php

namespace App\Http\Controllers;

use App\Services\Investor\InvestorWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestorWorkspaceController extends Controller
{
    public function __construct(
        private readonly InvestorWorkspaceService $workspaceService
    ) {
    }

    public function dashboard(): View
    {
        return view('pages.investor.dashboard', $this->workspaceService->dashboardData($this->userId()));
    }

    public function profile(): View
    {
        return view('pages.investor.profile', $this->workspaceService->profileData($this->userId()));
    }

    public function saveProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'investor_name' => ['required', 'string', 'max:180'],
            'investor_type' => ['required', 'string', 'in:individual,angel,venture_capital,private_equity,corporate,foundation,development_partner,bank,other'],
            'preferred_sectors' => ['nullable', 'string'],
            'preferred_regions' => ['nullable', 'string'],
            'ticket_min' => ['nullable', 'numeric', 'min:0'],
            'ticket_max' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'investment_stage_interest' => ['nullable', 'string'],
        ]);

        $this->workspaceService->saveProfile($this->userId(), $validated);

        return back()->with('success', 'Investor profile updated.');
    }

    public function discover(): View
    {
        return view('pages.investor.discover', $this->workspaceService->discoverData($this->userId()));
    }

    public function saveShortlist(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opportunity_id' => ['required', 'integer', 'min:1'],
        ]);

        $this->workspaceService->saveShortlist($this->userId(), (int) $validated['opportunity_id']);

        return back()->with('success', 'Opportunity saved to shortlist.');
    }

    public function shortlist(): View
    {
        return view('pages.investor.shortlist', $this->workspaceService->shortlistData($this->userId()));
    }

    public function acceptProject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opportunity_id' => ['required', 'integer', 'min:1'],
        ]);

        $this->workspaceService->acceptProject($this->userId(), (int) $validated['opportunity_id']);

        return back()->with('success', 'Project accepted and saved in My Projects.');
    }

    public function updateShortlistStage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shortlist_id' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:saved,interested,contacted,meeting_requested,in_review,not_interested'],
        ]);

        $this->workspaceService->updateShortlistStage($this->userId(), (int) $validated['shortlist_id'], $validated['status']);

        return back()->with('success', 'Pipeline stage updated.');
    }

    public function pipeline(): View
    {
        return view('pages.investor.pipeline', $this->workspaceService->pipelineData($this->userId()));
    }

    public function myProjects(Request $request): View
    {
        return view('pages.investor.my-projects', $this->workspaceService->myProjectsData(
            $this->userId(),
            (int) $request->query('shortlist_id', 0)
        ));
    }

    public function verifiedBusinesses(): View
    {
        return view('pages.investor.verified-businesses', $this->workspaceService->verifiedBusinessesData($this->userId()));
    }

    public function meetings(): View
    {
        return view('pages.investor.meetings', $this->workspaceService->meetingsData($this->userId()));
    }

    public function insights(): View
    {
        return view('pages.investor.insights', $this->workspaceService->insightsData($this->userId()));
    }

    public function messages(): View
    {
        return view('pages.investor.messages', $this->workspaceService->messagesData($this->userId()));
    }

    public function settings(): View
    {
        return view('pages.investor.settings', $this->workspaceService->settingsData($this->userId()));
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:150'],
            'organization' => ['nullable', 'string', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'notification_email' => ['nullable', 'in:0,1'],
            'notification_messages' => ['nullable', 'in:0,1'],
            'notification_deals' => ['nullable', 'in:0,1'],
        ]);

        $this->workspaceService->saveSettings($this->userId(), $validated);

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
