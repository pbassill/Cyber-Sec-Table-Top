<?php
/**
 * Cyber Quest — Facilitator Pack (Print-Friendly)
 * Generates a clean, printable facilitator briefing pack
 */
require_once '../includes/functions.php';

$code = isset($_GET['code']) ? preg_replace('/[^A-Z0-9]/', '', strtoupper($_GET['code'])) : '';

$sharedSession = $code !== '' ? loadSharedSession($code) : null;
if (!$sharedSession) {
    // Fall back to current session
    $session = getSessionData();
    if (empty($session['scenarios'])) {
        echo 'No active session found.';
        exit;
    }
    $sharedSession = $session;
}

$eventName = htmlspecialchars($sharedSession['event_name'] ?? 'Cyber Quest Exercise', ENT_QUOTES, 'UTF-8');
$scenarios = $sharedSession['scenarios'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facilitator Pack — <?php echo $eventName; ?></title>
    <style>
        body { font-family: Georgia, 'Times New Roman', serif; max-width: 800px; margin: 0 auto; padding: 20px; color: #1a1a1a; font-size: 11pt; }
        h1 { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
        h2 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 2em; page-break-before: auto; }
        h3 { color: #555; margin-top: 1.5em; }
        h4 { color: #666; margin-top: 1em; }
        .meta { text-align: center; color: #666; margin-bottom: 2em; }
        .inject { border: 1px solid #ccc; padding: 15px; margin: 15px 0; border-radius: 4px; page-break-inside: avoid; }
        .inject-header { background: #f5f5f5; margin: -15px -15px 15px; padding: 10px 15px; border-bottom: 1px solid #ccc; border-radius: 4px 4px 0 0; }
        .narrative { background: #fafafa; padding: 10px; border-left: 3px solid #333; margin: 10px 0; }
        .prompts li { margin: 5px 0; }
        .dm-notes { background: #fff3cd; border: 1px dashed #856404; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .dm-notes h5 { color: #856404; margin-top: 0; }
        .complications { background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .outcome-table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 10pt; }
        .outcome-table th, .outcome-table td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        .outcome-table th { background: #f5f5f5; }
        .framework-tag { display: inline-block; background: #e2e3e5; color: #383d41; padding: 2px 8px; border-radius: 3px; font-size: 9pt; margin: 2px; }
        .debrief { border: 2px solid #333; padding: 15px; margin-top: 2em; }
        .participants { columns: 2; }
        .participants li { margin: 3px 0; }
        @media print {
            body { font-size: 10pt; }
            .inject { page-break-inside: avoid; }
            h2 { page-break-before: always; }
            h2:first-of-type { page-break-before: auto; }
        }
    </style>
</head>
<body>
    <h1>⚔️ Facilitator Briefing Pack</h1>
    <div class="meta">
        <strong><?php echo $eventName; ?></strong><br>
        Session Code: <strong><?php echo htmlspecialchars($sharedSession['session_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong><br>
        Date: <?php echo date('d M Y'); ?>
    </div>

    <?php if (!empty($sharedSession['participants'])): ?>
    <h3>Participants</h3>
    <ul class="participants">
        <?php foreach ($sharedSession['participants'] as $p): ?>
        <li><strong><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></strong> — <?php echo htmlspecialchars($p['department'] ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <?php foreach ($scenarios as $sIdx => $sId):
        $scenario = loadScenario($sId);
        if (!$scenario) continue;
    ?>
    <h2><?php echo $scenario['icon']; ?> <?php echo htmlspecialchars($scenario['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><em><?php echo htmlspecialchars($scenario['subtitle'], ENT_QUOTES, 'UTF-8'); ?></em></p>
    <p><strong>Severity:</strong> <?php echo htmlspecialchars($scenario['severity'], ENT_QUOTES, 'UTF-8'); ?> |
       <strong>Duration:</strong> ~<?php echo $scenario['estimated_duration_minutes']; ?> min |
       <strong>DC:</strong> <?php echo $scenario['difficulty_class']; ?></p>

    <?php if (!empty($scenario['compliance_frameworks'])): ?>
    <p><strong>Compliance Frameworks:</strong>
        <?php foreach ($scenario['compliance_frameworks'] as $fw): ?>
        <span class="framework-tag"><?php echo htmlspecialchars($fw, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endforeach; ?>
    </p>
    <?php endif; ?>

    <h3>Roles Required</h3>
    <ul>
        <?php foreach ($scenario['roles'] as $role): ?>
        <li><strong><?php echo htmlspecialchars($role['title'], ENT_QUOTES, 'UTF-8'); ?></strong> (<?php echo htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8'); ?>) — <?php echo htmlspecialchars($role['description'], ENT_QUOTES, 'UTF-8'); ?> <?php echo $role['required'] ? '✅ Required' : '⬜ Optional'; ?></li>
        <?php endforeach; ?>
    </ul>

    <?php foreach ($scenario['injects'] as $inject): ?>
    <div class="inject">
        <div class="inject-header">
            <strong>[<?php echo htmlspecialchars($inject['id'], ENT_QUOTES, 'UTF-8'); ?>]</strong>
            <?php echo htmlspecialchars($inject['title'], ENT_QUOTES, 'UTF-8'); ?>
            <span style="float:right;"><?php echo htmlspecialchars($inject['time_offset'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <?php if (!empty($inject['compliance_frameworks'])): ?>
        <p>
            <?php foreach ($inject['compliance_frameworks'] as $fw): ?>
            <span class="framework-tag"><?php echo htmlspecialchars($fw, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endforeach; ?>
        </p>
        <?php endif; ?>

        <div class="narrative">
            <?php echo nl2br(htmlspecialchars($inject['narrative'], ENT_QUOTES, 'UTF-8')); ?>
        </div>

        <h4>Discussion Prompts</h4>
        <ol class="prompts">
            <?php foreach ($inject['facilitator_prompts'] as $prompt): ?>
            <li><?php echo htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ol>

        <?php if (!empty($inject['facilitator_notes'])): ?>
        <div class="dm-notes">
            <h5>🔒 Facilitator Notes (Private)</h5>
            <ul>
                <?php foreach ($inject['facilitator_notes'] as $note): ?>
                <li><?php echo htmlspecialchars($note, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($inject['dice_events'])): ?>
        <?php foreach ($inject['dice_events'] as $dice): ?>
        <h4>🎲 <?php echo htmlspecialchars($dice['description'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo strtoupper(htmlspecialchars($dice['dice'], ENT_QUOTES, 'UTF-8')); ?>)</h4>
        <table class="outcome-table">
            <tr><th>Roll</th><th>Outcome</th><th>Description</th><th>Modifier</th></tr>
            <?php foreach ($dice['outcomes'] as $key => $outcome): ?>
            <tr>
                <td><?php echo $outcome['range'][0]; ?>–<?php echo $outcome['range'][1]; ?></td>
                <td><strong><?php echo htmlspecialchars($outcome['title'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                <td><?php echo htmlspecialchars($outcome['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><em><?php echo htmlspecialchars($outcome['modifier'], ENT_QUOTES, 'UTF-8'); ?></em></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($inject['random_complications'])): ?>
        <div class="complications">
            <strong>Possible Complications:</strong>
            <ul>
                <?php foreach ($inject['random_complications'] as $c): ?>
                <li><?php echo htmlspecialchars($c, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div class="debrief">
        <h3>📋 <?php echo htmlspecialchars($scenario['debrief']['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <ol>
            <?php foreach ($scenario['debrief']['questions'] as $q): ?>
            <li><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ol>
    </div>
    <?php endforeach; ?>

    <div style="text-align: center; margin-top: 3em; color: #999; font-size: 9pt;">
        <p>Generated by Cyber Quest — Incident Response Tabletop Exercise Framework</p>
        <p><?php echo date('d M Y H:i'); ?></p>
    </div>
</body>
</html>
