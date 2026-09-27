
<?php

// CGI — Weighing Scale API

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// TODO:
// Read the calibrated weight from your
// Raspberry Pi / load-cell interface.
//
// Example of the required successful response:
//
// {
//     "weight": 349.6,
//     "unit": "g",
//     "demo": false
// }
//
// Only return this response after receiving
// a verified measurement from the hardware.

http_response_code(503);

echo json_encode([
    "error" => "Physical weighing scale is not yet integrated.",
    "weight" => null,
    "demo" => false
]);