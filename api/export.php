<?php
/**
 * Cyber Quest — Export API
 * Exports exercise data as CSV for corporate reporting
 */
require_once '../includes/functions.php';
require_once '../includes/database.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$type = isset($_GET['type']) ? $_GET['type'] : '';
$code = isset($_GET['code']) ? preg_replace('/[^A-Z0-9]/', '', strtoupper($_GET['code'])) : '';

if ($code === '') {
    http_response_code(400);
    echo 'Session code required';
    exit;
}

// Load session data — try database first, fall back to shared session
$exercise = getExerciseByCode($code);
$sharedSession = loadSharedSession($code);

if (!$exercise && !$sharedSession) {
    http_response_code(404);
    echo 'Session not found';
    exit;
}

$eventName = $exercise['event_name'] ?? $sharedSession['event_name'] ?? 'Exercise';
$safeEventName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $eventName);
$timestamp = date('Y-m-d');

switch ($type) {
    case 'gaps':
        exportGaps($exercise, $safeEventName, $timestamp);
        break;
    case 'notes':
        exportNotes($exercise, $sharedSession, $safeEventName, $timestamp);
        break;
    case 'evaluations':
        exportEvaluations($exercise, $safeEventName, $timestamp);
        break;
    case 'timeline':
        exportTimeline($exercise, $safeEventName, $timestamp);
        break;
    case 'full':
        exportFullReport($exercise, $sharedSession, $safeEventName, $timestamp);
        break;
    default:
        http_response_code(400);
        echo 'Invalid export type. Use: gaps, notes, evaluations, timeline, full';
        exit;
}

function outputCsv(string $filename, array $headers, array $rows): void {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');

    $output = fopen('php://output', 'w');
    // UTF-8 BOM for Excel compatibility
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, $headers);
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
}

function exportGaps(?array $exercise, string $safeName, string $date): void {
    $rows = [];
    if ($exercise) {
        $items = getActionItems((int)$exercise['id']);
        foreach ($items as $item) {
            $rows[] = [
                $item['gap_description'],
                $item['remediation_action'],
                $item['owner'],
                $item['target_date'] ?? '',
                ucfirst($item['status'])
            ];
        }
    }

    outputCsv(
        "CyberQuest_Gaps_{$safeName}_{$date}.csv",
        ['Identified Gap', 'Remediation Action', 'Owner', 'Target Date', 'Status'],
        $rows
    );
}

function exportNotes(?array $exercise, ?array $sharedSession, string $safeName, string $date): void {
    $notes = [];
    if ($exercise && !empty($exercise['notes'])) {
        $notes = $exercise['notes'];
    } elseif ($sharedSession && !empty($sharedSession['notes'])) {
        $notes = $sharedSession['notes'];
    }

    $rows = [];
    foreach ($notes as $note) {
        $rows[] = [
            $note['time'] ?? '',
            $note['text'] ?? '',
            isset($note['scenario']) ? 'Scenario ' . ($note['scenario'] + 1) : '',
            isset($note['inject']) ? 'Inject ' . ($note['inject'] + 1) : ''
        ];
    }

    outputCsv(
        "CyberQuest_Notes_{$safeName}_{$date}.csv",
        ['Time', 'Observation', 'Scenario', 'Inject'],
        $rows
    );
}

function exportEvaluations(?array $exercise, string $safeName, string $date): void {
    $rows = [];
    if ($exercise) {
        $evals = getEvaluations((int)$exercise['id']);
        foreach ($evals as $eval) {
            $rows[] = [
                $eval['question'],
                $eval['rating'],
                str_repeat('★', $eval['rating']) . str_repeat('☆', 5 - $eval['rating'])
            ];
        }
    }

    outputCsv(
        "CyberQuest_Evaluations_{$safeName}_{$date}.csv",
        ['Question', 'Rating (1-5)', 'Stars'],
        $rows
    );
}

function exportTimeline(?array $exercise, string $safeName, string $date): void {
    $rows = [];
    if ($exercise) {
        $timeline = getExerciseTimeline((int)$exercise['id']);
        foreach ($timeline as $event) {
            $rows[] = [
                $event['created_at'],
                ucfirst(str_replace('_', ' ', $event['event_type'])),
                'Scenario ' . ($event['scenario_index'] + 1),
                'Inject ' . ($event['inject_index'] + 1),
                json_encode($event['details'])
            ];
        }
    }

    outputCsv(
        "CyberQuest_Timeline_{$safeName}_{$date}.csv",
        ['Timestamp', 'Event Type', 'Scenario', 'Inject', 'Details'],
        $rows
    );
}

function exportFullReport(?array $exercise, ?array $sharedSession, string $safeName, string $date): void {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="CyberQuest_FullReport_' . $safeName . '_' . $date . '.csv"');
    header('Cache-Control: no-cache, no-store, must-revalidate');

    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");

    // Exercise Info
    fputcsv($output, ['=== EXERCISE SUMMARY ===']);
    fputcsv($output, ['Event Name', $exercise['event_name'] ?? ($sharedSession['event_name'] ?? '')]);
    fputcsv($output, ['Session Code', $exercise['session_code'] ?? ($sharedSession['session_code'] ?? '')]);
    fputcsv($output, ['Date', $exercise['created_at'] ?? date('Y-m-d')]);
    fputcsv($output, ['Status', ucfirst($exercise['status'] ?? 'unknown')]);

    // Scenarios
    $scenarios = $exercise['scenarios'] ?? ($sharedSession['scenarios'] ?? []);
    fputcsv($output, ['Scenarios', implode(', ', $scenarios)]);
    fputcsv($output, []);

    // Participants
    $participants = $exercise['participants'] ?? ($sharedSession['participants'] ?? []);
    if (!empty($participants)) {
        fputcsv($output, ['=== PARTICIPANTS ===']);
        fputcsv($output, ['Name', 'Department']);
        foreach ($participants as $p) {
            fputcsv($output, [$p['name'] ?? '', $p['department'] ?? '']);
        }
        fputcsv($output, []);
    }

    // Session Notes
    $notes = $exercise['notes'] ?? ($sharedSession['notes'] ?? []);
    if (!empty($notes)) {
        fputcsv($output, ['=== SESSION NOTES ===']);
        fputcsv($output, ['Time', 'Observation', 'Scenario', 'Inject']);
        foreach ($notes as $note) {
            fputcsv($output, [
                $note['time'] ?? '',
                $note['text'] ?? '',
                isset($note['scenario']) ? 'Scenario ' . ($note['scenario'] + 1) : '',
                isset($note['inject']) ? 'Inject ' . ($note['inject'] + 1) : ''
            ]);
        }
        fputcsv($output, []);
    }

    // Action Items
    if ($exercise) {
        $items = getActionItems((int)$exercise['id']);
        if (!empty($items)) {
            fputcsv($output, ['=== GAP ANALYSIS & ACTION ITEMS ===']);
            fputcsv($output, ['#', 'Identified Gap', 'Remediation Action', 'Owner', 'Target Date', 'Status']);
            $i = 1;
            foreach ($items as $item) {
                fputcsv($output, [
                    $i++,
                    $item['gap_description'],
                    $item['remediation_action'],
                    $item['owner'],
                    $item['target_date'] ?? '',
                    ucfirst($item['status'])
                ]);
            }
            fputcsv($output, []);
        }

        // Evaluations
        $evals = getEvaluations((int)$exercise['id']);
        if (!empty($evals)) {
            fputcsv($output, ['=== PARTICIPANT EVALUATIONS ===']);
            fputcsv($output, ['Question', 'Rating (1-5)']);
            foreach ($evals as $eval) {
                fputcsv($output, [$eval['question'], $eval['rating']]);
            }
            fputcsv($output, []);
        }

        // Timeline
        $timeline = getExerciseTimeline((int)$exercise['id']);
        if (!empty($timeline)) {
            fputcsv($output, ['=== EXERCISE TIMELINE ===']);
            fputcsv($output, ['Timestamp', 'Event Type', 'Scenario', 'Inject', 'Details']);
            foreach ($timeline as $event) {
                fputcsv($output, [
                    $event['created_at'],
                    ucfirst(str_replace('_', ' ', $event['event_type'])),
                    'Scenario ' . ($event['scenario_index'] + 1),
                    'Inject ' . ($event['inject_index'] + 1),
                    json_encode($event['details'])
                ]);
            }
        }
    }

    fclose($output);
}
