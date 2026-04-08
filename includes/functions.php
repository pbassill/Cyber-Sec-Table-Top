<?php
/**
 * Cyber Security Tabletop Exercise Framework
 * Core Functions
 */

/**
 * Configure and start the PHP session with hardened settings.
 * Call this early, before any session access.
 */
function initSecureSession(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isSecure,
        'httponly'  => true,
        'samesite'  => 'Strict',
    ]);
    session_start();
}

/**
 * Load campaign category definitions from templates/campaigns.json
 */
function loadCampaigns(): array {
    $file = __DIR__ . '/../templates/campaigns.json';
    if (!file_exists($file)) return [];

    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

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
 * Get the default list of departments for participant assignment
 */
function getDefaultDepartments(): array {
    return [
        'it_security' => 'IT Security',
        'it_operations' => 'IT Operations',
        'legal' => 'Legal',
        'compliance' => 'Compliance',
        'communications' => 'Communications',
        'management' => 'Senior Management',
        'business_ops' => 'Business Operations',
        'hr' => 'Human Resources'
    ];
}

/**
 * Generate a unique session code for participant access
 */
function generateSessionCode(): string {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

/**
 * Build the player URL for a given session code.
 * Sanitises the HTTP_HOST to prevent host-header injection.
 */
function buildPlayerUrl(string $sessionCode): string {
    if ($sessionCode === '') {
        return '';
    }
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Validate host to prevent host-header injection
    $host = strtolower($host);
    if (filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false
        && !preg_match('/^\[?[a-f0-9:]+\]?(:\d+)?$/', $host)) {
        $host = 'localhost';
    }
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $protocol . '://' . $host . $basePath . '/player.php?code=' . urlencode($sessionCode);
}

/**
 * Get the path to a shared session file
 */
function getSharedSessionPath(string $code): string {
    $safeCode = preg_replace('/[^A-Z0-9]/', '', strtoupper($code));
    return __DIR__ . '/../data/sessions/' . $safeCode . '.json';
}

/**
 * Save shared session state to a file so participants can read it
 */
function saveSharedSession(array $data): void {
    if (empty($data['session_code'])) return;

    $path = getSharedSessionPath($data['session_code']);
    $sharedState = [
        'session_code' => $data['session_code'],
        'event_name' => $data['event_name'] ?? '',
        'selected_campaign' => $data['selected_campaign'] ?? '',
        'scenarios' => $data['scenarios'] ?? [],
        'current_scenario' => $data['current_scenario'] ?? 0,
        'current_inject' => $data['current_inject'] ?? 0,
        'participants' => $data['participants'] ?? [],
        'started' => $data['started'] ?? false,
        'notes' => $data['notes'] ?? [],
        'updated_at' => time()
    ];

    $result = file_put_contents($path, json_encode($sharedState, JSON_PRETTY_PRINT), LOCK_EX);
    if ($result === false) {
        error_log('Cyber Quest: Failed to write shared session file: ' . $path);
    } else {
        chmod($path, 0600);
    }
}

/**
 * Load shared session state by session code
 */
function loadSharedSession(string $code): ?array {
    $path = getSharedSessionPath($code);
    if (!file_exists($path)) return null;

    $content = file_get_contents($path);
    $data = json_decode($content, true);
    return is_array($data) ? $data : null;
}

/**
 * Delete shared session file
 */
function deleteSharedSession(string $code): void {
    $path = getSharedSessionPath($code);
    if (file_exists($path)) {
        unlink($path);
    }
}

/**
 * Get session data or initialize
 */
function getSessionData(): array {
    initSecureSession();

    if (!isset($_SESSION['campaign'])) {
        $_SESSION['campaign'] = [
            'scenarios' => [],
            'current_scenario' => 0,
            'current_inject' => 0,
            'roll_history' => [],
            'notes' => [],
            'participants' => [],
            'session_code' => '',
            'started' => false
        ];
    }

    return $_SESSION['campaign'];
}

/**
 * Save session data
 */
function saveSessionData(array $data): void {
    initSecureSession();
    $_SESSION['campaign'] = $data;

    // Also update shared session file for participant access
    if (!empty($data['session_code'])) {
        saveSharedSession($data);
    }

    // Persist to SQLite database for exercise history
    if (!empty($data['session_code'])) {
        require_once __DIR__ . '/database.php';
        saveExercise($data);
    }
}

/**
 * Generate a CSRF token
 */
function generateCsrfToken(): string {
    initSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token
 */
function validateCsrfToken(string $token): bool {
    initSecureSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
