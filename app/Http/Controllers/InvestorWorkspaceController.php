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

    private function userId(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) ($_SESSION['user_id'] ?? 0);
    }
}
