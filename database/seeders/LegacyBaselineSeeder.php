<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyBaselineSeeder extends Seeder
{
    /**
     * Seed baseline records needed for role/access and chatbot defaults.
     */
    public function run(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $sqlPath = $this->resolveSqlDumpPath();

        if ($sqlPath === null) {
            $this->command?->warn('LegacyBaselineSeeder skipped: SQL dump file not found.');
            return;
        }

        $sql = file_get_contents($sqlPath);

        if ($sql === false) {
            $this->command?->warn(sprintf('LegacyBaselineSeeder skipped: unable to read SQL dump at %s', $sqlPath));
            return;
        }

        $tables = [
            'permissions',
            'roles',
            'role_permissions',
            'system_settings',
            'ai_assistants',
            'ai_settings',
            'dashboard_registry',
            'legal_documents',
            'faq_items',
            'platform_limitations',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            foreach ($this->extractInsertsForTable($sql, $table) as $insertSql) {
                DB::unprepared($insertSql);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @return array<int, string>
     */
    private function extractInsertsForTable(string $sql, string $table): array
    {
        $pattern = sprintf('/INSERT INTO `%s` .*?;/s', preg_quote($table, '/'));
        preg_match_all($pattern, $sql, $matches);

        if (empty($matches[0])) {
            return [];
        }

        return array_map(
            static fn (string $statement): string => preg_replace('/^INSERT INTO/i', 'INSERT IGNORE INTO', $statement, 1) ?? $statement,
            $matches[0]
        );
    }

    private function resolveSqlDumpPath(): ?string
    {
        $candidates = [
            base_path('..'.DIRECTORY_SEPARATOR.'eunicetz_unida (1).sql'),
            base_path('database'.DIRECTORY_SEPARATOR.'seeders'.DIRECTORY_SEPARATOR.'legacy-baseline.sql'),
            base_path('old-ui'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'unida_full_update.sql'),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
