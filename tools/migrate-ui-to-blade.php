<?php
/**
 * UNIDA Gateway - Old UI to Blade Migration Script
 * Run: php tools/migrate-ui-to-blade.php
 */

$oldUiRoot = __DIR__ . '/../old-ui';
$viewsRoot = __DIR__ . '/../resources/views';

// Create view directories
$dirs = [
    $viewsRoot . '/pages/business',
    $viewsRoot . '/pages/investor',
    $viewsRoot . '/pages/stakeholder',
    $viewsRoot . '/pages/admin',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Find all PHP files
$files = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($oldUiRoot, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $relativePath = str_replace($oldUiRoot . DIRECTORY_SEPARATOR, '', $file->getPathname());
       
        // Skip includes and tools folders
        if (str_starts_with($relativePath, 'includes') || str_starts_with($relativePath, 'tools') || str_starts_with($relativePath, 'api') || str_starts_with($relativePath, 'database')) {
            continue;
        }
       
        $files[] = [
            'source' => $file->getPathname(),
            'relative' => $relativePath,
        ];
    }
}

echo "Found " . count($files) . " PHP files to migrate.\n\n";

foreach ($files as $file) {
    $source = $file['source'];
    $relative = $file['relative'];
   
    $content = file_get_contents($source);
   
    // Determine layout
    $layout = 'guest';
    if (str_contains($relative, 'business') || str_contains($relative, 'investor') || str_contains($relative, 'stakeholder') || str_contains($relative, 'admin')) {
        $layout = 'dashboard';
    }
   
    // Extract main content (remove header/footer includes)
    $content = preg_replace('/<\?php[\s\S]*?require.*?header\.php.*?\?>/i', '', $content);
    $content = preg_replace('/<\?php[\s\S]*?include.*?header\.php.*?\?>/i', '', $content);
    $content = preg_replace('/<\?php[\s\S]*?include.*?footer\.php.*?\?>/i', '', $content);
    $content = preg_replace('/<\?php[\s\S]*?include.*?sidebar\.php.*?\?>/i', '', $content);
   
    // Extract page meta if present
    preg_match('/\$pageTitle\s*=\s*[\'"]([^\'"]+)[\'"];/', $content, $titleMatch);
    preg_match('/\$pageDescription\s*=\s*[\'"]([^\'"]+)[\'"];/', $content, $descMatch);
    preg_match('/\$pageName\s*=\s*[\'"]([^\'"]+)[\'"];/', $content, $nameMatch);
    preg_match('/\$activeSidebar\s*=\s*[\'"]([^\'"]+)[\'"];/', $content, $sidebarMatch);
   
    $pageTitle = $titleMatch[1] ?? '';
    $pageDescription = $descMatch[1] ?? '';
    $pageName = $nameMatch[1] ?? '';
    $activeSidebar = $sidebarMatch[1] ?? '';
   
    // Remove PHP config/auth lines
    $content = preg_replace('/<\?php[\s\S]*?require.*?config\.php.*?\?>/i', '', $content);
    $content = preg_replace('/require_role\([^\)]+\);/', '', $content);
    $content = preg_replace('/require_login\([^\)]+\);/', '', $content);
   
    // Convert BASE_URL references
    $content = preg_replace('/\<\?=\s*e\(BASE_URL\);?\s*\?\>/', '', $content);
    $content = preg_replace('/\<\?=\s*BASE_URL;?\s*\?\>/', '', $content);
   
    // Convert e() helper to {{ }}
    $content = preg_replace('/\<\?=\s*e\(([^\)]+)\)\s*\?\>/', '{{ $1 }}', $content);
   
    // Clean up remaining PHP tags at start
    $content = preg_replace('/^<\?php[\s\S]*?\?>\s*/i', '', $content);
   
    // Build Blade view
    $blade = "@extends('layouts.{$layout}')\n\n";
   
    if ($pageTitle || $pageDescription || $pageName || $activeSidebar) {
        $blade .= "@php\n";
        if ($pageTitle) $blade .= "    \$pageTitle = '{$pageTitle}';\n";
        if ($pageDescription) $blade .= "    \$pageDescription = '" . addslashes($pageDescription) . "';\n";
        if ($pageName) $blade .= "    \$pageName = '{$pageName}';\n";
        if ($activeSidebar) $blade .= "    \$activeSidebar = '{$activeSidebar}';\n";
        $blade .= "@endphp\n\n";
    }
   
    $blade .= "@section('content')\n";
    $blade .= trim($content) . "\n";
    $blade .= "@endsection\n";
   
    // Determine output path
    $outputPath = $viewsRoot . '/pages/' . str_replace('.php', '.blade.php', $relative);
    $outputDir = dirname($outputPath);
   
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }
   
    file_put_contents($outputPath, $blade);
    echo "✓ Migrated: {$relative}\n";
}

echo "\n✅ Migration complete! All " . count($files) . " files converted to Blade views.\n";
