<?php

namespace App\Http\Controllers;

use App\Support\LegacyRoleMatrix;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = $this->findUserByEmail($credentials['email']);
        if ($user === null) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        $hash = $user->password_hash ?? $user->password ?? null;
        if (! is_string($hash) || ! Hash::check($credentials['password'], $hash)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        $status = $user->status ?? 'active';
        if ($status !== 'active') {
            return back()->withErrors(['email' => 'Your account is not active.'])->onlyInput('email');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = (int) $user->id;
        $_SESSION['user_name'] = (string) ($user->full_name ?? $user->name ?? 'User');
        $_SESSION['user_email'] = (string) $user->email;
        $_SESSION['user_role'] = LegacyRoleMatrix::normalizeRole((string) ($user->role ?? 'business'));

        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login.php');
    }

    private function findUserByEmail(string $email): ?object
    {
        $columns = Schema::getColumnListing('users');
        $hasRoleColumn = in_array('role', $columns, true);
        $hasRoleId = in_array('role_id', $columns, true) && Schema::hasTable('roles');

        $selectParts = ['u.id', 'u.email'];
        $selectParts[] = in_array('password', $columns, true) ? 'u.password' : "NULL as password";
        $selectParts[] = in_array('password_hash', $columns, true) ? 'u.password_hash' : "NULL as password_hash";
        $selectParts[] = in_array('status', $columns, true) ? 'u.status' : "'active' as status";
        $selectParts[] = in_array('full_name', $columns, true) ? 'u.full_name' : "NULL as full_name";
        $selectParts[] = in_array('name', $columns, true) ? 'u.name' : "NULL as name";

        $query = DB::table('users as u')->where('u.email', $email);
        if ($hasRoleColumn) {
            $query->selectRaw(implode(', ', array_merge($selectParts, ['u.role'])));
        } elseif ($hasRoleId) {
            $query->leftJoin('roles as r', 'r.id', '=', 'u.role_id')
                ->selectRaw(implode(', ', array_merge($selectParts, ["COALESCE(r.role_key, 'business') as role"])));
        } else {
            $query->selectRaw(implode(', ', array_merge($selectParts, [$this->fallbackRoleSql($columns)])));
        }

        return $query->first();
    }

    private function fallbackRoleSql(array $columns): string
    {
        $roleHints = [];

        if (in_array('full_name', $columns, true)) {
            $roleHints[] = "LOWER(COALESCE(u.full_name, ''))";
        }

        if (in_array('name', $columns, true)) {
            $roleHints[] = "LOWER(COALESCE(u.name, ''))";
        }

        $roleHints[] = "LOWER(COALESCE(u.email, ''))";

        $roleSource = implode(", ' ', ", $roleHints);

        return "CASE
            WHEN CONCAT($roleSource) LIKE '%super admin%' OR CONCAT($roleSource) LIKE '%superadmin%' THEN 'SUPER_ADMIN'
            WHEN CONCAT($roleSource) LIKE '%admin%' THEN 'admin'
            WHEN CONCAT($roleSource) LIKE '%investor%' THEN 'investor'
            WHEN CONCAT($roleSource) LIKE '%stakeholder%' THEN 'stakeholder'
            ELSE 'business'
        END as role";
    }

}
