<?php
/**
 * Cyber Quest — SQLite Database Layer
 * Persistent storage for exercise history, action items, and evaluations
 */

/**
 * Get the SQLite database connection (singleton)
 */
function getDatabase(): PDO {
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $dbPath = __DIR__ . '/../data/cyberquest.sqlite';
    $isNew = !file_exists($dbPath);

    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec('PRAGMA journal_mode = WAL');
    $db->exec('PRAGMA foreign_keys = ON');

    if ($isNew) {
        chmod($dbPath, 0600);
    }

    runMigrations($db);

    return $db;
}

/**
 * Run database migrations
 */
function runMigrations(PDO $db): void {
    $db->exec('CREATE TABLE IF NOT EXISTS schema_version (
        version INTEGER PRIMARY KEY
    )');

    $currentVersion = 0;
    $stmt = $db->query('SELECT MAX(version) as v FROM schema_version');
    $row = $stmt->fetch();
    if ($row && $row['v'] !== null) {
        $currentVersion = (int)$row['v'];
    }

    $migrations = getMigrations();
    foreach ($migrations as $version => $sql) {
        if ($version > $currentVersion) {
            $db->exec($sql);
            $db->exec("INSERT INTO schema_version (version) VALUES ($version)");
        }
    }
}

/**
 * Define database migrations
 */
function getMigrations(): array {
    return [
        1 => "
            CREATE TABLE exercises (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_code TEXT NOT NULL UNIQUE,
                event_name TEXT NOT NULL DEFAULT '',
                campaign_category TEXT NOT NULL DEFAULT '',
                scenarios TEXT NOT NULL DEFAULT '[]',
                participants TEXT NOT NULL DEFAULT '[]',
                notes TEXT NOT NULL DEFAULT '[]',
                roll_history TEXT NOT NULL DEFAULT '[]',
                status TEXT NOT NULL DEFAULT 'active',
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                completed_at TEXT,
                updated_at TEXT NOT NULL DEFAULT (datetime('now'))
            );
            CREATE INDEX idx_exercises_status ON exercises(status);
            CREATE INDEX idx_exercises_created ON exercises(created_at);
        ",
        2 => "
            CREATE TABLE action_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                exercise_id INTEGER NOT NULL,
                gap_description TEXT NOT NULL DEFAULT '',
                remediation_action TEXT NOT NULL DEFAULT '',
                owner TEXT NOT NULL DEFAULT '',
                target_date TEXT,
                status TEXT NOT NULL DEFAULT 'open',
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now')),
                FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE
            );
            CREATE INDEX idx_actions_exercise ON action_items(exercise_id);
            CREATE INDEX idx_actions_status ON action_items(status);
        ",
        3 => "
            CREATE TABLE evaluations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                exercise_id INTEGER NOT NULL,
                question TEXT NOT NULL,
                rating INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE
            );
            CREATE INDEX idx_evaluations_exercise ON evaluations(exercise_id);
        ",
        4 => "
            CREATE TABLE exercise_timeline (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                exercise_id INTEGER NOT NULL,
                event_type TEXT NOT NULL,
                scenario_index INTEGER NOT NULL DEFAULT 0,
                inject_index INTEGER NOT NULL DEFAULT 0,
                details TEXT NOT NULL DEFAULT '{}',
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE
            );
            CREATE INDEX idx_timeline_exercise ON exercise_timeline(exercise_id);
        "
    ];
}

/**
 * Save an exercise to the database (create or update)
 */
function saveExercise(array $sessionData): ?int {
    if (empty($sessionData['session_code'])) {
        return null;
    }

    $db = getDatabase();

    // Check if exercise already exists
    $stmt = $db->prepare('SELECT id FROM exercises WHERE session_code = ?');
    $stmt->execute([$sessionData['session_code']]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $db->prepare("UPDATE exercises SET
            event_name = ?,
            campaign_category = ?,
            scenarios = ?,
            participants = ?,
            notes = ?,
            roll_history = ?,
            status = ?,
            updated_at = datetime('now')
            WHERE session_code = ?");
        $stmt->execute([
            $sessionData['event_name'] ?? '',
            $sessionData['selected_campaign'] ?? '',
            json_encode($sessionData['scenarios'] ?? []),
            json_encode($sessionData['participants'] ?? []),
            json_encode($sessionData['notes'] ?? []),
            json_encode($sessionData['roll_history'] ?? []),
            ($sessionData['started'] ?? false) ? 'active' : 'setup',
            $sessionData['session_code']
        ]);
        return (int)$existing['id'];
    } else {
        $stmt = $db->prepare('INSERT INTO exercises
            (session_code, event_name, campaign_category, scenarios, participants, notes, roll_history, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $sessionData['session_code'],
            $sessionData['event_name'] ?? '',
            $sessionData['selected_campaign'] ?? '',
            json_encode($sessionData['scenarios'] ?? []),
            json_encode($sessionData['participants'] ?? []),
            json_encode($sessionData['notes'] ?? []),
            json_encode($sessionData['roll_history'] ?? []),
            ($sessionData['started'] ?? false) ? 'active' : 'setup'
        ]);
        return (int)$db->lastInsertId();
    }
}

/**
 * Complete an exercise (mark as finished)
 */
function completeExercise(string $sessionCode): void {
    $db = getDatabase();
    $stmt = $db->prepare("UPDATE exercises SET status = 'completed', completed_at = datetime('now'), updated_at = datetime('now') WHERE session_code = ?");
    $stmt->execute([$sessionCode]);
}

/**
 * Get exercise by session code
 */
function getExerciseByCode(string $code): ?array {
    $db = getDatabase();
    $stmt = $db->prepare('SELECT * FROM exercises WHERE session_code = ?');
    $stmt->execute([$code]);
    $row = $stmt->fetch();
    if (!$row) return null;

    $row['scenarios'] = json_decode($row['scenarios'], true) ?: [];
    $row['participants'] = json_decode($row['participants'], true) ?: [];
    $row['notes'] = json_decode($row['notes'], true) ?: [];
    $row['roll_history'] = json_decode($row['roll_history'], true) ?: [];
    return $row;
}

/**
 * Get exercise by ID
 */
function getExerciseById(int $id): ?array {
    $db = getDatabase();
    $stmt = $db->prepare('SELECT * FROM exercises WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) return null;

    $row['scenarios'] = json_decode($row['scenarios'], true) ?: [];
    $row['participants'] = json_decode($row['participants'], true) ?: [];
    $row['notes'] = json_decode($row['notes'], true) ?: [];
    $row['roll_history'] = json_decode($row['roll_history'], true) ?: [];
    return $row;
}

/**
 * List exercises with pagination
 */
function listExercises(int $page = 1, int $perPage = 20, string $status = ''): array {
    $db = getDatabase();
    $offset = ($page - 1) * $perPage;

    $where = '';
    $params = [];
    if ($status !== '') {
        $where = 'WHERE status = ?';
        $params[] = $status;
    }

    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM exercises $where");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetch()['total'];

    $params[] = $perPage;
    $params[] = $offset;
    $stmt = $db->prepare("SELECT * FROM exercises $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    foreach ($rows as &$row) {
        $row['scenarios'] = json_decode($row['scenarios'], true) ?: [];
        $row['participants'] = json_decode($row['participants'], true) ?: [];
        $row['notes'] = json_decode($row['notes'], true) ?: [];
        $row['roll_history'] = json_decode($row['roll_history'], true) ?: [];
    }

    return [
        'exercises' => $rows,
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => (int)ceil($total / $perPage)
    ];
}

/**
 * Save action items for an exercise
 */
function saveActionItems(int $exerciseId, array $items): void {
    $db = getDatabase();

    // Delete existing items for this exercise
    $stmt = $db->prepare('DELETE FROM action_items WHERE exercise_id = ?');
    $stmt->execute([$exerciseId]);

    $insert = $db->prepare('INSERT INTO action_items
        (exercise_id, gap_description, remediation_action, owner, target_date, status)
        VALUES (?, ?, ?, ?, ?, ?)');

    foreach ($items as $item) {
        if (empty($item['gap']) && empty($item['action'])) continue;
        $insert->execute([
            $exerciseId,
            mb_substr($item['gap'] ?? '', 0, 1000),
            mb_substr($item['action'] ?? '', 0, 1000),
            mb_substr($item['owner'] ?? '', 0, 200),
            $item['target_date'] ?? null,
            $item['status'] ?? 'open'
        ]);
    }
}

/**
 * Get action items for an exercise
 */
function getActionItems(int $exerciseId): array {
    $db = getDatabase();
    $stmt = $db->prepare('SELECT * FROM action_items WHERE exercise_id = ? ORDER BY id');
    $stmt->execute([$exerciseId]);
    return $stmt->fetchAll();
}

/**
 * Save evaluation ratings
 */
function saveEvaluations(int $exerciseId, array $ratings): void {
    $db = getDatabase();

    $stmt = $db->prepare('DELETE FROM evaluations WHERE exercise_id = ?');
    $stmt->execute([$exerciseId]);

    $insert = $db->prepare('INSERT INTO evaluations (exercise_id, question, rating) VALUES (?, ?, ?)');
    foreach ($ratings as $question => $rating) {
        $rating = max(0, min(5, (int)$rating));
        $insert->execute([$exerciseId, mb_substr($question, 0, 500), $rating]);
    }
}

/**
 * Get evaluations for an exercise
 */
function getEvaluations(int $exerciseId): array {
    $db = getDatabase();
    $stmt = $db->prepare('SELECT question, rating FROM evaluations WHERE exercise_id = ? ORDER BY id');
    $stmt->execute([$exerciseId]);
    return $stmt->fetchAll();
}

/**
 * Record a timeline event
 */
function recordTimelineEvent(int $exerciseId, string $eventType, int $scenarioIndex, int $injectIndex, array $details = []): void {
    $db = getDatabase();
    $stmt = $db->prepare('INSERT INTO exercise_timeline
        (exercise_id, event_type, scenario_index, inject_index, details)
        VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $exerciseId,
        $eventType,
        $scenarioIndex,
        $injectIndex,
        json_encode($details)
    ]);
}

/**
 * Get timeline for an exercise
 */
function getExerciseTimeline(int $exerciseId): array {
    $db = getDatabase();
    $stmt = $db->prepare('SELECT * FROM exercise_timeline WHERE exercise_id = ? ORDER BY created_at ASC');
    $stmt->execute([$exerciseId]);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['details'] = json_decode($row['details'], true) ?: [];
    }
    return $rows;
}

/**
 * Get summary statistics across all exercises
 */
function getExerciseStats(): array {
    $db = getDatabase();

    $total = $db->query('SELECT COUNT(*) as c FROM exercises')->fetch()['c'];
    $completed = $db->query("SELECT COUNT(*) as c FROM exercises WHERE status = 'completed'")->fetch()['c'];
    $openActions = $db->query("SELECT COUNT(*) as c FROM action_items WHERE status = 'open'")->fetch()['c'];
    $closedActions = $db->query("SELECT COUNT(*) as c FROM action_items WHERE status = 'closed'")->fetch()['c'];

    $avgRating = $db->query('SELECT AVG(rating) as avg FROM evaluations WHERE rating > 0')->fetch()['avg'];

    return [
        'total_exercises' => (int)$total,
        'completed_exercises' => (int)$completed,
        'open_actions' => (int)$openActions,
        'closed_actions' => (int)$closedActions,
        'avg_rating' => $avgRating ? round((float)$avgRating, 1) : null
    ];
}
