<?php
/**
 * Cyber Quest — Incident Response Tabletop Exercise Framework
 * Landing Page / Campaign Setup
 */
$pageTitle = 'Cyber Quest — The Tavern';
require_once 'includes/header.php';

$scenarios = loadScenarios();
$session = getSessionData();
?>

<div class="container py-4">
    <!-- Hero Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10 text-center">
            <div class="dnd-hero">
                <div class="hero-ornament">⚔️ 🛡️ ⚔️</div>
                <h1 class="dnd-title display-3">Cyber Quest</h1>
                <h2 class="dnd-subtitle">Incident Response Tabletop Exercise</h2>
                <div class="hero-divider">═══════ ⚜️ ═══════</div>
                <p class="dnd-intro">
                    Welcome, brave defenders of the digital realm. Within these halls, you shall face
                    the darkest threats that plague our kingdoms — ransomware sorcerers, treacherous insiders,
                    and the corruption of trusted alliances. Gather your party, choose your quests, and
                    let the dice determine your fate.
                </p>
            </div>
        </div>
    </div>

    <!-- Campaign Builder -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="dnd-card">
                <div class="dnd-card-header">
                    <h3><i class="bi bi-map"></i> Campaign Builder — Forge Your Quest</h3>
                </div>
                <div class="dnd-card-body">
                    <p class="card-flavor-text">
                        Select and order the scenarios for your tabletop session. Drag to reorder.
                        Each quest will be played in sequence, with dice rolls determining the twists of fate.
                    </p>

                    <form id="campaignForm" method="POST" action="session.php">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="start_campaign">
                        <input type="hidden" name="scenario_order" id="scenarioOrder" value="">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="dnd-section-title">Available Quests</h5>
                                <div id="availableScenarios" class="scenario-list">
                                    <?php foreach ($scenarios as $id => $scenario): ?>
                                    <div class="scenario-card-mini" data-id="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
                                        <div class="d-flex align-items-center">
                                            <span class="scenario-icon me-3"><?php echo $scenario['icon']; ?></span>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($scenario['title'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($scenario['subtitle'], ENT_QUOTES, 'UTF-8'); ?>
                                                    — <?php echo $scenario['estimated_duration_minutes']; ?> min
                                                    — DC <?php echo $scenario['difficulty_class']; ?>
                                                </small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-gold add-scenario-btn" data-id="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="dnd-section-title">Your Campaign <span id="campaignDuration" class="badge bg-secondary ms-2">0 min</span></h5>
                                <div id="selectedScenarios" class="scenario-list campaign-dropzone">
                                    <p class="dropzone-text text-muted text-center py-4">
                                        <i class="bi bi-arrow-left"></i> Add quests from the available list
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-gold btn-lg" id="startCampaign" disabled>
                                <i class="bi bi-shield-exclamation"></i> Begin Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scenario Cards -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <h3 class="dnd-section-title text-center mb-4">
                <span class="section-ornament">⚔️</span> Available Quests <span class="section-ornament">⚔️</span>
            </h3>
            <div class="row g-4">
                <?php foreach ($scenarios as $id => $scenario): ?>
                <div class="col-md-4">
                    <div class="dnd-quest-card" style="border-color: <?php echo htmlspecialchars($scenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>">
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
                                    <span class="stat-label">Difficulty</span>
                                    <span class="stat-value">DC <?php echo $scenario['difficulty_class']; ?></span>
                                </div>
                                <div class="stat">
                                    <span class="stat-label">Injects</span>
                                    <span class="stat-value"><?php echo count($scenario['injects']); ?></span>
                                </div>
                                <div class="stat">
                                    <span class="stat-label">Players</span>
                                    <span class="stat-value"><?php echo $scenario['recommended_players']; ?>–<?php echo $scenario['max_players']; ?></span>
                                </div>
                            </div>
                            <h6 class="mt-3 mb-2 dnd-label">Required Roles:</h6>
                            <div class="role-badges">
                                <?php foreach ($scenario['roles'] as $role): ?>
                                    <?php if ($role['required']): ?>
                                    <span class="badge role-badge" title="<?php echo htmlspecialchars($role['description'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($role['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- How to Play -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="dnd-card">
                <div class="dnd-card-header">
                    <h3><i class="bi bi-book"></i> The Rules of Engagement</h3>
                </div>
                <div class="dnd-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="dnd-label">🎲 Dice Mechanics</h5>
                            <ul class="dnd-list">
                                <li><strong>D20 — The Fate Die:</strong> Rolled at critical decision points to determine outcomes. Natural 20 = Critical Success. Natural 1 = Critical Failure.</li>
                                <li><strong>D6 — The Chaos Die:</strong> Rolled between injects to determine random environmental events and complications.</li>
                                <li><strong>D4 — The Pressure Die:</strong> Determines time pressure modifiers for urgent decisions.</li>
                            </ul>

                            <h5 class="dnd-label mt-4">📋 Ground Rules</h5>
                            <ul class="dnd-list">
                                <li>This is a no-fault exercise. No blame, only improvement.</li>
                                <li>Respond as you would in a real incident.</li>
                                <li>The Facilitator (Dungeon Master) controls the pace.</li>
                                <li>All discussions are under Chatham House rules.</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="dnd-label">🏰 How a Campaign Works</h5>
                            <ol class="dnd-list">
                                <li><strong>Build Your Campaign:</strong> Select and order scenarios from the quest board.</li>
                                <li><strong>Assign Roles:</strong> Each participant takes a role (War Chief, Shadow Watcher, etc.).</li>
                                <li><strong>Play Through Injects:</strong> The facilitator reads each inject aloud, then guides discussion.</li>
                                <li><strong>Roll the Dice:</strong> At key moments, dice determine outcomes, complications, and twists.</li>
                                <li><strong>Debrief:</strong> After each scenario, assess what went well and what needs improvement.</li>
                            </ol>

                            <h5 class="dnd-label mt-4">🎯 Outcome Scale</h5>
                            <div class="outcome-scale">
                                <div class="outcome-item outcome-critical-fail">💀 Natural 1 — Critical Failure</div>
                                <div class="outcome-item outcome-fail">⚔️ 2–7 — Failure</div>
                                <div class="outcome-item outcome-partial">🛡️ 8–14 — Partial Success</div>
                                <div class="outcome-item outcome-success">⚡ 15–19 — Success</div>
                                <div class="outcome-item outcome-critical-success">👑 Natural 20 — Critical Success</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass scenario data to JS
    window.scenarioData = <?php echo json_encode($scenarios, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>

<?php require_once 'includes/footer.php'; ?>
