
<?php

// CGI — Hardware Status API

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Change to false when the Raspberry Pi
// hardware integrations are implemented.

$demoMode = true;

if ($demoMode) {

    echo json_encode([
        "camera" => "READY",
        "weighingScale" => "READY",
        "printer" => "READY",
        "demo" => true
    ]);

    exit;
}

// PRODUCTION MODE
//
// Replace these values with actual hardware
// status checks from your Raspberry Pi.
//
// READY   = Verified and operational
// WARNING = Connected but needs attention
// ERROR   = Disconnected or not operational

echo json_encode([
    "camera" => "ERROR",
    "weighingScale" => "ERROR",
    "printer" => "ERROR",
    "demo" => false
]);