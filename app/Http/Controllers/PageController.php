<?php

namespace App\Http\Controllers;

use App\Support\LegacyRoleMatrix;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    public function dashboardRedirect(): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rawRole = trim((string) ($_SESSION['user_role'] ?? ''));
        if ($rawRole === '') {
            return redirect('/login.php');
        }

        $role = LegacyRoleMatrix::normalizeRole($rawRole);

        return redirect(LegacyRoleMatrix::dashboardUriForRole($role));
    }

    // Public pages
    public function index(): View
    {
        return view('pages.index');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function ecosystem(): View
    {
        return view('pages.ecosystem');
    }

    public function opportunities(): View
    {
        return view('pages.opportunities');
    }

    public function verification(): View
    {
        return view('pages.verification');
    }

    public function verificationTrack(): View
    {
        return view('pages.verification-track');
    }

    public function verificationPolicy(): View
    {
        return view('pages.verification-policy');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    public function limitations(): View
    {
        return view('pages.limitations');
    }

    public function dashboards(): View
    {
        return view('pages.dashboards');
    }

    // Business pages
    public function businessDashboard(): View
    {
        return view('pages.business.dashboard');
    }

    public function businessProfile(): View
    {
        return view('pages.business.profile');
    }

    public function businessReadiness(): View
    {
        return view('pages.business.readiness');
    }

    public function businessDocuments(): View
    {
        return view('pages.business.documents');
    }

    public function businessOpportunities(): View
    {
        return view('pages.business.opportunities');
    }

    public function businessConnections(): View
    {
        return view('pages.business.connections');
    }

    public function businessInsights(): View
    {
        return view('pages.business.insights');
    }

    public function businessMessages(): View
    {
        return view('pages.business.messages');
    }

    public function businessSettings(): View
    {
        return view('pages.business.settings');
    }

    // Investor pages
    public function investorDashboard(): View
    {
        return view('pages.investor.dashboard');
    }

    public function investorProfile(): View
    {
        return view('pages.investor.profile');
    }

    public function investorDiscover(): View
    {
        return view('pages.investor.discover');
    }

    public function investorVerifiedBusinesses(): View
    {
        return view('pages.investor.verified-businesses');
    }

    public function investorShortlist(): View
    {
        return view('pages.investor.shortlist');
    }

    public function investorPipeline(): View
    {
        return view('pages.investor.pipeline');
    }

    public function investorMeetings(): View
    {
        return view('pages.investor.meetings');
    }

    public function investorInsights(): View
    {
        return view('pages.investor.insights');
    }

    public function investorMessages(): View
    {
        return view('pages.investor.messages');
    }

    public function investorSettings(): View
    {
        return view('pages.investor.settings');
    }

    // Stakeholder pages
    public function stakeholderDashboard(): View
    {
        return view('pages.stakeholder.dashboard');
    }

    public function stakeholderProfile(): View
    {
        return view('pages.stakeholder.profile');
    }

    public function stakeholderBusinesses(): View
    {
        return view('pages.stakeholder.businesses');
    }

    public function stakeholderRecommendations(): View
    {
        return view('pages.stakeholder.recommendations');
    }

    public function stakeholderConnections(): View
    {
        return view('pages.stakeholder.connections');
    }

    public function stakeholderFollowUps(): View
    {
        return view('pages.stakeholder.follow-ups');
    }

    public function stakeholderReports(): View
    {
        return view('pages.stakeholder.reports');
    }

    public function stakeholderInsights(): View
    {
        return view('pages.stakeholder.insights');
    }

    public function stakeholderMessages(): View
    {
        return view('pages.stakeholder.messages');
    }

    public function stakeholderSettings(): View
    {
        return view('pages.stakeholder.settings');
    }

    // Admin pages
    public function adminDashboard(): View
    {
        return view('pages.admin.dashboard');
    }

    public function adminProfile(): View
    {
        return view('pages.admin.profile');
    }

    public function adminUsers(): View
    {
        return view('pages.admin.user');
    }

    public function adminRoles(): View
    {
        return view('pages.admin.roles');
    }

    public function adminPermissions(): View
    {
        return view('pages.admin.permissions');
    }

    public function adminVerifications(): View
    {
        return view('pages.admin.verifications');
    }

    public function adminVerificationTrack(): View
    {
        return view('pages.admin.verification-track');
    }

    public function adminBusinesses(): View
    {
        return view('pages.admin.businesses');
    }

    public function adminInvestors(): View
    {
        return view('pages.admin.investors');
    }

    public function adminStakeholders(): View
    {
        return view('pages.admin.stakeholders');
    }

    public function adminOpportunities(): View
    {
        return view('pages.admin.opportunities');
    }

    public function adminUploads(): View
    {
        return view('pages.admin.uploads');
    }

    public function adminInsights(): View
    {
        return view('pages.admin.insights');
    }

    public function adminMessages(): View
    {
        return view('pages.admin.messages');
    }

    public function adminChatbot(): View
    {
        return view('pages.admin.chatbot');
    }

    public function adminAiAssistants(): View
    {
        return view('pages.admin.ai-assistants');
    }

    public function adminAiTools(): View
    {
        return view('pages.admin.ai-tools');
    }

    public function adminLegal(): View
    {
        return view('pages.admin.legal');
    }

    public function adminSettings(): View
    {
        return view('pages.admin.settings');
    }

    public function adminDashboards(): View
    {
        return view('pages.admin.dashboards');
    }
}
