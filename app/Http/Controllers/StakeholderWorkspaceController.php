<?php

namespace App\Http\Controllers;

use App\Services\Stakeholder\StakeholderWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StakeholderWorkspaceController extends Controller
{
    public function __construct(
        private readonly StakeholderWorkspaceService $workspaceService
    ) {
    }

    public function recommendations(): View
    {
        return view('pages.stakeholder.recommendations', $this->workspaceService->recommendationsData($this->userId()));
    }

    public function saveRecommendation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'investor_user_id' => ['required', 'integer', 'min:1'],
            'subject' => ['required', 'string', 'max:220'],
            'message' => ['nullable', 'string'],
        ]);

        $this->workspaceService->saveRecommendation($this->userId(), $validated);

        return back()->with('success', 'Recommendation submitted.');
    }

    private function userId(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) ($_SESSION['user_id'] ?? 0);
    }
}
