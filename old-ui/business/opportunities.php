<?php
$pageTitle = 'Investment Requests';
$pageDescription = 'Create and manage your investment requests, funding needs, opportunity status and investor visibility.';
$pageName = 'business-opportunities';
$activeSidebar = 'opportunities';

require_once __DIR__ . '/../includes/config.php';
require_role('business');

$pdo = db();
$userId = (int) ($_SESSION['user_id'] ?? 0);

function business_safe_count($pdo, $table, $where = '', $params = [])
{
    try {
        $sql = "SELECT COUNT(*) AS total FROM {$table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

function opportunity_columns(PDO $pdo)
{
    static $columns = null;
    if ($columns !== null) {
        return $columns;
    }

    $columns = [];
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM investment_opportunities");
        foreach ($stmt->fetchAll() as $column) {
            $columns[] = $column['Field'];
        }
    } catch (Throwable $e) {
        $columns = [];
    }

    return $columns;
}

function opportunity_next_id(PDO $pdo)
{
    try {
        $row = $pdo->query("SELECT MAX(id) AS max_id FROM investment_opportunities")->fetch();
        return (int) ($row['max_id'] ?? 0) + 1;
    } catch (Throwable $e) {
        return 1;
    }
}

function map_opportunity_payload(array $columns, array $payload)
{
    $result = [];
    foreach ($payload as $key => $value) {
        if (in_array($key, $columns, true)) {
            $result[$key] = $value;
        }
    }
    return $result;
}

$businessProfile = null;
$formError = '';
$formSuccess = '';

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM business_profiles
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $businessProfile = $stmt->fetch();
} catch (Throwable $e) {
    $businessProfile = null;
}

$businessProfileId = (int) ($businessProfile['id'] ?? 0);
$opportunities = [];
$isOpportunityTableReady = table_exists('investment_opportunities');

if (is_post_request() && isset($_POST['save_opportunity'])) {
    $title = trim((string) ($_POST['title'] ?? ''));
    $sector = trim((string) ($_POST['sector'] ?? ''));
    $region = trim((string) ($_POST['region'] ?? ''));
    $summary = trim((string) ($_POST['summary'] ?? ''));
    $fundingAmount = (float) ($_POST['funding_amount'] ?? 0);
    $currency = strtoupper(substr(trim((string) ($_POST['currency'] ?? 'TZS')), 0, 10));
    $fundingType = trim((string) ($_POST['funding_type'] ?? 'equity'));
    $stage = trim((string) ($_POST['stage'] ?? 'mvp'));
    $status = trim((string) ($_POST['status'] ?? 'draft'));

    $allowedFundingTypes = ['equity', 'debt', 'grant', 'partnership', 'asset_finance', 'other'];
    $allowedStages = ['idea', 'prototype', 'mvp', 'early_revenue', 'growth', 'scale'];
    $allowedStatuses = ['draft', 'published'];

    if (!$isOpportunityTableReady) {
        $formError = 'Investment opportunities table is not ready yet.';
    } elseif ($businessProfileId <= 0) {
        $formError = 'Please complete your business profile before creating an opportunity.';
    } elseif ($title === '' || $summary === '') {
        $formError = 'Title and summary are required.';
    } elseif (!in_array($fundingType, $allowedFundingTypes, true)) {
        $formError = 'Invalid funding type.';
    } elseif (!in_array($stage, $allowedStages, true)) {
        $formError = 'Invalid business stage.';
    } elseif (!in_array($status, $allowedStatuses, true)) {
        $formError = 'Invalid status.';
    } else {
        try {
            $columns = opportunity_columns($pdo);
            $record = map_opportunity_payload($columns, [
                'id' => opportunity_next_id($pdo),
                'business_profile_id' => $businessProfileId,
                'created_by' => $userId,
                'title' => $title,
                'sector' => $sector !== '' ? $sector : null,
                'region' => $region !== '' ? $region : null,
                'summary' => $summary,
                'funding_amount' => $fundingAmount > 0 ? $fundingAmount : null,
                'currency' => $currency !== '' ? $currency : 'TZS',
                'funding_type' => $fundingType,
                'stage' => $stage,
                'status' => $status,
                'verification_status' => 'pending',
                'readiness_score' => (int) ($businessProfile['readiness_score'] ?? 0),
                'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if (!empty($record)) {
                $keys = array_keys($record);
                $placeholders = array_fill(0, count($keys), '?');
                $sql = 'INSERT INTO investment_opportunities ('.implode(',', $keys).') VALUES ('.implode(',', $placeholders).')';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_values($record));
                $formSuccess = 'Investment request saved successfully.';
                $_POST = [];
            } else {
                $formError = 'Could not prepare opportunity payload for insertion.';
            }
        } catch (Throwable $e) {
            $formError = 'Failed to save opportunity. Please try again.';
        }
    }
}

if ($isOpportunityTableReady) {
    try {
        $sql = "
            SELECT id, title, sector, region, funding_amount, currency, funding_type, stage, status, verification_status, created_at
            FROM investment_opportunities
            WHERE created_by = ?
        ";
        $params = [$userId];
        if ($businessProfileId > 0) {
            $sql .= " OR business_profile_id = ?";
            $params[] = $businessProfileId;
        }
        $sql .= " ORDER BY id DESC LIMIT 100";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $opportunities = $stmt->fetchAll();
    } catch (Throwable $e) {
        $opportunities = [];
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-briefcase"></i>
                    Business Workspace
                </div>

                <h1>Investment Requests</h1>

                <p>Create and manage your investment requests, funding needs, opportunity status and investor visibility.</p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar">
                    <?= e(strtoupper(substr($_SESSION['user_name'] ?? 'BU', 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($_SESSION['user_name'] ?? 'Business User'); ?></h3>
                    <p><?= e($businessProfile['business_name'] ?? 'Business account'); ?></p>

                    <span class="status-badge status-progress">
                        <i class="fa-solid fa-briefcase"></i>
                        Business workspace
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <?php
                $totalOpportunities = $businessProfileId ? business_safe_count($pdo, 'investment_opportunities', 'business_profile_id = ?', [$businessProfileId]) : 0;
                $publishedOpportunities = $businessProfileId ? business_safe_count($pdo, 'investment_opportunities', "business_profile_id = ? AND status = 'published'", [$businessProfileId]) : 0;
                $verifiedOpportunities = $businessProfileId ? business_safe_count($pdo, 'investment_opportunities', "business_profile_id = ? AND verification_status = 'verified'", [$businessProfileId]) : 0;
                ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-briefcase"></i></span><div><strong><?= e($totalOpportunities); ?></strong><small>Total requests</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-eye"></i></span><div><strong><?= e($publishedOpportunities); ?></strong><small>Published</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-shield-halved"></i></span><div><strong><?= e($verifiedOpportunities); ?></strong><small>Verified</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-chart-line"></i></span><div><strong><?= e($businessProfile['readiness_score'] ?? 0); ?>%</strong><small>Readiness</small></div></article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Create Investment Request</h3>
                            <p>Publish a real opportunity that investors can discover after verification.</p>
                        </div>
                        <span class="status-badge status-open">Live Form</span>
                    </div>

                    <?php if ($formSuccess !== ''): ?>
                        <div class="form-alert form-alert-success" style="margin-bottom:14px;">
                            <p><?= e($formSuccess); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($formError !== ''): ?>
                        <div class="form-alert form-alert-error" style="margin-bottom:14px;">
                            <p><?= e($formError); ?></p>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="form-grid two" style="margin-bottom:20px;">
                        <input type="hidden" name="save_opportunity" value="1">
                        <div class="form-group">
                            <small>Title *</small>
                            <input class="form-control" type="text" name="title" value="<?= e($_POST['title'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <small>Sector</small>
                            <input class="form-control" type="text" name="sector" value="<?= e($_POST['sector'] ?? ($businessProfile['sector'] ?? '')); ?>">
                        </div>
                        <div class="form-group">
                            <small>Region</small>
                            <input class="form-control" type="text" name="region" value="<?= e($_POST['region'] ?? ($businessProfile['region'] ?? '')); ?>">
                        </div>
                        <div class="form-group">
                            <small>Funding Amount</small>
                            <input class="form-control" type="number" min="0" step="0.01" name="funding_amount" value="<?= e($_POST['funding_amount'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <small>Currency</small>
                            <input class="form-control" type="text" maxlength="10" name="currency" value="<?= e($_POST['currency'] ?? ($businessProfile['funding_currency'] ?? 'TZS')); ?>">
                        </div>
                        <div class="form-group">
                            <small>Funding Type *</small>
                            <?php $selectedFundingType = $_POST['funding_type'] ?? 'equity'; ?>
                            <select class="form-control" name="funding_type" required>
                                <option value="equity" <?= $selectedFundingType === 'equity' ? 'selected' : ''; ?>>Equity</option>
                                <option value="debt" <?= $selectedFundingType === 'debt' ? 'selected' : ''; ?>>Debt</option>
                                <option value="grant" <?= $selectedFundingType === 'grant' ? 'selected' : ''; ?>>Grant</option>
                                <option value="partnership" <?= $selectedFundingType === 'partnership' ? 'selected' : ''; ?>>Partnership</option>
                                <option value="asset_finance" <?= $selectedFundingType === 'asset_finance' ? 'selected' : ''; ?>>Asset Finance</option>
                                <option value="other" <?= $selectedFundingType === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <small>Business Stage *</small>
                            <?php $selectedStage = $_POST['stage'] ?? ($businessProfile['business_stage'] ?? 'mvp'); ?>
                            <select class="form-control" name="stage" required>
                                <option value="idea" <?= $selectedStage === 'idea' ? 'selected' : ''; ?>>Idea</option>
                                <option value="prototype" <?= $selectedStage === 'prototype' ? 'selected' : ''; ?>>Prototype</option>
                                <option value="mvp" <?= $selectedStage === 'mvp' ? 'selected' : ''; ?>>MVP</option>
                                <option value="early_revenue" <?= $selectedStage === 'early_revenue' ? 'selected' : ''; ?>>Early Revenue</option>
                                <option value="growth" <?= $selectedStage === 'growth' ? 'selected' : ''; ?>>Growth</option>
                                <option value="scale" <?= $selectedStage === 'scale' ? 'selected' : ''; ?>>Scale</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <small>Status *</small>
                            <?php $selectedStatus = $_POST['status'] ?? 'draft'; ?>
                            <select class="form-control" name="status" required>
                                <option value="draft" <?= $selectedStatus === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?= $selectedStatus === 'published' ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <small>Summary *</small>
                            <textarea class="form-control" name="summary" rows="4" required><?= e($_POST['summary'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Save Opportunity
                            </button>
                        </div>
                    </form>
                </article>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>My Opportunities</h3>
                            <p>Track your requests, publishing status and admin verification.</p>
                        </div>
                        <span class="status-badge status-progress"><?= e(count($opportunities)); ?> records</span>
                    </div>

                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Sector / Region</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Verification</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($opportunities)): ?>
                                    <?php foreach ($opportunities as $opportunity): ?>
                                        <tr>
                                            <td><?= e($opportunity['title'] ?? 'Untitled'); ?></td>
                                            <td><?= e(($opportunity['sector'] ?? 'General') . ' / ' . ($opportunity['region'] ?? 'N/A')); ?></td>
                                            <td><?= e(($opportunity['currency'] ?? 'TZS') . ' ' . number_format((float) ($opportunity['funding_amount'] ?? 0), 2)); ?></td>
                                            <td><span class="status-badge status-open"><?= e(ucfirst((string) ($opportunity['status'] ?? 'draft'))); ?></span></td>
                                            <td><span class="status-badge status-progress"><?= e(ucfirst(str_replace('_', ' ', (string) ($opportunity['verification_status'] ?? 'pending')))); ?></span></td>
                                            <td><?= e((string) ($opportunity['created_at'] ?? '-')); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">No opportunities created yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
