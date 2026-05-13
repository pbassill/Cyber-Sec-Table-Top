<?php
/**
 * Cyber Quest — Exercise History
 * Browse past exercises with stored data, action items, and evaluations
 */
$pageTitle = 'Cyber Quest — Exercise History';
require_once 'includes/header.php';
require_once 'includes/database.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$status = isset($_GET['status']) && in_array($_GET['status'], ['active', 'completed', 'setup']) ? $_GET['status'] : '';
$viewId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Detail view
if ($viewId > 0) {
    $exercise = getExerciseById($viewId);
    if (!$exercise) {
        echo '<div class="container py-5 text-center"><h3>Exercise not found</h3><a href="history.php" class="btn btn-gold mt-3">Back to History</a></div>';
        require_once 'includes/footer.php';
        exit;
    }
    $actionItems = getActionItems($viewId);
    $evaluations = getEvaluations($viewId);
    $timeline = getExerciseTimeline($viewId);
    $code = $exercise['session_code'];
?>
<div class="container py-4">
    <a href="history.php" class="btn btn-outline-gold mb-4"><i class="bi bi-arrow-left"></i> Back to History</a>

    <div class="dnd-card mb-4">
        <div class="dnd-card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><?php echo htmlspecialchars($exercise['event_name'] ?: 'Unnamed Exercise', ENT_QUOTES, 'UTF-8'); ?></h3>
                <span class="badge <?php echo $exercise['status'] === 'completed' ? 'bg-success' : ($exercise['status'] === 'active' ? 'bg-warning' : 'bg-secondary'); ?>">
                    <?php echo ucfirst(htmlspecialchars($exercise['status'], ENT_QUOTES, 'UTF-8')); ?>
                </span>
            </div>
        </div>
        <div class="dnd-card-body">
            <div class="row g-3">
                <div class="col-md-3"><strong>Session Code:</strong> <?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="col-md-3"><strong>Created:</strong> <?php echo htmlspecialchars($exercise['created_at'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="col-md-3"><strong>Completed:</strong> <?php echo $exercise['completed_at'] ? htmlspecialchars($exercise['completed_at'], ENT_QUOTES, 'UTF-8') : '—'; ?></div>
                <div class="col-md-3"><strong>Campaign:</strong> <?php echo htmlspecialchars($exercise['campaign_category'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></div>
                <?php
                    $detailVerticalId = $exercise['industry_vertical'] ?? '';
                    $detailVerticals = loadVerticals();
                    $detailVerticalLabel = '—';
                    if ($detailVerticalId !== '' && isset($detailVerticals[$detailVerticalId])) {
                        $detailVerticalLabel = $detailVerticals[$detailVerticalId]['icon'] . ' ' . $detailVerticals[$detailVerticalId]['title'];
                    }
                ?>
                <div class="col-md-3"><strong>Industry:</strong> <?php echo htmlspecialchars($detailVerticalLabel, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>

            <?php if (!empty($exercise['scenarios'])): ?>
            <div class="mt-3">
                <strong>Scenarios:</strong>
                <?php foreach ($exercise['scenarios'] as $sId):
                    $s = loadScenario($sId); ?>
                    <span class="badge bg-secondary me-1"><?php echo ($s['icon'] ?? '') . ' ' . htmlspecialchars($s['title'] ?? $sId, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($exercise['participants'])): ?>
            <div class="mt-3">
                <strong>Participants (<?php echo count($exercise['participants']); ?>):</strong>
                <?php foreach ($exercise['participants'] as $p): ?>
                    <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Export buttons -->
            <div class="mt-4">
                <strong>Export:</strong>
                <a href="api/export.php?type=full&code=<?php echo urlencode($code); ?>" class="btn btn-sm btn-gold ms-2"><i class="bi bi-file-earmark-spreadsheet"></i> Full Report (CSV)</a>
                <a href="api/export.php?type=gaps&code=<?php echo urlencode($code); ?>" class="btn btn-sm btn-outline-gold"><i class="bi bi-download"></i> Gaps</a>
                <a href="api/export.php?type=notes&code=<?php echo urlencode($code); ?>" class="btn btn-sm btn-outline-gold"><i class="bi bi-download"></i> Notes</a>
                <a href="api/export.php?type=evaluations&code=<?php echo urlencode($code); ?>" class="btn btn-sm btn-outline-gold"><i class="bi bi-download"></i> Evaluations</a>
                <a href="api/export.php?type=timeline&code=<?php echo urlencode($code); ?>" class="btn btn-sm btn-outline-gold"><i class="bi bi-download"></i> Timeline</a>
            </div>
        </div>
    </div>

    <!-- Action Items -->
    <?php if (!empty($actionItems)): ?>
    <div class="dnd-card mb-4">
        <div class="dnd-card-header"><h4><i class="bi bi-exclamation-diamond"></i> Action Items (<?php echo count($actionItems); ?>)</h4></div>
        <div class="dnd-card-body">
            <table class="table table-dark table-hover dnd-table">
                <thead>
                    <tr><th>#</th><th>Gap</th><th>Remediation</th><th>Owner</th><th>Target Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($actionItems as $item): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($item['gap_description'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($item['remediation_action'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($item['owner'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($item['target_date'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="badge <?php echo $item['status'] === 'closed' ? 'bg-success' : ($item['status'] === 'in_progress' ? 'bg-warning' : 'bg-danger'); ?>"><?php echo ucfirst(htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8')); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Evaluations -->
    <?php if (!empty($evaluations)): ?>
    <div class="dnd-card mb-4">
        <div class="dnd-card-header"><h4><i class="bi bi-star"></i> Evaluations</h4></div>
        <div class="dnd-card-body">
            <table class="table table-dark dnd-table">
                <thead><tr><th>Question</th><th>Rating</th></tr></thead>
                <tbody>
                    <?php foreach ($evaluations as $eval): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($eval['question'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo str_repeat('★', $eval['rating']) . str_repeat('☆', 5 - $eval['rating']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Session Notes -->
    <?php if (!empty($exercise['notes'])): ?>
    <div class="dnd-card mb-4">
        <div class="dnd-card-header"><h4><i class="bi bi-journal-text"></i> Session Notes</h4></div>
        <div class="dnd-card-body">
            <table class="table table-dark table-hover dnd-table">
                <thead><tr><th>Time</th><th>Observation</th></tr></thead>
                <tbody>
                    <?php foreach ($exercise['notes'] as $note): ?>
                    <tr>
                        <td class="text-nowrap"><?php echo htmlspecialchars($note['time'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($note['text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timeline -->
    <?php if (!empty($timeline)): ?>
    <div class="dnd-card mb-4">
        <div class="dnd-card-header"><h4><i class="bi bi-clock-history"></i> Exercise Timeline</h4></div>
        <div class="dnd-card-body">
            <div class="timeline-list">
                <?php foreach ($timeline as $event): ?>
                <div class="d-flex align-items-start mb-2">
                    <small class="text-muted me-3 text-nowrap"><?php echo htmlspecialchars($event['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <span class="badge bg-secondary me-2"><?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($event['event_type'], ENT_QUOTES, 'UTF-8'))); ?></span>
                    <span>Scenario <?php echo $event['scenario_index'] + 1; ?>, Inject <?php echo $event['inject_index'] + 1; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
    require_once 'includes/footer.php';
    exit;
}

// List view
$data = listExercises($page, 20, $status);
$stats = getExerciseStats();
?>

<div class="container py-4">
    <div class="text-center mb-5">
        <h1 class="dnd-title">📜 Exercise History 📜</h1>
        <h2 class="dnd-subtitle">Chronicles of Past Campaigns</h2>
        <div class="hero-divider">═══════ ⚜️ ═══════</div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4 justify-content-center">
        <div class="col-md-2">
            <div class="dnd-card text-center">
                <div class="dnd-card-body py-3">
                    <div class="fs-2 fw-bold" style="color: var(--gold)"><?php echo $stats['total_exercises']; ?></div>
                    <small class="text-muted">Total Exercises</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dnd-card text-center">
                <div class="dnd-card-body py-3">
                    <div class="fs-2 fw-bold text-success"><?php echo $stats['completed_exercises']; ?></div>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dnd-card text-center">
                <div class="dnd-card-body py-3">
                    <div class="fs-2 fw-bold text-danger"><?php echo $stats['open_actions']; ?></div>
                    <small class="text-muted">Open Actions</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dnd-card text-center">
                <div class="dnd-card-body py-3">
                    <div class="fs-2 fw-bold text-success"><?php echo $stats['closed_actions']; ?></div>
                    <small class="text-muted">Closed Actions</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dnd-card text-center">
                <div class="dnd-card-body py-3">
                    <div class="fs-2 fw-bold" style="color: var(--gold)"><?php echo $stats['avg_rating'] ?? '—'; ?></div>
                    <small class="text-muted">Avg Rating</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="text-center mb-4">
        <a href="history.php" class="btn btn-sm <?php echo $status === '' ? 'btn-gold' : 'btn-outline-gold'; ?> me-1">All</a>
        <a href="history.php?status=completed" class="btn btn-sm <?php echo $status === 'completed' ? 'btn-gold' : 'btn-outline-gold'; ?> me-1">Completed</a>
        <a href="history.php?status=active" class="btn btn-sm <?php echo $status === 'active' ? 'btn-gold' : 'btn-outline-gold'; ?> me-1">Active</a>
    </div>

    <!-- Exercise List -->
    <?php if (empty($data['exercises'])): ?>
    <div class="text-center text-muted py-5">
        <p class="fs-5">No exercises found yet.</p>
        <p>Run your first exercise from the <a href="setup.php" class="text-decoration-none" style="color: var(--gold)">Event Setup</a> page.</p>
    </div>
    <?php else: ?>
    <?php $listVerticals = loadVerticals(); ?>
    <div class="table-responsive">
        <table class="table table-dark table-hover dnd-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Session Code</th>
                    <th>Industry</th>
                    <th>Campaign</th>
                    <th>Scenarios</th>
                    <th>Participants</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['exercises'] as $ex): ?>
                <?php
                    $rowVerticalId = $ex['industry_vertical'] ?? '';
                    $rowVerticalLabel = '—';
                    if ($rowVerticalId !== '' && isset($listVerticals[$rowVerticalId])) {
                        $rowVerticalLabel = $listVerticals[$rowVerticalId]['icon'] . ' ' . $listVerticals[$rowVerticalId]['title'];
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($ex['event_name'] ?: 'Unnamed', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><code><?php echo htmlspecialchars($ex['session_code'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                    <td><?php echo htmlspecialchars($rowVerticalLabel, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($ex['campaign_category'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo count($ex['scenarios']); ?></td>
                    <td><?php echo count($ex['participants']); ?></td>
                    <td class="text-nowrap"><?php echo htmlspecialchars(substr($ex['created_at'], 0, 10), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <span class="badge <?php echo $ex['status'] === 'completed' ? 'bg-success' : ($ex['status'] === 'active' ? 'bg-warning' : 'bg-secondary'); ?>">
                            <?php echo ucfirst(htmlspecialchars($ex['status'], ENT_QUOTES, 'UTF-8')); ?>
                        </span>
                    </td>
                    <td>
                        <a href="history.php?id=<?php echo $ex['id']; ?>" class="btn btn-sm btn-outline-gold"><i class="bi bi-eye"></i> View</a>
                        <a href="api/export.php?type=full&code=<?php echo urlencode($ex['session_code']); ?>" class="btn btn-sm btn-outline-gold"><i class="bi bi-download"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($data['total_pages'] > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($p = 1; $p <= $data['total_pages']; $p++): ?>
            <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                <a class="page-link" href="history.php?page=<?php echo $p; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?>"><?php echo $p; ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
