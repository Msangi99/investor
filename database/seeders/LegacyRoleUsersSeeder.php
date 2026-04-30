<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyRoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (! Schema::hasTable('users')) {
            return;
        }

        $rolesByKey = DB::table('roles')->pluck('id', 'role_key')->all();
        $userColumns = Schema::getColumnListing('users');

        $users = [
            [
                'full_name' => 'Business Demo User',
                'organization' => 'UNIDA Business Demo',
                'email' => 'business.demo@unida.local',
                'phone' => '0700000001',
                'role' => 'business',
                'role_id' => $rolesByKey['business'] ?? null,
            ],
            [
                'full_name' => 'Investor Demo User',
                'organization' => 'UNIDA Investor Demo',
                'email' => 'investor.demo@unida.local',
                'phone' => '0700000002',
                'role' => 'investor',
                'role_id' => $rolesByKey['investor'] ?? null,
            ],
            [
                'full_name' => 'Stakeholder Demo User',
                'organization' => 'UNIDA Stakeholder Demo',
                'email' => 'stakeholder.demo@unida.local',
                'phone' => '0700000003',
                'role' => 'stakeholder',
                'role_id' => $rolesByKey['stakeholder'] ?? null,
            ],
            [
                'full_name' => 'Admin Demo User',
                'organization' => 'UNIDA Admin Demo',
                'email' => 'admin.demo@unida.local',
                'phone' => '0700000004',
                'role' => 'admin',
                'role_id' => $rolesByKey['admin'] ?? ($rolesByKey['SUPER_ADMIN'] ?? null),
            ],
            [
                'full_name' => 'Super Admin Demo User',
                'organization' => 'UNIDA Platform',
                'email' => 'superadmin.demo@unida.local',
                'phone' => '0700000005',
                'role' => 'SUPER_ADMIN',
                'role_id' => $rolesByKey['SUPER_ADMIN'] ?? ($rolesByKey['admin'] ?? null),
            ],
        ];

        foreach ($users as $user) {
            $payload = [
                'email' => $user['email'],
                'updated_at' => now(),
            ];

            if (in_array('role_id', $userColumns, true)) {
                $payload['role_id'] = $user['role_id'];
            }
            if (in_array('full_name', $userColumns, true)) {
                $payload['full_name'] = $user['full_name'];
            }
            if (in_array('name', $userColumns, true)) {
                $payload['name'] = $user['full_name'];
            }
            if (in_array('organization', $userColumns, true)) {
                $payload['organization'] = $user['organization'];
            }
            if (in_array('phone', $userColumns, true)) {
                $payload['phone'] = $user['phone'];
            }
            if (in_array('role', $userColumns, true)) {
                $payload['role'] = $user['role'];
            }
            if (in_array('password_hash', $userColumns, true)) {
                $payload['password_hash'] = password_hash('Pass@123456', PASSWORD_DEFAULT);
            }
            if (in_array('password', $userColumns, true)) {
                $payload['password'] = password_hash('Pass@123456', PASSWORD_DEFAULT);
            }
            if (in_array('status', $userColumns, true)) {
                $payload['status'] = 'active';
            }
            if (in_array('email_verified_at', $userColumns, true)) {
                $payload['email_verified_at'] = now();
            }
            if (! in_array('updated_at', $userColumns, true)) {
                unset($payload['updated_at']);
            }

            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $payload
            );
        }
    }
}
