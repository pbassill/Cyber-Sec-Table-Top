<?php
/**
 * Cyber Quest — Scenarios API
 * Returns scenario data as JSON
 */
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once '../includes/functions.php';

$action = isset($_GET['action']) ? preg_replace('/[^a-z_]/', '', $_GET['action']) : 'list';

switch ($action) {
    case 'list':
        $scenarios = loadScenarios();
        $list = [];
        foreach ($scenarios as $id => $s) {
            $list[] = [
                'id' => $id,
                'title' => $s['title'],
                'subtitle' => $s['subtitle'],
                'description' => $s['description'],
                'severity' => $s['severity'],
                'theme_color' => $s['theme_color'],
                'icon' => $s['icon'],
                'estimated_duration_minutes' => $s['estimated_duration_minutes'],
                'difficulty_class' => $s['difficulty_class'],
                'inject_count' => count($s['injects']),
                'recommended_players' => $s['recommended_players'],
                'max_players' => $s['max_players']
            ];
        }
        echo json_encode(['scenarios' => $list]);
        break;

    case 'get':
        $id = isset($_GET['id']) ? sanitizeId($_GET['id']) : '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Scenario ID required']);
            exit;
        }
        $scenario = loadScenario($id);
        if (!$scenario) {
            http_response_code(404);
            echo json_encode(['error' => 'Scenario not found']);
            exit;
        }
        echo json_encode($scenario);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
