<?php
/**
 * Cyber Quest — Event Setup Page (DM / Facilitator)
 * Configure event name, select scenarios, add participants to departments,
 * and generate a unique session URL for players.
 */
$pageTitle = 'Cyber Quest — Event Setup';
require_once 'includes/header.php';

$scenarios = loadScenarios();
$session = getSessionData();
$departments = getDefaultDepartments();

// Handle form submissions
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        $error = 'Invalid session. Please refresh and try again.';
    } else {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'save_setup':
                // Save event name
                $eventName = trim($_POST['event_name'] ?? '');
                if ($eventName === '') {
                    $eventName = 'Cyber Quest Event';
                }
                $session['event_name'] = substr($eventName, 0, 200);

                // Save scenario order
                $order = $_POST['scenario_order'] ?? '';
                $scenarioIds = array_filter(explode(',', $order), function($id) use ($scenarios) {
                    return isset($scenarios[sanitizeId($id)]);
                });
                $scenarioIds = array_map('sanitizeId', $scenarioIds);
                $session['scenarios'] = $scenarioIds;

                // Save participants
                $participantNames = $_POST['participant_name'] ?? [];
                $participantDepts = $_POST['participant_dept'] ?? [];
                $participants = [];
                for ($i = 0; $i < count($participantNames); $i++) {
                    $name = trim($participantNames[$i] ?? '');
                    $dept = sanitizeId($participantDepts[$i] ?? '');
                    if ($name !== '' && isset($departments[$dept])) {
                        $participants[] = [
                            'name' => substr($name, 0, 100),
                            'department' => $dept
                        ];
                    }
                }
                $session['participants'] = $participants;

                // Generate session code if not already set
                if (empty($session['session_code'])) {
                    $session['session_code'] = generateSessionCode();
                }

                saveSessionData($session);
                $success = 'Event setup saved. Share the session code with participants.';
                break;

            case 'start_event':
                if (empty($session['scenarios'])) {
                    $error = 'Please add at least one scenario before starting the event.';
                } else {
                    $session['current_scenario'] = 0;
                    $session['current_inject'] = 0;
                    $session['roll_history'] = [];
                    $session['notes'] = $session['notes'] ?? [];
                    $session['started'] = true;

                    if (empty($session['session_code'])) {
                        $session['session_code'] = generateSessionCode();
                    }

                    saveSessionData($session);
                    header('Location: session.php');
                    exit;
                }
                break;

            case 'reset_setup':
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
                $success = 'Event setup has been reset.';
                break;
        }
    }
}

// Reload session
$session = getSessionData();

// Build participant list grouped by department for display
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

// Determine the player URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$playerUrl = $session['session_code']
    ? $protocol . '://' . $host . $basePath . '/player.php?code=' . urlencode($session['session_code'])
    : '';
?>

<div class="container py-4">
    <!-- Header -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10 text-center">
            <div class="dnd-hero">
                <div class="hero-ornament">📜 🗡️ 📜</div>
                <h1 class="dnd-title display-4">Event Setup</h1>
                <h2 class="dnd-subtitle">Forge Your Quest — Assemble Your Party</h2>
                <div class="hero-divider">═══════ ⚜️ ═══════</div>
                <p class="dnd-intro">
                    As the Dungeon Master, configure your tabletop event below. Name your campaign,
                    choose the scenarios, add your participants to their departments, and generate
                    the unique event link for your players.
                </p>
            </div>
        </div>
    </div>

    <?php if ($error): ?>
    <div class="row justify-content-center mb-3">
        <div class="col-lg-10">
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="row justify-content-center mb-3">
        <div class="col-lg-10">
            <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <form id="setupForm" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="action" value="save_setup">
        <input type="hidden" name="scenario_order" id="scenarioOrder" value="<?php echo htmlspecialchars(implode(',', $session['scenarios'] ?? []), ENT_QUOTES, 'UTF-8'); ?>">

        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Step 1: Event Name -->
                <div class="dnd-card mb-4">
                    <div class="dnd-card-header">
                        <h3><i class="bi bi-pencil-square"></i> Step 1 — Name Your Campaign</h3>
                    </div>
                    <div class="dnd-card-body">
                        <div class="mb-3">
                            <label for="eventName" class="form-label dnd-label">Event Name</label>
                            <input type="text" class="form-control dnd-input" id="eventName" name="event_name"
                                   value="<?php echo htmlspecialchars($session['event_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                   placeholder="e.g. Q2 2026 Incident Response Exercise" maxlength="200">
                        </div>
                    </div>
                </div>

                <!-- Step 2: Select Scenarios -->
                <div class="dnd-card mb-4">
                    <div class="dnd-card-header">
                        <h3><i class="bi bi-map"></i> Step 2 — Select Quests</h3>
                    </div>
                    <div class="dnd-card-body">
                        <p class="card-flavor-text">
                            Choose and order the scenarios for your event. Each quest will be played in sequence.
                        </p>
                        <div class="row">
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
                    </div>
                </div>

                <!-- Step 3: Add Participants -->
                <div class="dnd-card mb-4">
                    <div class="dnd-card-header">
                        <h3><i class="bi bi-people-fill"></i> Step 3 — Assemble Your Party</h3>
                    </div>
                    <div class="dnd-card-body">
                        <p class="card-flavor-text">
                            Add participants and assign them to departments. Each participant will be able to
                            view the event in real-time from their browser using the unique event link.
                        </p>

                        <div id="participantList">
                            <?php if (!empty($session['participants'])): ?>
                                <?php foreach ($session['participants'] as $index => $p): ?>
                                <div class="participant-row mb-2">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control dnd-input" name="participant_name[]"
                                                   value="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                                   placeholder="Participant name" maxlength="100">
                                        </div>
                                        <div class="col-md-5">
                                            <select class="form-select dnd-input" name="participant_dept[]">
                                                <?php foreach ($departments as $key => $label): ?>
                                                <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"
                                                    <?php echo ($p['department'] === $key) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-participant-btn">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <button type="button" class="btn btn-outline-gold btn-sm mt-2" id="addParticipantBtn">
                            <i class="bi bi-plus-lg"></i> Add Participant
                        </button>

                        <!-- Department summary -->
                        <div id="deptSummary" class="mt-4" style="display: none;">
                            <h5 class="dnd-section-title">Party Roster by Department</h5>
                            <div class="row g-3" id="deptSummaryCards">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save & Session Code -->
                <div class="dnd-card mb-4">
                    <div class="dnd-card-header">
                        <h3><i class="bi bi-link-45deg"></i> Step 4 — Generate Event Link</h3>
                    </div>
                    <div class="dnd-card-body">
                        <p class="card-flavor-text">
                            Save your setup to generate the unique event URL. Share this link with all
                            participants so they can follow the event in real-time from their browsers.
                        </p>

                        <div class="text-center mb-4">
                            <button type="submit" class="btn btn-gold btn-lg">
                                <i class="bi bi-floppy"></i> Save Event Setup
                            </button>
                        </div>

                        <?php if (!empty($session['session_code'])): ?>
                        <div class="session-code-display text-center">
                            <h5 class="dnd-label mb-3">Session Code</h5>
                            <div class="session-code-value mb-3">
                                <?php echo htmlspecialchars($session['session_code'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label dnd-label">Player Event URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control dnd-input" id="playerUrl"
                                           value="<?php echo htmlspecialchars($playerUrl, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                    <button type="button" class="btn btn-outline-gold" id="copyUrlBtn" title="Copy URL">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                Share this URL with your participants. They can open it in their browser to follow along.
                            </small>
                        </div>
                        <?php else: ?>
                        <div class="text-center text-muted">
                            <p><i class="bi bi-info-circle"></i> Save your setup to generate the event URL.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mb-5">
                    <form method="POST" class="d-inline" onsubmit="return confirm('Reset all event setup? This cannot be undone.');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="reset_setup">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Setup
                        </button>
                    </form>

                    <?php if (!empty($session['scenarios']) && !empty($session['session_code'])): ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="start_event">
                        <button type="submit" class="btn btn-gold btn-lg">
                            <i class="bi bi-shield-exclamation"></i> Begin Campaign
                        </button>
                    </form>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // Pass data to JS
    window.scenarioData = <?php echo json_encode($scenarios, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    window.departmentData = <?php echo json_encode($departments, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    window.preselectedScenarios = <?php echo json_encode($session['scenarios'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>

<?php require_once 'includes/footer.php'; ?>
