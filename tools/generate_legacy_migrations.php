<?php

$root = dirname(__DIR__, 2);
$sqlPath = $root.DIRECTORY_SEPARATOR.'eunicetz_unida (1).sql';
$migrationDir = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';

$sql = file_get_contents($sqlPath);
if ($sql === false) {
    throw new RuntimeException('Unable to read SQL dump.');
}

preg_match_all('/CREATE TABLE `([^`]+)` \\((.*?)\\) ENGINE=.*?;/s', $sql, $createMatches, PREG_SET_ORDER);
preg_match_all('/ALTER TABLE `[^`]+`.*?;/s', $sql, $alterMatches, PREG_SET_ORDER);

$timestampPrefix = '2026_04_28_1800';
$index = 1;

foreach ($createMatches as $match) {
    $table = $match[1];
    $columns = $match[2];

    $filename = sprintf(
        '%s%02d_create_legacy_%s_table.php',
        $timestampPrefix,
        $index++,
        $table
    );

    $createSql = "CREATE TABLE IF NOT EXISTS `{$table}` ({$columns}) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $content = <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
{$createSql}
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `{$table}`');
    }
};
PHP;

    file_put_contents($migrationDir.DIRECTORY_SEPARATOR.$filename, $content);
}

if (! empty($alterMatches)) {
    $alterSql = implode("\n", array_column($alterMatches, 0));
    $alterSql = preg_replace('/AUTO_INCREMENT=\\d+/', '', $alterSql) ?? $alterSql;

    $filename = sprintf('%s%02d_apply_legacy_schema_alterations.php', $timestampPrefix, $index);
    $content = <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
{$alterSql}
SQL);
    }

    public function down(): void
    {
        // Non-reversible schema changes.
    }
};
PHP;

    file_put_contents($migrationDir.DIRECTORY_SEPARATOR.$filename, $content);
}

echo 'Generated '.count($createMatches).' table migrations and '.(! empty($alterMatches) ? '1' : '0').' alteration migration.'.PHP_EOL;
