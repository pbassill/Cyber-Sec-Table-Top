<?php
/**
 * Cyber Quest — Main Session Runner (War Room)
 * Handles campaign progression, dice rolls, and inject delivery
 */
$pageTitle = 'Cyber Quest — War Room';
require_once 'includes/header.php';

$scenarios = loadScenarios();
$randomEvents = loadRandomEvents();
$session = getSessionData();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        $error = 'Invalid session. Please return to the Tavern and try again.';
    } else {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'start_campaign':
                $order = $_POST['scenario_order'] ?? '';
                $scenarioIds = array_filter(explode(',', $order), function($id) use ($scenarios) {
                    return isset($scenarios[sanitizeId($id)]);
                });
                $scenarioIds = array_map('sanitizeId', $scenarioIds);

                if (!empty($scenarioIds)) {
                    $session['scenarios'] = $scenarioIds;
                    $session['current_scenario'] = 0;
                    $session['current_inject'] = 0;
                    $session['roll_history'] = [];
                    $session['notes'] = [];
                    $session['started'] = true;
                    if (empty($session['session_code'])) {
                        $session['session_code'] = generateSessionCode();
                    }
                    saveSessionData($session);
                }
                break;

            case 'next_inject':
                $currentScenarioId = $session['scenarios'][$session['current_scenario']] ?? null;
                if ($currentScenarioId) {
                    $currentScenario = loadScenario($currentScenarioId);
                    if ($currentScenario && $session['current_inject'] < count($currentScenario['injects']) - 1) {
                        $session['current_inject']++;
                    }
                    saveSessionData($session);
                }
                break;

            case 'prev_inject':
                if ($session['current_inject'] > 0) {
                    $session['current_inject']--;
                    saveSessionData($session);
                }
                break;

            case 'next_scenario':
                if ($session['current_scenario'] < count($session['scenarios']) - 1) {
                    $session['current_scenario']++;
                    $session['current_inject'] = 0;
                    saveSessionData($session);
                }
                break;

            case 'save_note':
                $note = trim($_POST['note'] ?? '');
                $note = mb_substr($note, 0, 500);
                if ($note !== '') {
                    // Cap session notes to prevent unbounded growth
                    if (count($session['notes']) >= 500) {
                        array_shift($session['notes']);
                    }
                    $session['notes'][] = [
                        'text' => $note,
                        'scenario' => $session['current_scenario'],
                        'inject' => $session['current_inject'],
                        'time' => date('H:i:s')
                    ];
                    saveSessionData($session);
                }
                break;

            case 'reset':
                $oldCode = $session['session_code'] ?? '';
                if ($oldCode !== '') {
                    deleteSharedSession($oldCode);
                }
                $session = [
                    'scenarios' => [],
                    'current_scenario' => 0,
                    'current_inject' => 0,
                    'roll_history' => [],
                    'notes' => [],
                    'participants' => [],
                    'session_code' => '',
                    'event_name' => '',
                    'started' => false
                ];
                saveSessionData($session);
                break;
        }
    }
}

// Reload session after any changes
$session = getSessionData();

// Get current scenario and inject
$currentScenarioId = $session['scenarios'][$session['current_scenario']] ?? null;
$currentScenario = $currentScenarioId ? loadScenario($currentScenarioId) : null;
$currentInject = null;
if ($currentScenario && isset($currentScenario['injects'][$session['current_inject']])) {
    $currentInject = $currentScenario['injects'][$session['current_inject']];
}
?>

<div class="container py-4">
    <?php if (!$session['started'] || empty($session['scenarios'])): ?>
    <!-- No active campaign -->
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="dnd-card">
                <div class="dnd-card-body py-5">
                    <h2 class="dnd-title">The War Room Awaits</h2>
                    <div class="hero-divider mb-4">═══════ ⚜️ ═══════</div>
                    <p class="fs-5 mb-4">No campaign is currently active. Visit the Tavern to assemble your quests and begin.</p>
                    <a href="index.php" class="btn btn-gold btn-lg">
                        <i class="bi bi-house-door"></i> Return to Tavern
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- Active Campaign -->
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Campaign Progress Bar -->
            <div class="campaign-progress mb-4">
                <div class="progress-scenarios">
                    <?php foreach ($session['scenarios'] as $idx => $sId): ?>
                        <?php $s = loadScenario($sId); ?>
                        <div class="progress-scenario <?php echo $idx === $session['current_scenario'] ? 'active' : ($idx < $session['current_scenario'] ? 'completed' : ''); ?>">
                            <span class="progress-icon"><?php echo $s['icon'] ?? '📋'; ?></span>
                            <span class="progress-label"><?php echo htmlspecialchars($s['title'] ?? $sId, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="inject-progress mt-2">
                    <?php if ($currentScenario): ?>
                    <small>Inject <?php echo $session['current_inject'] + 1; ?> of <?php echo count($currentScenario['injects']); ?></small>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gold" style="width: <?php echo (($session['current_inject'] + 1) / count($currentScenario['injects'])) * 100; ?>%"></div>
                    </div>
                    <?php endif; ?>
                </div>
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

                    <!-- Facilitator Prompts -->
                    <div class="facilitator-prompts mt-4">
                        <h5 class="dnd-label"><i class="bi bi-chat-quote"></i> Facilitator Discussion Prompts</h5>
                        <ul class="prompt-list">
                            <?php foreach ($currentInject['facilitator_prompts'] as $prompt): ?>
                            <li class="prompt-item">
                                <i class="bi bi-chevron-right"></i>
                                <?php echo htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8'); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Dice Events -->
                    <?php if (!empty($currentInject['dice_events'])): ?>
                    <div class="dice-events mt-4">
                        <h5 class="dnd-label"><i class="bi bi-dice-5"></i> Dice Events</h5>
                        <?php foreach ($currentInject['dice_events'] as $diceEvent): ?>
                        <div class="dice-event-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><?php echo htmlspecialchars($diceEvent['description'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                <span class="badge bg-primary"><?php echo htmlspecialchars(strtoupper($diceEvent['dice']), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <button type="button"
                                    class="btn btn-gold roll-dice-btn"
                                    data-dice="<?php echo htmlspecialchars($diceEvent['dice'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-trigger="<?php echo htmlspecialchars($diceEvent['trigger'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-outcomes='<?php echo json_encode($diceEvent['outcomes'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>'>
                                <i class="bi bi-dice-5"></i> Roll <?php echo htmlspecialchars(strtoupper($diceEvent['dice']), ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                            <div class="dice-result mt-3" id="result-<?php echo htmlspecialchars($diceEvent['trigger'], ENT_QUOTES, 'UTF-8'); ?>" style="display:none;">
                                <!-- Filled by JS -->
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Random Complication Button -->
                    <?php if (!empty($currentInject['random_complications'])): ?>
                    <div class="random-complications mt-4">
                        <h5 class="dnd-label"><i class="bi bi-exclamation-triangle"></i> Random Complications</h5>
                        <button type="button" class="btn btn-outline-danger draw-complication-btn"
                                data-complications='<?php echo json_encode($currentInject['random_complications'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>'>
                            <i class="bi bi-lightning"></i> Draw a Complication Card
                        </button>
                        <div class="complication-result mt-3" style="display:none;">
                            <!-- Filled by JS -->
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Navigation -->
            <div class="d-flex justify-content-between mb-4">
                <form method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="prev_inject">
                    <button type="submit" class="btn btn-outline-gold" <?php echo $session['current_inject'] === 0 ? 'disabled' : ''; ?>>
                        <i class="bi bi-arrow-left"></i> Previous Inject
                    </button>
                </form>

                <?php if ($session['current_inject'] < count($currentScenario['injects']) - 1): ?>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="next_inject">
                    <button type="submit" class="btn btn-gold">
                        Next Inject <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
                <?php elseif ($session['current_scenario'] < count($session['scenarios']) - 1): ?>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="next_scenario">
                    <button type="submit" class="btn btn-gold">
                        Next Quest <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
                <?php else: ?>
                <a href="debrief.php" class="btn btn-gold">
                    <i class="bi bi-flag"></i> Proceed to Debrief
                </a>
                <?php endif; ?>
            </div>

            <!-- Debrief Section (shown on last inject of a scenario) -->
            <?php if ($session['current_inject'] === count($currentScenario['injects']) - 1): ?>
            <div class="dnd-card debrief-section mb-4">
                <div class="dnd-card-header">
                    <h4><i class="bi bi-clipboard-check"></i> <?php echo htmlspecialchars($currentScenario['debrief']['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                </div>
                <div class="dnd-card-body">
                    <p class="card-flavor-text">Review and discuss the following questions before proceeding to the next quest.</p>
                    <ol class="dnd-list">
                        <?php foreach ($currentScenario['debrief']['questions'] as $q): ?>
                        <li><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Session Code & Player URL -->
            <?php if (!empty($session['session_code'])): ?>
            <?php
                $playerUrl = buildPlayerUrl($session['session_code']);
            ?>
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h5 class="mb-0"><i class="bi bi-broadcast"></i> Session Info</h5>
                </div>
                <div class="dnd-card-body text-center">
                    <?php if (!empty($session['event_name'])): ?>
                    <p class="mb-2"><strong><?php echo htmlspecialchars($session['event_name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    <?php endif; ?>
                    <div class="session-code-value mb-2">
                        <?php echo htmlspecialchars($session['session_code'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div class="input-group input-group-sm mb-2">
                        <input type="text" class="form-control dnd-input" value="<?php echo htmlspecialchars($playerUrl, ENT_QUOTES, 'UTF-8'); ?>" readonly id="sessionPlayerUrl">
                        <button type="button" class="btn btn-outline-gold" onclick="navigator.clipboard.writeText(document.getElementById('sessionPlayerUrl').value);" title="Copy URL">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <small class="text-muted">Share this with participants</small>
                </div>
            </div>
            <?php endif; ?>

            <!-- Party Members -->
            <?php
                $departments = getDefaultDepartments();
                $participantsByDept = [];
                foreach ($departments as $key => $label) {
                    $participantsByDept[$key] = [];
                }
                foreach ($session['participants'] ?? [] as $p) {
                    $dept = $p['department'] ?? '';
                    if (isset($participantsByDept[$dept])) {
                        $participantsByDept[$dept][] = $p;
                    }
                }
                $participantsByDept = array_filter($participantsByDept, function($members) {
                    return !empty($members);
                });
            ?>
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

            <!-- Quick Dice Roller -->
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h5 class="mb-0"><i class="bi bi-dice-5"></i> Quick Dice Roller</h5>
                </div>
                <div class="dnd-card-body text-center">
                    <div class="dice-display" id="quickDiceDisplay">
                        <span class="dice-value">—</span>
                    </div>
                    <div class="dice-buttons mt-3">
                        <button class="btn btn-sm btn-outline-gold quick-roll" data-sides="4">D4</button>
                        <button class="btn btn-sm btn-outline-gold quick-roll" data-sides="6">D6</button>
                        <button class="btn btn-sm btn-outline-gold quick-roll" data-sides="8">D8</button>
                        <button class="btn btn-sm btn-outline-gold quick-roll" data-sides="10">D10</button>
                        <button class="btn btn-sm btn-outline-gold quick-roll" data-sides="12">D12</button>
                        <button class="btn btn-sm btn-gold quick-roll" data-sides="20">D20</button>
                        <button class="btn btn-sm btn-outline-gold quick-roll" data-sides="100">D100</button>
                    </div>
                    <div class="roll-history mt-3" id="rollHistory">
                        <!-- Filled by JS -->
                    </div>
                </div>
            </div>

            <!-- Random Event Generator -->
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Random Event Generator</h5>
                </div>
                <div class="dnd-card-body">
                    <button class="btn btn-outline-danger w-100 mb-2" id="envEventBtn">
                        <i class="bi bi-cloud-lightning"></i> Environmental Event (D6)
                    </button>
                    <button class="btn btn-outline-warning w-100 mb-2" id="plotTwistBtn">
                        <i class="bi bi-shuffle"></i> Plot Twist (D20)
                    </button>
                    <button class="btn btn-outline-info w-100" id="npcActionBtn">
                        <i class="bi bi-person-exclamation"></i> NPC Action
                    </button>
                    <div class="random-event-result mt-3" id="randomEventResult" style="display:none;">
                        <!-- Filled by JS -->
                    </div>
                </div>
            </div>

            <!-- Session Notes -->
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> Scribe's Log</h5>
                </div>
                <div class="dnd-card-body">
                    <form method="POST" class="mb-3">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="save_note">
                        <div class="input-group">
                            <input type="text" name="note" class="form-control dnd-input" placeholder="Record an observation..." maxlength="500">
                            <button type="submit" class="btn btn-outline-gold">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </form>
                    <div class="notes-list" style="max-height: 300px; overflow-y: auto;">
                        <?php if (empty($session['notes'])): ?>
                        <p class="text-muted small text-center">No notes recorded yet.</p>
                        <?php else: ?>
                            <?php foreach (array_reverse($session['notes']) as $note): ?>
                            <div class="note-item">
                                <small class="text-muted">[<?php echo htmlspecialchars($note['time'], ENT_QUOTES, 'UTF-8'); ?>]</small>
                                <span><?php echo htmlspecialchars($note['text'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Campaign Controls -->
            <div class="dnd-card">
                <div class="dnd-card-header">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Campaign Controls</h5>
                </div>
                <div class="dnd-card-body">
                    <form method="POST" onsubmit="return confirm('Are you sure you want to end the campaign? All progress will be lost.');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="reset">
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle"></i> End Campaign
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    // Pass random events data to JS
    window.randomEventsData = <?php echo json_encode($randomEvents, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>

<?php require_once 'includes/footer.php'; ?>
