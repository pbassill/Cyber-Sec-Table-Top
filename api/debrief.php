<?php
/**
 * Cyber Quest — Debrief Save API
 * Saves gap analysis items and evaluation ratings to the database
 */
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once '../includes/functions.php';
require_once '../includes/database.php';

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
$session = getSessionData();

if (empty($session['session_code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No active session']);
    exit;
}

// Ensure exercise exists in database
$exerciseId = saveExercise($session);
if (!$exerciseId) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save exercise']);
    exit;
}

switch ($action) {
    case 'save_gaps':
        $gaps = [];
        $gapTexts = $_POST['gap'] ?? [];
        $actionTexts = $_POST['remediation'] ?? [];
        $owners = $_POST['owner'] ?? [];
        $dates = $_POST['target_date'] ?? [];
        $statuses = $_POST['status'] ?? [];

        for ($i = 0; $i < count($gapTexts); $i++) {
            $gap = trim($gapTexts[$i] ?? '');
            $act = trim($actionTexts[$i] ?? '');
            if ($gap === '' && $act === '') continue;

            $gaps[] = [
                'gap' => mb_substr($gap, 0, 1000),
                'action' => mb_substr($act, 0, 1000),
                'owner' => mb_substr(trim($owners[$i] ?? ''), 0, 200),
                'target_date' => $dates[$i] ?? null,
                'status' => in_array($statuses[$i] ?? 'open', ['open', 'in_progress', 'closed']) ? ($statuses[$i] ?? 'open') : 'open'
            ];
        }

        saveActionItems($exerciseId, $gaps);
        echo json_encode(['success' => true, 'saved' => count($gaps)]);
        break;

    case 'save_evaluations':
        $ratings = [];
        $questions = $_POST['eval_question'] ?? [];
        $scores = $_POST['eval_rating'] ?? [];

        for ($i = 0; $i < count($questions); $i++) {
            $q = trim($questions[$i] ?? '');
            $r = (int)($scores[$i] ?? 0);
            if ($q !== '' && $r > 0) {
                $ratings[$q] = $r;
            }
        }

        saveEvaluations($exerciseId, $ratings);
        echo json_encode(['success' => true, 'saved' => count($ratings)]);
        break;

    case 'complete_exercise':
        completeExercise($session['session_code']);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
