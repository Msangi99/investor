<?php

namespace App\Http\Middleware;

use App\Support\LegacyRoleMatrix;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLegacyRole
{
    public function handle(Request $request, Closure $next, string $expectedRoles, ?string $moduleKey = null): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rawRole = trim((string) ($_SESSION['user_role'] ?? ''));
        if ($rawRole === '') {
            return redirect('/login.php');
        }

        $role = LegacyRoleMatrix::normalizeRole($rawRole);
        $allowed = array_filter(array_map(
            static fn (string $allowedRole): string => LegacyRoleMatrix::normalizeRole($allowedRole),
            array_map('trim', explode('|', $expectedRoles))
        ));

        if (! in_array($role, $allowed, true)) {
            return redirect('/login.php');
        }

        $resolvedModuleKey = $moduleKey ?: LegacyRoleMatrix::moduleKeyFromUri($request->getPathInfo());
        if (! LegacyRoleMatrix::canAccessModule($role, $resolvedModuleKey)) {
            return redirect(LegacyRoleMatrix::dashboardUriForRole($role));
        }

        return $next($request);
    }
}
