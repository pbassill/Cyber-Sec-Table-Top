<?php
/**
 * Cyber Quest — Campaign Debrief
 * Final debrief and action capture page
 */
require_once 'includes/functions.php';
require_once 'includes/database.php';

$session = getSessionData();

if (!$session['started'] || empty($session['scenarios'])) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Cyber Quest — Campaign Debrief';
require_once 'includes/header.php';

// Collect all scenario data
$campaignScenarios = [];
foreach ($session['scenarios'] as $sId) {
    $s = loadScenario($sId);
    if ($s) $campaignScenarios[] = $s;
}

// Load existing saved data if available
$exerciseId = null;
$savedGaps = [];
$savedEvals = [];
if (!empty($session['session_code'])) {
    $exercise = getExerciseByCode($session['session_code']);
    if ($exercise) {
        $exerciseId = (int)$exercise['id'];
        $savedGaps = getActionItems($exerciseId);
        $savedEvals = getEvaluations($exerciseId);
    }
}

$sessionCode = $session['session_code'] ?? '';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="dnd-title">⚔️ Campaign Debrief ⚔️</h1>
                <h2 class="dnd-subtitle">Hot Wash & After-Action Review</h2>
                <?php
                    $debriefVerticalId = $session['selected_vertical'] ?? '';
                    $debriefVerticals = loadVerticals();
                    if ($debriefVerticalId !== '' && isset($debriefVerticals[$debriefVerticalId])):
                        $dv = $debriefVerticals[$debriefVerticalId];
                ?>
                <p class="mt-2 mb-0">
                    <span class="badge" style="background-color: <?php echo htmlspecialchars($dv['theme_color'], ENT_QUOTES, 'UTF-8'); ?>;">
                        <?php echo $dv['icon'] . ' ' . htmlspecialchars($dv['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </p>
                <?php endif; ?>
                <div class="hero-divider">═══════ ⚜️ ═══════</div>
            </div>

            <!-- Overall Debrief Questions -->
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h3><i class="bi bi-clipboard-check"></i> Structured Debrief</h3>
                </div>
                <div class="dnd-card-body">
                    <h5 class="dnd-label">Process and Procedures</h5>
                    <ol class="dnd-list mb-4">
                        <li>Were the Incident Response Plan and supporting procedures adequate for the scenarios presented?</li>
                        <li>Were escalation pathways clear and followed correctly? Were there any points of confusion?</li>
                        <li>Were regulatory notification obligations well understood? Did the team know the timelines and recipients?</li>
                        <li>Were roles and responsibilities clearly defined, or were there overlaps and gaps?</li>
                    </ol>

                    <h5 class="dnd-label">Technical Controls</h5>
                    <ol class="dnd-list mb-4">
                        <li>Did the detection and monitoring tools perform as expected in each scenario?</li>
                        <li>Were containment options adequate and timely? What additional controls would have helped?</li>
                        <li>Were backup and recovery capabilities sufficient? Were RTOs and RPOs achievable?</li>
                        <li>Are privileged access management controls adequate for both internal users and third parties?</li>
                    </ol>

                    <h5 class="dnd-label">Communication and Coordination</h5>
                    <ol class="dnd-list">
                        <li>Were internal communications effective across all functions?</li>
                        <li>Were client communication templates adequate and pre-approved?</li>
                        <li>Was the media response strategy effective and timely?</li>
                        <li>Were third-party and vendor management communications handled appropriately?</li>
                    </ol>
                </div>
            </div>

            <!-- Per-Scenario Debrief -->
            <?php foreach ($campaignScenarios as $scenario): ?>
            <div class="dnd-card mb-4" style="border-color: <?php echo htmlspecialchars($scenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="dnd-card-header" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($scenario['theme_color'], ENT_QUOTES, 'UTF-8'); ?>22, transparent)">
                    <h4><?php echo $scenario['icon']; ?> <?php echo htmlspecialchars($scenario['title'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars($scenario['debrief']['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                </div>
                <div class="dnd-card-body">
                    <ol class="dnd-list">
                        <?php foreach ($scenario['debrief']['questions'] as $q): ?>
                        <li><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Session Notes -->
            <?php if (!empty($session['notes'])): ?>
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h3><i class="bi bi-journal-text"></i> Scribe's Log — Session Notes</h3>
                </div>
                <div class="dnd-card-body">
                    <table class="table table-dark table-hover dnd-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Observation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($session['notes'] as $note): ?>
                            <tr>
                                <td class="text-nowrap"><?php echo htmlspecialchars($note['time'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($note['text'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Gap Identification -->
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h3><i class="bi bi-exclamation-diamond"></i> Gap Identification & Action Capture</h3>
                </div>
                <div class="dnd-card-body">
                    <p class="card-flavor-text">Record all identified gaps and remediation actions below.</p>
                    <table class="table table-dark table-hover dnd-table" id="gapTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Identified Gap</th>
                                <th>Remediation Action</th>
                                <th>Owner</th>
                                <th>Target Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><input type="text" class="form-control dnd-input form-control-sm" placeholder="Describe the gap..."></td>
                                <td><input type="text" class="form-control dnd-input form-control-sm" placeholder="Remediation action..."></td>
                                <td><input type="text" class="form-control dnd-input form-control-sm" placeholder="Owner..."></td>
                                <td><input type="date" class="form-control dnd-input form-control-sm"></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <button class="btn btn-outline-gold btn-sm" id="addGapRow">
                        <i class="bi bi-plus-lg"></i> Add Row
                    </button>
                </div>
            </div>

            <!-- Participant Evaluation -->
            <div class="dnd-card mb-4">
                <div class="dnd-card-header">
                    <h3><i class="bi bi-star"></i> Participant Evaluation</h3>
                </div>
                <div class="dnd-card-body">
                    <div class="table-responsive">
                        <table class="table table-dark dnd-table">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th style="width: 200px;">Rating (1–5)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $evalQuestions = [
                                    'Were the exercise objectives clearly communicated?',
                                    'Were the scenarios realistic and relevant?',
                                    'Did you feel your role and responsibilities were clear?',
                                    'Were escalation pathways and decision-making effective?',
                                    'Is the Incident Response Plan adequate for the scenarios?',
                                    'Were regulatory notification obligations well understood?',
                                    'Were communication strategies effective?',
                                    'Were technical containment and recovery options adequate?'
                                ];
                                foreach ($evalQuestions as $q):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <div class="star-rating">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <span class="star" data-value="<?php echo $s; ?>">☆</span>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center mb-5">
                <?php if ($sessionCode): ?>
                <div class="mb-3">
                    <button class="btn btn-gold btn-lg me-2" id="saveDebriefBtn">
                        <i class="bi bi-save"></i> Save Debrief Data
                    </button>
                    <button class="btn btn-gold btn-lg me-2" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print Report
                    </button>
                </div>
                <div class="mb-3">
                    <a href="api/export.php?type=full&code=<?php echo urlencode($sessionCode); ?>" class="btn btn-outline-gold me-1">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export Full Report (CSV)
                    </a>
                    <a href="api/export.php?type=gaps&code=<?php echo urlencode($sessionCode); ?>" class="btn btn-outline-gold me-1">
                        <i class="bi bi-download"></i> Export Gaps
                    </a>
                    <a href="api/export.php?type=notes&code=<?php echo urlencode($sessionCode); ?>" class="btn btn-outline-gold me-1">
                        <i class="bi bi-download"></i> Export Notes
                    </a>
                    <a href="api/export.php?type=evaluations&code=<?php echo urlencode($sessionCode); ?>" class="btn btn-outline-gold me-1">
                        <i class="bi bi-download"></i> Export Evaluations
                    </a>
                </div>
                <?php else: ?>
                <button class="btn btn-gold btn-lg me-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Report
                </button>
                <?php endif; ?>
                <form method="POST" action="session.php" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="reset">
                    <button type="submit" class="btn btn-outline-danger btn-lg" onclick="return confirm('End campaign and return to Tavern?')">
                        <i class="bi bi-x-circle"></i> End Campaign
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Add gap row functionality
document.getElementById('addGapRow')?.addEventListener('click', function() {
    const table = document.getElementById('gapTable').querySelector('tbody');
    const rowCount = table.rows.length + 1;
    const row = table.insertRow();
    row.innerHTML = `
        <td>${rowCount}</td>
        <td><input type="text" class="form-control dnd-input form-control-sm" placeholder="Describe the gap..."></td>
        <td><input type="text" class="form-control dnd-input form-control-sm" placeholder="Remediation action..."></td>
        <td><input type="text" class="form-control dnd-input form-control-sm" placeholder="Owner..."></td>
        <td><input type="date" class="form-control dnd-input form-control-sm"></td>
    `;
});

// Star rating functionality
document.querySelectorAll('.star-rating').forEach(function(rating) {
    rating.querySelectorAll('.star').forEach(function(star) {
        star.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            const stars = this.parentElement.querySelectorAll('.star');
            stars.forEach(function(s, i) {
                s.textContent = i < value ? '★' : '☆';
                s.classList.toggle('active', i < value);
            });
            this.parentElement.dataset.rating = value;
        });
    });
});

// Save debrief data to database
document.getElementById('saveDebriefBtn')?.addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Collect gap data
    const gapData = new FormData();
    gapData.append('csrf_token', csrfToken);
    gapData.append('action', 'save_gaps');
    const gapRows = document.getElementById('gapTable')?.querySelectorAll('tbody tr') || [];
    gapRows.forEach(function(row) {
        const inputs = row.querySelectorAll('input');
        if (inputs.length >= 4) {
            gapData.append('gap[]', inputs[0].value);
            gapData.append('remediation[]', inputs[1].value);
            gapData.append('owner[]', inputs[2].value);
            gapData.append('target_date[]', inputs[3].value);
            gapData.append('status[]', 'open');
        }
    });

    // Collect evaluation data
    const evalData = new FormData();
    evalData.append('csrf_token', csrfToken);
    evalData.append('action', 'save_evaluations');
    document.querySelectorAll('.star-rating').forEach(function(rating) {
        const question = rating.closest('tr')?.querySelector('td')?.textContent?.trim() || '';
        const score = rating.dataset.rating || '0';
        if (question) {
            evalData.append('eval_question[]', question);
            evalData.append('eval_rating[]', score);
        }
    });

    // Save both
    Promise.all([
        fetch('api/debrief.php', { method: 'POST', body: gapData }),
        fetch('api/debrief.php', { method: 'POST', body: evalData })
    ]).then(function(responses) {
        return Promise.all(responses.map(function(r) { return r.json(); }));
    }).then(function() {
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Saved!';
        btn.classList.remove('btn-gold');
        btn.classList.add('btn-success');
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save"></i> Save Debrief Data';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-gold');
        }, 2000);
    }).catch(function() {
        btn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Save Failed';
        btn.classList.add('btn-danger');
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save"></i> Save Debrief Data';
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-gold');
        }, 2000);
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
