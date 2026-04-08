<?php
/**
 * Cyber Quest — Incident Response Tabletop Exercise Framework
 * Landing Page — Choose your path
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

    <!-- Choose Your Path -->
    <div class="row justify-content-center mb-5 g-4">
        <div class="col-lg-5">
            <div class="dnd-card path-card h-100">
                <div class="dnd-card-header text-center">
                    <h3><i class="bi bi-shield-shaded"></i> Dungeon Master</h3>
                </div>
                <div class="dnd-card-body text-center d-flex flex-column">
                    <div class="path-icon mb-3">🧙‍♂️</div>
                    <h4 class="dnd-section-title">Facilitate an Event</h4>
                    <p class="flex-grow-1">
                        Set up a new tabletop exercise event. Configure scenarios, add participants
                        to their departments, and generate a unique event URL for your players.
                    </p>
                    <div class="mt-3">
                        <a href="setup.php" class="btn btn-gold btn-lg w-100">
                            <i class="bi bi-plus-circle"></i> Create Event
                        </a>
                        <?php if ($session['started']): ?>
                        <a href="session.php" class="btn btn-outline-gold w-100 mt-2">
                            <i class="bi bi-play-circle"></i> Resume Active Campaign
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dnd-card path-card h-100">
                <div class="dnd-card-header text-center">
                    <h3><i class="bi bi-people-fill"></i> Player</h3>
                </div>
                <div class="dnd-card-body text-center d-flex flex-column">
                    <div class="path-icon mb-3">⚔️</div>
                    <h4 class="dnd-section-title">Join an Event</h4>
                    <p class="flex-grow-1">
                        Enter the session code provided by your Dungeon Master to join an event.
                        Follow along in real-time as the exercise unfolds.
                    </p>
                    <div class="mt-3">
                        <form action="player.php" method="GET" class="mb-0">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control dnd-input text-center" name="code"
                                       placeholder="SESSION CODE" maxlength="6" pattern="[A-Za-z0-9]{6}"
                                       style="font-family: 'Cinzel', serif; letter-spacing: 3px; font-size: 1.1rem;">
                                <button type="submit" class="btn btn-gold">
                                    <i class="bi bi-door-open"></i> Join
                                </button>
                            </div>
                        </form>
                        <a href="player.php" class="btn btn-outline-gold w-100">
                            <i class="bi bi-search"></i> Enter Session Code
                        </a>
                    </div>
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
                                <li><strong>Set Up Event:</strong> The Dungeon Master creates an event, selects scenarios, and adds participants.</li>
                                <li><strong>Share the Link:</strong> Participants join via the unique event URL to follow along.</li>
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

<?php require_once 'includes/footer.php'; ?>
