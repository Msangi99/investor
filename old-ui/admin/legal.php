<?php
$pageTitle = 'Legal Documents';
$pageDescription = 'Manage legal documents for UNIDA Gateway.';
$pageName = 'admin-legal';
$activeSidebar = 'settings';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$documents = [];

try {
    if (table_exists('legal_documents')) {
        $documents = db()->query("SELECT * FROM legal_documents ORDER BY document_key ASC, version DESC")->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-scale-balanced"></i> Legal Documents</div>
                <h1>Privacy, Terms, Limitations and Verification Policy.</h1>
                <p>Review current legal document versions stored in the platform database.</p>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <article class="dashboard-panel">
                    <div class="panel-head"><div><h3>Documents</h3><p>Current legal records.</p></div><span class="status-badge status-open"><?= e(count($documents)); ?> documents</span></div>
                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead><tr><th>Key</th><th>Title</th><th>Version</th><th>Status</th><th>Effective Date</th></tr></thead>
                            <tbody>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><?= e($doc['document_key']); ?></td>
                                        <td><?= e($doc['title']); ?></td>
                                        <td><?= e($doc['version']); ?></td>
                                        <td><?= e($doc['status']); ?></td>
                                        <td><?= e($doc['effective_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
