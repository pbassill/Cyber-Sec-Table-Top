<?php
/**
 * Cyber Security Tabletop Exercise Framework
 * Header Include - D&D Themed
 */
require_once __DIR__ . '/functions.php';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Cyber Quest — Incident Response Tabletop', ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&family=Cinzel:wght@400;700;900&family=Cinzel+Decorative:wght@400;700;900&family=Spectral:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark dnd-navbar">
        <div class="container">
            <a class="navbar-brand dnd-brand" href="index.php">
                <span class="brand-icon">⚔️</span> Cyber Quest
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Tavern</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="setup.php"><i class="bi bi-gear"></i> Event Setup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="scenarios.php"><i class="bi bi-journal-text"></i> Quest Board</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="session.php"><i class="bi bi-shield-exclamation"></i> War Room</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="player.php"><i class="bi bi-people"></i> Player View</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container-fluid main-content">
