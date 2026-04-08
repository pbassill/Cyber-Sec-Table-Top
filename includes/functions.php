<?php
/**
 * Cyber Security Tabletop Exercise Framework
 * Core Functions
 */

/**
 * Load all scenario templates from the templates directory
 */
function loadScenarios(): array {
    $scenarios = [];
    $templateDir = __DIR__ . '/../templates/';
    $files = glob($templateDir . '*.json');

    foreach ($files as $file) {
        $filename = basename($file, '.json');
        if ($filename === 'random_events') continue;

        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if ($data && isset($data['id'])) {
            $scenarios[$data['id']] = $data;
        }
    }

    return $scenarios;
}

/**
 * Load a single scenario by ID
 */
function loadScenario(string $id): ?array {
    $file = __DIR__ . '/../templates/' . basename($id) . '.json';
    if (!file_exists($file)) return null;

    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return $data ?: null;
}

/**
 * Load random events table
 */
function loadRandomEvents(): array {
    $file = __DIR__ . '/../templates/random_events.json';
    if (!file_exists($file)) return [];

    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return $data ?: [];
}

/**
 * Roll a dice and return the result
 */
function rollDice(int $sides = 20): int {
    return random_int(1, $sides);
}

/**
 * Get the outcome for a dice roll from an event's outcome table
 */
function getOutcome(int $roll, array $outcomes): ?array {
    foreach ($outcomes as $key => $outcome) {
        if (isset($outcome['range'])) {
            if ($roll >= $outcome['range'][0] && $roll <= $outcome['range'][1]) {
                return array_merge($outcome, ['outcome_key' => $key]);
            }
        }
    }
    return null;
}

/**
 * Get a random complication for an inject
 */
function getRandomComplication(array $complications): ?string {
    if (empty($complications)) return null;
    $index = random_int(0, count($complications) - 1);
    return $complications[$index];
}

/**
 * Get a random environmental event
 */
function getRandomEnvironmentalEvent(array $events): ?array {
    if (empty($events)) return null;
    $roll = rollDice(6);
    foreach ($events as $event) {
        if (in_array($roll, $event['trigger_on'])) {
            return array_merge($event, ['roll' => $roll]);
        }
    }
    return null;
}

/**
 * Get a random plot twist based on d20 roll
 */
function getRandomPlotTwist(array $twists): ?array {
    if (empty($twists)) return null;
    $roll = rollDice(20);
    foreach ($twists as $twist) {
        if (in_array($roll, $twist['trigger_on'])) {
            return array_merge($twist, ['roll' => $roll]);
        }
    }
    return null;
}

/**
 * Get a random NPC action
 */
function getRandomNPC(array $npcs): ?array {
    if (empty($npcs)) return null;
    $index = random_int(0, count($npcs) - 1);
    return $npcs[$index];
}

/**
 * Get the CSS class for a dice outcome
 */
function getOutcomeClass(string $key): string {
    $classes = [
        'critical_fail' => 'outcome-critical-fail',
        'fail' => 'outcome-fail',
        'partial' => 'outcome-partial',
        'success' => 'outcome-success',
        'critical_success' => 'outcome-critical-success'
    ];
    return $classes[$key] ?? 'outcome-partial';
}

/**
 * Get the icon for a dice outcome
 */
function getOutcomeIcon(string $key): string {
    $icons = [
        'critical_fail' => '💀',
        'fail' => '⚔️',
        'partial' => '🛡️',
        'success' => '⚡',
        'critical_success' => '👑'
    ];
    return $icons[$key] ?? '🎲';
}

/**
 * Format narrative text with proper paragraphs
 */
function formatNarrative(string $text): string {
    $paragraphs = explode("\n\n", $text);
    $html = '';
    foreach ($paragraphs as $p) {
        $html .= '<p>' . htmlspecialchars(trim($p), ENT_QUOTES, 'UTF-8') . '</p>';
    }
    return $html;
}

/**
 * Sanitize and validate scenario IDs
 */
function sanitizeId(string $id): string {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
}

/**
 * Get session data or initialize
 */
function getSessionData(): array {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['campaign'])) {
        $_SESSION['campaign'] = [
            'scenarios' => [],
            'current_scenario' => 0,
            'current_inject' => 0,
            'roll_history' => [],
            'notes' => [],
            'started' => false
        ];
    }

    return $_SESSION['campaign'];
}

/**
 * Save session data
 */
function saveSessionData(array $data): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['campaign'] = $data;
}

/**
 * Generate a CSRF token
 */
function generateCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token
 */
function validateCsrfToken(string $token): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
