<?php
/**
 * Cyber Quest — Calendar Invite Generator
 * Generates .ics calendar file for exercise scheduling
 */
require_once '../includes/functions.php';

$code = isset($_GET['code']) ? preg_replace('/[^A-Z0-9]/', '', strtoupper($_GET['code'])) : '';
$eventName = trim($_GET['name'] ?? 'Cyber Quest Tabletop Exercise');
$eventName = mb_substr($eventName, 0, 200);

if ($code === '') {
    http_response_code(400);
    echo 'Session code required';
    exit;
}

$sharedSession = loadSharedSession($code);

// Build player URL
$playerUrl = buildPlayerUrl($code);

// Default to tomorrow 10:00-12:00 local time
$startTime = new DateTime('tomorrow 10:00:00', new DateTimeZone('Europe/London'));
$endTime = new DateTime('tomorrow 12:00:00', new DateTimeZone('Europe/London'));

$dtStart = $startTime->format('Ymd\THis');
$dtEnd = $endTime->format('Ymd\THis');
$dtStamp = (new DateTime('now', new DateTimeZone('UTC')))->format('Ymd\THis\Z');
$uid = $code . '-' . time() . '@cyberquest';

$description = "Cyber Quest — Incident Response Tabletop Exercise\\n\\n"
    . "Session Code: $code\\n"
    . "Player URL: $playerUrl\\n\\n"
    . "Instructions:\\n"
    . "1. Open the Player URL above in your browser\\n"
    . "2. Follow along as the facilitator guides you through the exercise\\n"
    . "3. Prepare any relevant reference materials (IRP, BCP, contact lists)\\n\\n"
    . "Ground Rules:\\n"
    . "- This is a no-fault exercise\\n"
    . "- Respond as you would in a real incident\\n"
    . "- All discussions are under Chatham House rules";

$safeEventName = str_replace(["\r", "\n", "\\"], '', $eventName);

$ics = "BEGIN:VCALENDAR\r\n"
    . "VERSION:2.0\r\n"
    . "PRODID:-//CyberQuest//Tabletop Exercise//EN\r\n"
    . "CALSCALE:GREGORIAN\r\n"
    . "METHOD:PUBLISH\r\n"
    . "BEGIN:VEVENT\r\n"
    . "DTSTART;TZID=Europe/London:$dtStart\r\n"
    . "DTEND;TZID=Europe/London:$dtEnd\r\n"
    . "DTSTAMP:$dtStamp\r\n"
    . "UID:$uid\r\n"
    . "SUMMARY:$safeEventName\r\n"
    . "DESCRIPTION:$description\r\n"
    . "URL:$playerUrl\r\n"
    . "STATUS:CONFIRMED\r\n"
    . "END:VEVENT\r\n"
    . "END:VCALENDAR\r\n";

$filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $eventName) . '.ics';

header('Content-Type: text/calendar; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');

echo $ics;
