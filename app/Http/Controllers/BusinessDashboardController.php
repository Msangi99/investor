<?php

namespace App\Http\Controllers;

use App\Services\Business\BusinessDashboardService;
use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    public function __construct(
        private readonly BusinessDashboardService $dashboardService
    ) {
    }

    public function show(): View
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $userName = (string) ($_SESSION['user_name'] ?? 'Business User');

        return view('pages.business.dashboard', [
            'dashboard' => $this->dashboardService->buildForUser($userId),
            'userName' => $userName,
        ]);
    }
}
