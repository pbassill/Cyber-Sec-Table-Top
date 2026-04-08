<?php
/**
 * Cyber Quest — Session State API
 * Returns the current session state for player polling.
 * Players check if the state has changed since their last update.
 */
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once '../includes/functions.php';

$code = isset($_GET['code']) ? preg_replace('/[^A-Z0-9]/', '', strtoupper($_GET['code'] ?? '')) : '';
$since = (int)($_GET['since'] ?? 0);

if ($code === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Session code required']);
    exit;
}

$session = loadSharedSession($code);
if (!$session) {
    http_response_code(404);
    echo json_encode(['error' => 'Session not found']);
    exit;
}

$updatedAt = $session['updated_at'] ?? 0;
$changed = ($updatedAt > $since);

echo json_encode([
    'changed' => $changed,
    'updated_at' => $updatedAt
]);
