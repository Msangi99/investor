<?php

namespace App\Support;

class LegacyRoleMatrix
{
    public static function normalizeRole(?string $role): string
    {
        $input = strtolower(trim((string) $role));
        $aliases = config('legacy_roles.aliases', []);

        if (isset($aliases[$input])) {
            return (string) $aliases[$input];
        }

        return (string) config('legacy_roles.default_role', 'business');
    }

    /**
     * @return array<string, mixed>
     */
    public static function roleConfig(string $role): array
    {
        $normalized = self::normalizeRole($role);
        $roles = config('legacy_roles.roles', []);

        return $roles[$normalized] ?? [];
    }

    /**
     * @return array<string, array{label:string,description:string,uri:string}>
     */
    public static function modulesForRole(string $role): array
    {
        $roleConfig = self::roleConfig($role);
        $modules = $roleConfig['modules'] ?? [];

        return is_array($modules) ? $modules : [];
    }

    public static function dashboardUriForRole(string $role): string
    {
        $roleConfig = self::roleConfig($role);

        return (string) ($roleConfig['dashboard_uri'] ?? '/login.php');
    }

    public static function moduleKeyFromUri(string $uri): ?string
    {
        $map = self::uriToModuleMap();

        return $map[$uri] ?? null;
    }

    public static function canAccessModule(string $role, ?string $moduleKey): bool
    {
        if ($moduleKey === null || $moduleKey === '') {
            return true;
        }

        return array_key_exists($moduleKey, self::modulesForRole($role));
    }

    /**
     * @return array<int, string>
     */
    public static function allRoles(): array
    {
        return array_keys(config('legacy_roles.roles', []));
    }

    /**
     * Build a URI → module-key map by scanning all roles.
     * Each URI is recorded only once (first occurrence wins), so shared
     * URIs that appear in multiple roles under the same module key are
     * recorded consistently. Extra URI overrides are applied last.
     *
     * @return array<string, string>
     */
    public static function uriToModuleMap(): array
    {
        $map = [];
        $roles = config('legacy_roles.roles', []);

        foreach ($roles as $roleConfig) {
            $modules = $roleConfig['modules'] ?? [];
            if (! is_array($modules)) {
                continue;
            }

            foreach ($modules as $moduleKey => $module) {
                $uri = is_array($module) ? ($module['uri'] ?? null) : null;
                if (is_string($uri) && $uri !== '' && ! isset($map[$uri])) {
                    $map[$uri] = (string) $moduleKey;
                }
            }
        }

        $extras = config('legacy_roles.extra_uri_module_map', []);
        if (is_array($extras)) {
            foreach ($extras as $uri => $moduleKey) {
                if (is_string($uri) && is_string($moduleKey)) {
                    $map[$uri] = $moduleKey;
                }
            }
        }

        return $map;
    }
}
