<?php

namespace App\Http\Controllers;

use App\Support\LegacyRoleMatrix;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyPageController extends Controller
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

    public function render(Request $request, string $legacyPath): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rawRole = trim((string) ($_SESSION['user_role'] ?? ''));
        $isAuthenticated = $rawRole !== '';

        $role = $isAuthenticated ? LegacyRoleMatrix::normalizeRole($rawRole) : null;
        $allowedModules = $role !== null ? LegacyRoleMatrix::modulesForRole($role) : [];
        $dashboardUri = $role !== null ? LegacyRoleMatrix::dashboardUriForRole($role) : '/dashboard';

        if ($isAuthenticated) {
            $_SESSION['legacy_allowed_modules'] = $allowedModules;
            $_SESSION['legacy_role'] = $role;
        }

        $legacyRoot = config('legacy.root');
        $absolutePath = $legacyRoot.DIRECTORY_SEPARATOR.$legacyPath;
        $legacyData = [
            'role' => $role,
            'dashboardUri' => $dashboardUri,
            'modules' => $allowedModules,
        ];

        if (! is_file($absolutePath)) {
            return response()->view('legacy.page', [
                'content' => '<p>Legacy page is not yet available for this route.</p>',
                'legacyPath' => $legacyPath,
                'legacyData' => $legacyData,
            ]);
        }

        $previousCwd = getcwd();
        $previousScriptName = $_SERVER['SCRIPT_NAME'] ?? null;
        $previousPhpSelf = $_SERVER['PHP_SELF'] ?? null;
        $previousRequestUri = $_SERVER['REQUEST_URI'] ?? null;
        $previousErrorLevel = error_reporting();
        $previousDisplayErrors = ini_get('display_errors');

        $_SERVER['SCRIPT_NAME'] = '/'.$legacyPath;
        $_SERVER['PHP_SELF'] = '/'.$legacyPath;
        $_SERVER['REQUEST_URI'] = $request->getRequestUri();
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
        ini_set('display_errors', '0');

        ob_start();

        try {
            chdir($legacyRoot);
            include $absolutePath;
            $content = ob_get_clean() ?: '';
        } finally {
            if ($previousCwd !== false) {
                chdir($previousCwd);
            }

            $_SERVER['SCRIPT_NAME'] = $previousScriptName;
            $_SERVER['PHP_SELF'] = $previousPhpSelf;
            $_SERVER['REQUEST_URI'] = $previousRequestUri;
            error_reporting($previousErrorLevel);
            ini_set('display_errors', (string) $previousDisplayErrors);
        }

        if (preg_match('/<html[\s>]/i', $content) === 1) {
            return response($this->injectLegacyAccessData($content, $legacyData));
        }

        return response()->view('legacy.page', [
            'content' => $content,
            'legacyPath' => $legacyPath,
            'legacyData' => $legacyData,
        ]);
    }

    /**
     * @param  array<string, mixed>  $legacyData
     */
    private function injectLegacyAccessData(string $content, array $legacyData): string
    {
        $json = json_encode($legacyData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (! is_string($json)) {
            return $content;
        }

        $script = '<script>window.UNIDA_ROLE_ACCESS='.$json.';</script>';

        if (str_contains(strtolower($content), '</body>')) {
            return preg_replace('/<\/body>/i', $script.'</body>', $content, 1) ?? ($content.$script);
        }

        return $content.$script;
    }
}
