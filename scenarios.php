<?php
/**
 * Cyber Quest — Scenario Browser
 * View all available scenario templates, grouped by campaign category
 */
$pageTitle = 'Cyber Quest — Quest Board';
require_once 'includes/header.php';

$scenarios = loadScenarios();
$campaigns = loadCampaigns();

// If a specific scenario is requested, show detail view
$viewId = isset($_GET['id']) ? sanitizeId($_GET['id']) : null;
$viewScenario = $viewId ? loadScenario($viewId) : null;

// Optional campaign filter
$filterCampaign = isset($_GET['campaign']) ? sanitizeId($_GET['campaign']) : '';
?>

<div class="container py-4">
    <?php if ($viewScenario): ?>
    <!-- Detailed Scenario View -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <a href="scenarios.php" class="btn btn-outline-gold mb-4">
                <i class="bi bi-arrow-left"></i> Back to Quest Board
            </a>

            <div class="dnd-card scenario-detail" style="border-color: <?php echo htmlspecialchars($viewScenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="dnd-card-header" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($viewScenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>33, transparent)">
                    <div class="d-flex align-items-center">
                        <span class="quest-icon-lg me-3"><?php echo $viewScenario['icon']; ?></span>
                        <div>
                            <h2 class="mb-0"><?php echo htmlspecialchars($viewScenario['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <h5 class="text-muted mb-0"><?php echo htmlspecialchars($viewScenario['subtitle'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        </div>
                        <span class="badge ms-auto fs-6" style="background-color: <?php echo htmlspecialchars($viewScenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($viewScenario['severity'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                </div>
                <div class="dnd-card-body">
                    <p class="quest-description fs-5"><?php echo htmlspecialchars($viewScenario['description'], ENT_QUOTES, 'UTF-8'); ?></p>

                    <div class="quest-stats-bar mb-4">
                        <div class="stat-item">
                            <i class="bi bi-clock"></i>
                            <span><?php echo $viewScenario['estimated_duration_minutes']; ?> minutes</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-bullseye"></i>
                            <span>DC <?php echo $viewScenario['difficulty_class']; ?></span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-lightning"></i>
                            <span><?php echo count($viewScenario['injects']); ?> Injects</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-people"></i>
                            <span><?php echo $viewScenario['recommended_players']; ?>–<?php echo $viewScenario['max_players']; ?> Players</span>
                        </div>
                    </div>

                    <!-- Roles -->
                    <h4 class="dnd-section-title mb-3">🛡️ Roles & Party Members</h4>
                    <div class="row g-3 mb-4">
                        <?php foreach ($viewScenario['roles'] as $role): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="role-card <?php echo $role['required'] ? 'role-required' : 'role-optional'; ?>">
                                <h6 class="mb-1">
                                    <?php echo htmlspecialchars($role['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if ($role['required']): ?>
                                    <span class="badge bg-danger ms-1">Required</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary ms-1">Optional</span>
                                    <?php endif; ?>
                                </h6>
                                <small class="text-muted"><?php echo htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8'); ?></small>
                                <p class="mb-0 mt-1 small"><?php echo htmlspecialchars($role['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Inject Timeline -->
                    <h4 class="dnd-section-title mb-3">⚡ Inject Timeline</h4>
                    <div class="inject-timeline">
                        <?php foreach ($viewScenario['injects'] as $index => $inject): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <span class="phase-number"><?php echo $inject['phase']; ?></span>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5><?php echo htmlspecialchars($inject['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($inject['time_offset'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <p class="inject-preview"><?php echo htmlspecialchars(substr($inject['narrative'], 0, 200), ENT_QUOTES, 'UTF-8'); ?>...</p>
                                <div class="inject-meta">
                                    <span class="meta-item"><i class="bi bi-chat-dots"></i> <?php echo count($inject['facilitator_prompts']); ?> Discussion Prompts</span>
                                    <span class="meta-item"><i class="bi bi-dice-5"></i> <?php echo count($inject['dice_events']); ?> Dice Events</span>
                                    <span class="meta-item"><i class="bi bi-exclamation-triangle"></i> <?php echo count($inject['random_complications']); ?> Possible Complications</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Debrief Questions -->
                    <h4 class="dnd-section-title mb-3 mt-4">📋 Debrief Questions</h4>
                    <div class="debrief-card">
                        <h5><?php echo htmlspecialchars($viewScenario['debrief']['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <ol class="dnd-list">
                            <?php foreach ($viewScenario['debrief']['questions'] as $q): ?>
                            <li><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- Scenario List grouped by Campaign -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="dnd-title">Quest Board</h1>
                <p class="dnd-subtitle">Browse available scenario templates grouped by campaign</p>
                <div class="hero-divider">═══════ ⚜️ ═══════</div>
            </div>

            <!-- Campaign filter pills -->
            <div class="text-center mb-4">
                <a href="scenarios.php" class="btn btn-sm <?php echo $filterCampaign === '' ? 'btn-gold' : 'btn-outline-gold'; ?> me-1 mb-1">All</a>
                <?php foreach ($campaigns as $cId => $camp): ?>
                <a href="scenarios.php?campaign=<?php echo htmlspecialchars(urlencode($cId), ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn btn-sm <?php echo $filterCampaign === $cId ? 'btn-gold' : 'btn-outline-gold'; ?> me-1 mb-1">
                    <?php echo $camp['icon']; ?> <?php echo htmlspecialchars($camp['title'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <?php endforeach; ?>
            </div>

            <?php
            // Group scenarios by campaign
            $grouped = [];
            foreach ($scenarios as $id => $scenario) {
                $sCampaign = $scenario['campaign'] ?? 'uncategorised';
                if ($filterCampaign !== '' && $sCampaign !== $filterCampaign) continue;
                $grouped[$sCampaign][] = ['id' => $id, 'scenario' => $scenario];
            }

            // Sort groups so campaigns with definitions come first
            $orderedGroups = [];
            foreach ($campaigns as $cId => $camp) {
                if (isset($grouped[$cId])) {
                    $orderedGroups[$cId] = $grouped[$cId];
                }
            }
            // Append any uncategorised
            if (isset($grouped['uncategorised'])) {
                $orderedGroups['uncategorised'] = $grouped['uncategorised'];
            }

            foreach ($orderedGroups as $groupKey => $items):
                $campInfo = $campaigns[$groupKey] ?? null;
            ?>
            <div class="mb-5">
                <div class="d-flex align-items-center mb-3">
                    <?php if ($campInfo): ?>
                    <span style="font-size: 1.5rem;" class="me-2"><?php echo $campInfo['icon']; ?></span>
                    <h3 class="dnd-section-title mb-0" style="color: <?php echo htmlspecialchars($campInfo['theme_color'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($campInfo['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <?php else: ?>
                    <h3 class="dnd-section-title mb-0">Uncategorised</h3>
                    <?php endif; ?>
                </div>
                <?php if ($campInfo): ?>
                <p class="text-muted small mb-3"><?php echo htmlspecialchars($campInfo['defining_characteristic'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($items as $item):
                        $id = $item['id'];
                        $scenario = $item['scenario'];
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="scenarios.php?id=<?php echo htmlspecialchars(urlencode($id), ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                            <div class="dnd-quest-card h-100" style="border-color: <?php echo htmlspecialchars($scenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="quest-card-header" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($scenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>22, transparent)">
                                    <span class="quest-icon"><?php echo $scenario['icon']; ?></span>
                                    <h4 class="quest-title"><?php echo htmlspecialchars($scenario['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <span class="quest-severity badge" style="background-color: <?php echo htmlspecialchars($scenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($scenario['severity'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <div class="quest-card-body">
                                    <p class="quest-description"><?php echo htmlspecialchars($scenario['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <div class="quest-stats">
                                        <div class="stat">
                                            <span class="stat-label">Duration</span>
                                            <span class="stat-value"><?php echo $scenario['estimated_duration_minutes']; ?> min</span>
                                        </div>
                                        <div class="stat">
                                            <span class="stat-label">DC</span>
                                            <span class="stat-value"><?php echo $scenario['difficulty_class']; ?></span>
                                        </div>
                                        <div class="stat">
                                            <span class="stat-label">Injects</span>
                                            <span class="stat-value"><?php echo count($scenario['injects']); ?></span>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <span class="btn btn-outline-gold btn-sm">View Quest Details</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($orderedGroups)): ?>
            <div class="text-center text-muted py-5">
                <p>No adventures found for this campaign filter.</p>
                <a href="scenarios.php" class="btn btn-outline-gold">View all adventures</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
