<?php
/**
 * Cyber Quest — Scenario Browser
 * View all available scenario templates
 */
$pageTitle = 'Cyber Quest — Quest Board';
require_once 'includes/header.php';

$scenarios = loadScenarios();

// If a specific scenario is requested, show detail view
$viewId = isset($_GET['id']) ? sanitizeId($_GET['id']) : null;
$viewScenario = $viewId ? loadScenario($viewId) : null;
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
    <!-- Scenario List -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="dnd-title">Quest Board</h1>
                <p class="dnd-subtitle">Browse available scenario templates for your campaign</p>
                <div class="hero-divider">═══════ ⚜️ ═══════</div>
            </div>

            <div class="row g-4">
                <?php foreach ($scenarios as $id => $scenario): ?>
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
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
