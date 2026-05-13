<?php
/**
 * Cyber Quest — Player View
 * Read-only participant view of the event, accessed via unique session code.
 * Auto-refreshes to stay in sync with the facilitator's session.
 */
$pageTitle = 'Cyber Quest — Player View';
require_once 'includes/header.php';

$code = isset($_GET['code']) ? preg_replace('/[^A-Z0-9]/', '', strtoupper($_GET['code'])) : '';
$sharedSession = $code !== '' ? loadSharedSession($code) : null;

// Load scenario data if session exists
$currentScenario = null;
$currentInject = null;
$allScenarios = loadScenarios();

if ($sharedSession && !empty($sharedSession['started'])) {
    $currentScenarioId = $sharedSession['scenarios'][$sharedSession['current_scenario']] ?? null;
    if ($currentScenarioId) {
        $currentScenario = loadScenario($currentScenarioId);
        if ($currentScenario && isset($currentScenario['injects'][$sharedSession['current_inject']])) {
            $currentInject = $currentScenario['injects'][$sharedSession['current_inject']];
        }
    }
}

// Group participants by department
$departments = getDefaultDepartments();
$participantsByDept = [];
foreach ($departments as $key => $label) {
    $participantsByDept[$key] = [];
}
if ($sharedSession) {
    foreach ($sharedSession['participants'] ?? [] as $p) {
        $dept = $p['department'] ?? '';
        if (isset($participantsByDept[$dept])) {
            $participantsByDept[$dept][] = $p;
        }
    }
}
// Remove empty departments
$participantsByDept = array_filter($participantsByDept, function($members) {
    return !empty($members);
});
?>

<div class="container py-4">
    <?php if ($code === ''): ?>
    <!-- No code provided — show join form -->
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="dnd-card">
                <div class="dnd-card-body py-5">
                    <h2 class="dnd-title mb-3">Join an Event</h2>
                    <div class="hero-divider mb-4">═══════ ⚜️ ═══════</div>
                    <p class="mb-4">Enter the session code provided by your Dungeon Master to join the event.</p>
                    <form method="GET" action="player.php">
                        <div class="input-group input-group-lg mb-3" style="max-width: 300px; margin: 0 auto;">
                            <input type="text" class="form-control dnd-input text-center" name="code"
                                   placeholder="SESSION CODE" maxlength="6" pattern="[A-Za-z0-9]{6}"
                                   style="font-family: 'Cinzel', serif; font-size: 1.5rem; letter-spacing: 4px;" required>
                        </div>
                        <button type="submit" class="btn btn-gold btn-lg">
                            <i class="bi bi-door-open"></i> Join Event
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php elseif (!$sharedSession): ?>
    <!-- Invalid code -->
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="dnd-card">
                <div class="dnd-card-body py-5">
                    <h2 class="dnd-title mb-3">⚠️ Event Not Found</h2>
                    <div class="hero-divider mb-4">═══════ ⚜️ ═══════</div>
                    <p class="mb-4">The session code <strong><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></strong> was not found.
                    Please check with your Dungeon Master and try again.</p>
                    <a href="player.php" class="btn btn-gold">
                        <i class="bi bi-arrow-left"></i> Try Again
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php elseif (empty($sharedSession['started'])): ?>
    <!-- Event not started yet — show waiting room -->
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="dnd-card">
                <div class="dnd-card-body py-5">
                    <h2 class="dnd-title mb-3">⏳ Awaiting the Dungeon Master</h2>
                    <div class="hero-divider mb-4">═══════ ⚜️ ═══════</div>
                    <?php if (!empty($sharedSession['event_name'])): ?>
                    <h4 class="dnd-subtitle mb-4"><?php echo htmlspecialchars($sharedSession['event_name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                    <?php endif; ?>
                    <?php
                        $waitingVerticalId = $sharedSession['selected_vertical'] ?? '';
                        $waitingVerticals = loadVerticals();
                        if ($waitingVerticalId !== '' && isset($waitingVerticals[$waitingVerticalId])):
                            $wv = $waitingVerticals[$waitingVerticalId];
                    ?>
                    <p class="mb-4">
                        <span class="badge" style="background-color: <?php echo htmlspecialchars($wv['theme_color'], ENT_QUOTES, 'UTF-8'); ?>;">
                            <?php echo $wv['icon'] . ' ' . htmlspecialchars($wv['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </p>
                    <?php endif; ?>
                    <p class="fs-5 mb-4">The event has not started yet. This page will automatically update when the Dungeon Master begins the campaign.</p>

                    <?php if (!empty($participantsByDept)): ?>
                    <h5 class="dnd-section-title mb-3">🛡️ Party Members</h5>
                    <div class="row g-3 justify-content-center text-start">
                        <?php foreach ($participantsByDept as $deptKey => $members): ?>
                        <div class="col-md-4">
                            <div class="dept-card">
                                <h6 class="dept-card-title"><?php echo htmlspecialchars($departments[$deptKey] ?? $deptKey, ENT_QUOTES, 'UTF-8'); ?></h6>
                                <ul class="dept-member-list">
                                    <?php foreach ($members as $m): ?>
                                    <li><?php echo htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8'); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="player-auto-refresh mt-4">
                        <small class="text-muted"><i class="bi bi-arrow-repeat"></i> Auto-refreshing every 5 seconds...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- Active event — show current scenario/inject -->
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Event Header -->
            <?php if (!empty($sharedSession['event_name'])): ?>
            <div class="player-event-header mb-3">
                <h4 class="dnd-label mb-0">
                    <i class="bi bi-broadcast"></i> <?php echo htmlspecialchars($sharedSession['event_name'], ENT_QUOTES, 'UTF-8'); ?>
                    <span class="badge bg-success ms-2">LIVE</span>
                    <?php
                        $liveVerticalId = $sharedSession['selected_vertical'] ?? '';
                        $liveVerticals = loadVerticals();
                        if ($liveVerticalId !== '' && isset($liveVerticals[$liveVerticalId])):
                            $lv = $liveVerticals[$liveVerticalId];
                    ?>
                    <span class="badge ms-2" style="background-color: <?php echo htmlspecialchars($lv['theme_color'], ENT_QUOTES, 'UTF-8'); ?>;">
                        <?php echo $lv['icon'] . ' ' . htmlspecialchars($lv['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <?php endif; ?>
                </h4>
            </div>
            <?php endif; ?>

            <!-- Campaign Progress Bar -->
            <div class="campaign-progress mb-4">
                <div class="progress-scenarios">
                    <?php foreach ($sharedSession['scenarios'] as $idx => $sId):
                        $s = loadScenario($sId); ?>
                        <div class="progress-scenario <?php echo $idx === $sharedSession['current_scenario'] ? 'active' : ($idx < $sharedSession['current_scenario'] ? 'completed' : ''); ?>">
                            <span class="progress-icon"><?php echo $s['icon'] ?? '📋'; ?></span>
                            <span class="progress-label"><?php echo htmlspecialchars($s['title'] ?? $sId, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($currentScenario):
                    $playerInjectCount = is_array($currentScenario['injects'] ?? null) ? count($currentScenario['injects']) : 0;
                ?>
                <?php if ($playerInjectCount > 0): ?>
                <div class="inject-progress mt-2">
                    <small>Inject <?php echo $sharedSession['current_inject'] + 1; ?> of <?php echo $playerInjectCount; ?></small>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gold" style="width: <?php echo (($sharedSession['current_inject'] + 1) / $playerInjectCount) * 100; ?>%"></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if ($currentScenario && $currentInject): ?>
            <!-- Current Scenario Header -->
            <div class="scenario-header mb-3" style="border-left: 4px solid <?php echo htmlspecialchars($currentScenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>">
                <h3>
                    <?php echo $currentScenario['icon']; ?>
                    <?php echo htmlspecialchars($currentScenario['title'], ENT_QUOTES, 'UTF-8'); ?>
                </h3>
                <small class="text-muted"><?php echo htmlspecialchars($currentScenario['subtitle'], ENT_QUOTES, 'UTF-8'); ?></small>
            </div>

            <!-- Current Inject -->
            <div class="dnd-card inject-card mb-4">
                <div class="dnd-card-header inject-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <span class="inject-badge"><?php echo htmlspecialchars($currentInject['id'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php echo htmlspecialchars($currentInject['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </h4>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($currentInject['time_offset'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <div class="dnd-card-body">
                    <!-- Narrative -->
                    <div class="inject-narrative">
                        <div class="narrative-icon">🔴</div>
                        <div class="narrative-label">INJECT</div>
                        <div class="narrative-text">
                            <?php echo formatNarrative($currentInject['narrative']); ?>
                        </div>
                    </div>

                    <!-- Discussion Prompts -->
                    <?php
                        $playerPrompts = isset($currentInject['facilitator_prompts']) && is_array($currentInject['facilitator_prompts'])
                            ? $currentInject['facilitator_prompts'] : [];
                        $playerHints = [
                            'Share what your team would do first, and who would lead the response.',
                            'Surface assumptions — what information would you need to be confident?',
                            'Compare with your day-to-day procedure: would it apply, or fall short?',
                            'Identify the trade-off — speed vs. accuracy, containment vs. continuity.',
                            'Decide who you would escalate to, and how you would phrase the ask.',
                            'Agree on the smallest concrete next action you could commit to today.'
                        ];
                    ?>
                    <?php if (!empty($playerPrompts)): ?>
                    <div class="facilitator-prompts mt-4">
                        <h5 class="dnd-label"><i class="bi bi-chat-quote"></i> Discussion Prompts</h5>
                        <p class="text-muted small mb-2"><i class="bi bi-info-circle"></i> Click a prompt to expand a hint and check it off as your team discusses it.</p>
                        <div class="prompt-list">
                            <?php foreach ($playerPrompts as $idx => $prompt): ?>
                            <details class="prompt-item">
                                <summary>
                                    <i class="bi bi-chevron-right prompt-chevron"></i>
                                    <span class="prompt-text"><?php echo htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8'); ?></span>
                                </summary>
                                <div class="prompt-body">
                                    <div class="prompt-notes-block">
                                        <strong><i class="bi bi-people"></i> Discuss with your team:</strong>
                                        <span><?php echo htmlspecialchars($playerHints[$idx % count($playerHints)], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>
                            </details>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Debrief Section (on last inject) -->
            <?php if ($sharedSession['current_inject'] === count($currentScenario['injects']) - 1): ?>
            <?php
                $playerDebriefQuestions = (isset($currentScenario['debrief']['questions']) && is_array($currentScenario['debrief']['questions']))
                    ? $currentScenario['debrief']['questions'] : [];
                $playerDebriefFocus = [
                    'Reflect on what your team did, not just what was said.',
                    'Tie this back to a specific moment in the inject.',
                    'Decide who would own a follow-up action and by when.',
                    'Compare this with how your real procedure would handle it.',
                    'Capture the metric you would use to know you succeeded.',
                    'Note any regulatory or contractual obligation worth raising.'
                ];
            ?>
            <div class="dnd-card debrief-section mb-4">
                <div class="dnd-card-header">
                    <h4><i class="bi bi-clipboard-check"></i> <?php echo htmlspecialchars($currentScenario['debrief']['title'] ?? 'Debrief', ENT_QUOTES, 'UTF-8'); ?></h4>
                </div>
                <div class="dnd-card-body">
                    <p class="card-flavor-text">Review and discuss the following questions. Click a question to reveal a discussion focus.</p>
                    <ol class="debrief-question-list">
                        <?php foreach ($playerDebriefQuestions as $idx => $q): ?>
                        <li>
                            <details class="debrief-question">
                                <summary>
                                    <i class="bi bi-chevron-right prompt-chevron"></i>
                                    <span><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></span>
                                </summary>
                                <div class="debrief-question-body">
                                    <strong><i class="bi bi-bullseye"></i> Discussion focus:</strong>
                                    <span><?php echo htmlspecialchars($playerDebriefFocus[$idx % count($playerDebriefFocus)], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </details>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <div class="player-auto-refresh mt-3 mb-4">
                <small class="text-muted"><i class="bi bi-arrow-repeat player-spin"></i> Auto-refreshing every 5 seconds...</small>
            </div>
        </div>

        <!-- Sidebar: Participants -->
        <div class="col-lg-4">
            <?php if (!empty($participantsByDept)): ?>
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h5 class="mb-0"><i class="bi bi-people-fill"></i> Party Members</h5>
                </div>
                <div class="dnd-card-body">
                    <?php foreach ($participantsByDept as $deptKey => $members): ?>
                    <div class="dept-card mb-3">
                        <h6 class="dept-card-title"><?php echo htmlspecialchars($departments[$deptKey] ?? $deptKey, ENT_QUOTES, 'UTF-8'); ?></h6>
                        <ul class="dept-member-list">
                            <?php foreach ($members as $m): ?>
                            <li><?php echo htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Session Notes (read-only) -->
            <?php if (!empty($sharedSession['notes'])): ?>
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> Scribe's Log</h5>
                </div>
                <div class="dnd-card-body">
                    <div class="notes-list" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach (array_reverse($sharedSession['notes']) as $note): ?>
                        <div class="note-item">
                            <small class="text-muted">[<?php echo htmlspecialchars($note['time'], ENT_QUOTES, 'UTF-8'); ?>]</small>
                            <span><?php echo htmlspecialchars($note['text'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    // Auto-refresh the player view
    (function() {
        var code = <?php echo json_encode($code, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        if (!code) return;

        var lastUpdate = <?php echo json_encode($sharedSession['updated_at'] ?? 0); ?>;

        function pollForUpdates() {
            fetch('api/session_state.php?code=' + encodeURIComponent(code) + '&since=' + lastUpdate)
                .then(function(r) {
                    if (r.status === 404) {
                        // The DM has ended the campaign — return to the join screen.
                        window.location.href = 'player.php';
                        return null;
                    }
                    if (!r.ok) {
                        // Transient error — retry on the next poll.
                        return null;
                    }
                    return r.json();
                })
                .then(function(data) {
                    if (data && data.changed) {
                        // Reload the page to show updated state
                        window.location.reload();
                    }
                })
                .catch(function() { /* Silently retry on next poll */ });
        }

        setInterval(pollForUpdates, 5000);
    })();
</script>

<?php require_once 'includes/footer.php'; ?>
