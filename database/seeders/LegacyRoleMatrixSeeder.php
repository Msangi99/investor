<?php

namespace Database\Seeders;

use App\Support\LegacyRoleMatrix;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyRoleMatrixSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (! Schema::hasTable('roles') || ! Schema::hasTable('permissions') || ! Schema::hasTable('role_permissions')) {
            return;
        }

        $roles = config('legacy_roles.roles', []);
        if (! is_array($roles) || $roles === []) {
            return;
        }

        $rolesRequireManualId = $this->requiresManualId('roles');
        $permissionsRequireManualId = $this->requiresManualId('permissions');
        $rolePermissionsRequireManualId = $this->requiresManualId('role_permissions');
        $nextRoleId = ((int) (DB::table('roles')->max('id') ?? 0)) + 1;
        $nextPermissionId = ((int) (DB::table('permissions')->max('id') ?? 0)) + 1;
        $nextRolePermissionId = ((int) (DB::table('role_permissions')->max('id') ?? 0)) + 1;

        foreach ($roles as $roleKey => $roleConfig) {
            $existingRoleId = DB::table('roles')->where('role_key', $roleKey)->value('id');
            $rolePayload = [
                'role_name' => (string) (($roleConfig['label'] ?? $roleKey).' Role'),
                'role_type' => (($roleConfig['area'] ?? 'public') === 'admin') ? 'admin' : 'public_user',
                'description' => (string) ($roleConfig['label'] ?? $roleKey),
                'is_system' => 1,
                'is_active' => 1,
                'created_at' => now(),
            ];

            if ($existingRoleId !== null) {
                DB::table('roles')->where('role_key', $roleKey)->update($rolePayload);
                continue;
            }

            $roleInsertPayload = ['role_key' => $roleKey] + $rolePayload;
            if ($rolesRequireManualId) {
                $roleInsertPayload['id'] = $nextRoleId++;
            }

            DB::table('roles')->insert($roleInsertPayload);
        }

        $roleIdsByKey = DB::table('roles')->pluck('id', 'role_key')->all();
        $permissionIdByKey = DB::table('permissions')->pluck('id', 'permission_key')->all();

        foreach (LegacyRoleMatrix::allRoles() as $roleKey) {
            $roleId = $roleIdsByKey[$roleKey] ?? null;
            if (! is_numeric($roleId)) {
                continue;
            }

            $modules = LegacyRoleMatrix::modulesForRole($roleKey);
            foreach ($modules as $moduleKey => $module) {
                $uri = (string) ($module['uri'] ?? '');
                if ($uri === '') {
                    continue;
                }

                $area = trim(explode('/', trim($uri, '/'))[0] ?? 'app');
                $permissionKey = $area.'.'.$moduleKey;

                if (! isset($permissionIdByKey[$permissionKey])) {
                    $existingPermissionId = DB::table('permissions')->where('permission_key', $permissionKey)->value('id');
                    $permissionPayload = [
                        'permission_name' => (string) ($module['label'] ?? $moduleKey),
                        'module' => $area,
                        'description' => (string) ($module['description'] ?? $moduleKey),
                        'created_at' => now(),
                    ];

                    if ($existingPermissionId !== null) {
                        DB::table('permissions')->where('permission_key', $permissionKey)->update($permissionPayload);
                    } else {
                        $permissionInsertPayload = ['permission_key' => $permissionKey] + $permissionPayload;
                        if ($permissionsRequireManualId) {
                            $permissionInsertPayload['id'] = $nextPermissionId++;
                        }

                        DB::table('permissions')->insert($permissionInsertPayload);
                    }

                    $permissionIdByKey = DB::table('permissions')->pluck('id', 'permission_key')->all();
                }

                $permissionId = $permissionIdByKey[$permissionKey] ?? null;
                if (! is_numeric($permissionId)) {
                    continue;
                }

                $rolePermissionExists = DB::table('role_permissions')
                    ->where('role_id', (int) $roleId)
                    ->where('permission_id', (int) $permissionId)
                    ->exists();

                if (! $rolePermissionExists) {
                    $rolePermissionPayload = [
                        'role_id' => (int) $roleId,
                        'permission_id' => (int) $permissionId,
                        'created_at' => now(),
                    ];

                    if ($rolePermissionsRequireManualId) {
                        $rolePermissionPayload['id'] = $nextRolePermissionId++;
                    }

                    DB::table('role_permissions')->insert($rolePermissionPayload);
                }
            }
        }
    }

    private function requiresManualId(string $table): bool
    {
        $rows = DB::select(sprintf("SHOW COLUMNS FROM `%s` LIKE 'id'", str_replace('`', '``', $table)));
        if ($rows === []) {
            return false;
        }

        $column = (array) $rows[0];
        $extra = strtolower((string) ($column['Extra'] ?? $column['extra'] ?? ''));

        return ! str_contains($extra, 'auto_increment');
    }
}
