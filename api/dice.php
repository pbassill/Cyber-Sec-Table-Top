<?php
/**
 * Cyber Quest — Dice Roll API
 * Returns dice roll results as JSON
 */
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once '../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
$token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!validateCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'roll':
        $sides = (int)($_POST['sides'] ?? 20);
        $allowedDice = [4, 6, 8, 10, 12, 20, 100];
        if (!in_array($sides, $allowedDice)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid dice type']);
            exit;
        }
        $result = rollDice($sides);
        echo json_encode([
            'roll' => $result,
            'sides' => $sides,
            'dice' => "d{$sides}",
            'is_critical' => ($sides === 20 && ($result === 1 || $result === 20)),
            'is_nat_1' => ($sides === 20 && $result === 1),
            'is_nat_20' => ($sides === 20 && $result === 20)
        ]);
        break;

    case 'roll_with_outcome':
        $sides = (int)($_POST['sides'] ?? 20);
        $allowedDice = [4, 6, 8, 10, 12, 20, 100];
        if (!in_array($sides, $allowedDice)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid dice type']);
            exit;
        }
        $result = rollDice($sides);
        $outcomesJson = $_POST['outcomes'] ?? '{}';
        $outcomes = json_decode($outcomesJson, true);
        $outcome = null;

        if (is_array($outcomes)) {
            $outcome = getOutcome($result, $outcomes);
        }

        echo json_encode([
            'roll' => $result,
            'sides' => $sides,
            'dice' => "d{$sides}",
            'is_critical' => ($sides === 20 && ($result === 1 || $result === 20)),
            'is_nat_1' => ($sides === 20 && $result === 1),
            'is_nat_20' => ($sides === 20 && $result === 20),
            'outcome' => $outcome
        ]);
        break;

    case 'random_event':
        $type = $_POST['type'] ?? 'environmental';
        $allowedTypes = ['environmental', 'plot_twist', 'npc'];
        if (!in_array($type, $allowedTypes, true)) {
            $type = 'environmental';
        }
        $randomEvents = loadRandomEvents();
        $result = null;

        if ($type === 'environmental' && isset($randomEvents['random_events']['environmental'])) {
            $result = getRandomEnvironmentalEvent($randomEvents['random_events']['environmental']);
        } elseif ($type === 'plot_twist' && isset($randomEvents['random_events']['plot_twists'])) {
            $result = getRandomPlotTwist($randomEvents['random_events']['plot_twists']);
        } elseif ($type === 'npc' && isset($randomEvents['random_events']['npc_actions'])) {
            $result = getRandomNPC($randomEvents['random_events']['npc_actions']);
        }

        echo json_encode([
            'type' => $type,
            'event' => $result
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
