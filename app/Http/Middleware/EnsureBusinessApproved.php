<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            return redirect('/login.php');
        }

        if (! $this->isBusinessApproved($userId)) {
            return redirect()
                ->route('business.dashboard')
                ->with('error', 'Access is limited until Super Admin approval and document inspection are completed.');
        }

        return $next($request);
    }

    private function isBusinessApproved(int $userId): bool
    {
        if (! Schema::hasTable('business_profiles')) {
            return false;
        }

        $verificationStatus = (string) (
            DB::table('business_profiles')
                ->where('user_id', $userId)
                ->value('verification_status') ?? ''
        );

        if ($verificationStatus !== 'verified') {
            return false;
        }

        if (! Schema::hasTable('uploads')) {
            return false;
        }

        return DB::table('uploads')
            ->where('user_id', $userId)
            ->where('related_type', 'business')
            ->where('upload_status', 'approved')
            ->exists();
    }
}
