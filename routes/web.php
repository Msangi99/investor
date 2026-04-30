<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\BusinessWorkspaceController;
use App\Http\Controllers\InvestorWorkspaceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\StakeholderWorkspaceController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login.php', [AuthController::class, 'showLogin'])->name('login.legacy');
Route::post('/login.php', [AuthController::class, 'login'])->name('login.legacy.submit');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout.php', [AuthController::class, 'logout'])->name('logout.legacy');
Route::get('/logout.php', [AuthController::class, 'logout']);

// Dashboard redirect
Route::get('/dashboard', [PageController::class, 'dashboardRedirect'])->name('dashboard.redirect');

// Public pages
Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/index.php', [PageController::class, 'index']);
Route::get('/about.php', [PageController::class, 'about'])->name('about');
Route::get('/contact.php', [PageController::class, 'contact'])->name('contact');
Route::get('/ecosystem.php', [PageController::class, 'ecosystem'])->name('ecosystem');
Route::get('/opportunities.php', [PageController::class, 'opportunities'])->name('opportunities');
Route::get('/verification.php', [PageController::class, 'verification'])->name('verification');
Route::get('/verification-track.php', [PageController::class, 'verificationTrack'])->name('verification-track');
Route::get('/verification-policy.php', [PageController::class, 'verificationPolicy'])->name('verification-policy');
Route::get('/privacy.php', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms.php', [PageController::class, 'terms'])->name('terms');
Route::get('/faq.php', [PageController::class, 'faq'])->name('faq');
Route::get('/limitations.php', [PageController::class, 'limitations'])->name('limitations');
Route::get('/dashboards.php', [PageController::class, 'dashboards'])->name('dashboards');

// Business routes
Route::middleware(['legacy.role:business'])->group(function () {
    Route::get('/business/dashboard.php', [BusinessDashboardController::class, 'show'])->name('business.dashboard');
    Route::get('/business/profile.php', [BusinessWorkspaceController::class, 'profile'])->name('business.profile');
    Route::post('/business/profile.php', [BusinessWorkspaceController::class, 'saveProfile'])->name('business.profile.save');
    Route::get('/business/readiness.php', [BusinessWorkspaceController::class, 'readiness'])->name('business.readiness');
    Route::post('/business/readiness.php', [BusinessWorkspaceController::class, 'saveReadiness'])->name('business.readiness.save');
    Route::get('/business/documents.php', [BusinessWorkspaceController::class, 'documents'])->name('business.documents');
    Route::post('/business/documents.php', [BusinessWorkspaceController::class, 'saveDocument'])->name('business.documents.save');

    Route::middleware(['business.approved'])->group(function () {
        Route::get('/business/opportunities.php', [BusinessWorkspaceController::class, 'opportunities'])->name('business.opportunities');
        Route::post('/business/opportunities.php', [BusinessWorkspaceController::class, 'saveOpportunity'])->name('business.opportunities.save');
        Route::get('/business/connections.php', [BusinessWorkspaceController::class, 'connections'])->name('business.connections');
        Route::post('/business/connections.php', [BusinessWorkspaceController::class, 'saveConnection'])->name('business.connections.save');
        Route::post('/business/connections.php/status', [BusinessWorkspaceController::class, 'updateConnectionStatus'])->name('business.connections.status');
        Route::get('/business/insights.php', [BusinessWorkspaceController::class, 'insights'])->name('business.insights');
        Route::get('/business/messages.php', [BusinessWorkspaceController::class, 'messages'])->name('business.messages');
        Route::post('/business/messages.php', [BusinessWorkspaceController::class, 'saveMessage'])->name('business.messages.save');
        Route::get('/business/settings.php', [BusinessWorkspaceController::class, 'settings'])->name('business.settings');
        Route::post('/business/settings.php', [BusinessWorkspaceController::class, 'saveSettings'])->name('business.settings.save');
    });
});

// Investor routes
Route::middleware(['legacy.role:investor'])->group(function () {
    Route::get('/investor/dashboard.php', [PageController::class, 'investorDashboard'])->name('investor.dashboard');
    Route::get('/investor/profile.php', [PageController::class, 'investorProfile'])->name('investor.profile');
    Route::get('/investor/discover.php', [InvestorWorkspaceController::class, 'discover'])->name('investor.discover');
    Route::post('/investor/discover.php/shortlist', [InvestorWorkspaceController::class, 'saveShortlist'])->name('investor.discover.shortlist');
    Route::get('/investor/verified-businesses.php', [PageController::class, 'investorVerifiedBusinesses'])->name('investor.verified-businesses');
    Route::get('/investor/shortlist.php', [InvestorWorkspaceController::class, 'shortlist'])->name('investor.shortlist');
    Route::post('/investor/shortlist.php/stage', [InvestorWorkspaceController::class, 'updateShortlistStage'])->name('investor.shortlist.stage');
    Route::get('/investor/pipeline.php', [InvestorWorkspaceController::class, 'pipeline'])->name('investor.pipeline');
    Route::get('/investor/meetings.php', [PageController::class, 'investorMeetings'])->name('investor.meetings');
    Route::get('/investor/insights.php', [PageController::class, 'investorInsights'])->name('investor.insights');
    Route::get('/investor/messages.php', [PageController::class, 'investorMessages'])->name('investor.messages');
    Route::get('/investor/settings.php', [PageController::class, 'investorSettings'])->name('investor.settings');
});

// Stakeholder routes
Route::middleware(['legacy.role:stakeholder'])->group(function () {
    Route::get('/stakeholder/dashboard.php', [PageController::class, 'stakeholderDashboard'])->name('stakeholder.dashboard');
    Route::get('/stakeholder/profile.php', [PageController::class, 'stakeholderProfile'])->name('stakeholder.profile');
    Route::get('/stakeholder/businesses.php', [PageController::class, 'stakeholderBusinesses'])->name('stakeholder.businesses');
    Route::get('/stakeholder/recommendations.php', [StakeholderWorkspaceController::class, 'recommendations'])->name('stakeholder.recommendations');
    Route::post('/stakeholder/recommendations.php', [StakeholderWorkspaceController::class, 'saveRecommendation'])->name('stakeholder.recommendations.save');
    Route::get('/stakeholder/connections.php', [PageController::class, 'stakeholderConnections'])->name('stakeholder.connections');
    Route::get('/stakeholder/follow-ups.php', [PageController::class, 'stakeholderFollowUps'])->name('stakeholder.follow-ups');
    Route::get('/stakeholder/reports.php', [PageController::class, 'stakeholderReports'])->name('stakeholder.reports');
    Route::get('/stakeholder/insights.php', [PageController::class, 'stakeholderInsights'])->name('stakeholder.insights');
    Route::get('/stakeholder/messages.php', [PageController::class, 'stakeholderMessages'])->name('stakeholder.messages');
    Route::get('/stakeholder/settings.php', [PageController::class, 'stakeholderSettings'])->name('stakeholder.settings');
});

// Admin routes
Route::middleware(['legacy.role:SUPER_ADMIN|VERIFICATION_ADMIN|SUPPORT_ADMIN|FINANCE_ADMIN|CONTENT_ADMIN|PARTNERSHIP_ADMIN|ANALYTICS_ADMIN'])->group(function () {
    Route::get('/admin/dashboard.php', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profile.php', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('/admin/user.php', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/user.php/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.status');
    Route::post('/admin/user.php/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
    Route::get('/admin/roles.php', [AdminController::class, 'roles'])->name('admin.roles');
    Route::post('/admin/roles.php/assign', [AdminController::class, 'assignRole'])->name('admin.roles.assign');
    Route::get('/admin/permissions.php', [AdminController::class, 'permissions'])->name('admin.permissions');
    Route::get('/admin/verifications.php', [AdminController::class, 'verifications'])->name('admin.verifications');
    Route::post('/admin/verifications.php/review', [AdminController::class, 'reviewVerification'])->name('admin.verifications.review');
    Route::get('/admin/verification-track.php', [AdminController::class, 'verificationTrack'])->name('admin.verification-track');
    Route::get('/admin/businesses.php', [AdminController::class, 'businesses'])->name('admin.businesses');
    Route::get('/admin/investors.php', [AdminController::class, 'investors'])->name('admin.investors');
    Route::get('/admin/stakeholders.php', [AdminController::class, 'stakeholders'])->name('admin.stakeholders');
    Route::get('/admin/opportunities.php', [AdminController::class, 'opportunities'])->name('admin.opportunities');
    Route::post('/admin/opportunities.php/review', [AdminController::class, 'reviewOpportunity'])->name('admin.opportunities.review');
    Route::get('/admin/uploads.php', [AdminController::class, 'uploads'])->name('admin.uploads');
    Route::get('/admin/insights.php', [AdminController::class, 'insights'])->name('admin.insights');
    Route::get('/admin/messages.php', [AdminController::class, 'messages'])->name('admin.messages');
    Route::post('/admin/messages.php/connection-status', [AdminController::class, 'updateConnectionStatus'])->name('admin.messages.connection-status');
    Route::get('/admin/chatbot.php', [AdminController::class, 'chatbot'])->name('admin.chatbot');
    Route::get('/admin/ai-assistants.php', [AdminController::class, 'aiAssistants'])->name('admin.ai-assistants');
    Route::get('/admin/ai-tools.php', [AdminController::class, 'aiTools'])->name('admin.ai-tools');
    Route::get('/admin/legal.php', [AdminController::class, 'legal'])->name('admin.legal');
    Route::post('/admin/legal.php', [AdminController::class, 'updateLegal'])->name('admin.legal.update');
    Route::get('/admin/settings.php', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings.php', [AdminController::class, 'updateSetting'])->name('admin.settings.update');
    Route::get('/admin/dashboards.php', [AdminController::class, 'dashboards'])->name('admin.dashboards');
});
