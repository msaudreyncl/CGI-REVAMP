
<?php

// CGI — Coffee Quality Analysis API

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    header('Allow: POST');

    echo json_encode([
        "error" => "Method not allowed."
    ]);

    exit;
}

$rawInput = file_get_contents('php://input');

$data = json_decode($rawInput, true);

if (!is_array($data)) {

    http_response_code(400);

    echo json_encode([
        "error" => "Invalid JSON request."
    ]);

    exit;
}

$image = $data['image'] ?? null;
$weight = $data['weight'] ?? null;
$coffeeType = $data['coffeeType'] ?? null;

if (
    !is_string($image) ||
    !str_starts_with(
        $image,
        'data:image/jpeg;base64,'
    )
) {

    http_response_code(400);

    echo json_encode([
        "error" => "A JPEG sample image is required."
    ]);

    exit;
}

if (
    !is_numeric($weight) ||
    !is_finite((float) $weight) ||
    (float) $weight <= 0
) {

    http_response_code(400);

    echo json_encode([
        "error" => "A valid sample weight is required."
    ]);

    exit;
}

if ($coffeeType !== 'Robusta') {

    http_response_code(400);

    echo json_encode([
        "error" => "Only Robusta samples are supported."
    ]);

    exit;
}

/*
 * FUTURE INTEGRATION
 *
 * 1. Decode and validate the captured image.
 *
 * 2. Send it to the trained computer-vision
 *    inference service.
 *
 * 3. Receive bean detections, defect
 *    classifications, and the overall grade.
 *
 * 4. Apply the validated grading criteria.
 *
 * 5. Retrieve the configured reference price
 *    for the identified grade.
 *
 * 6. Calculate:
 *
 *    totalPrice = unitPrice * (weight / 1000)
 *
 * 7. Return a JSON response containing:
 *
 *    grade
 *    confidence
 *    beanCount
 *    defectCount
 *    defects
 *    unitPrice
 *    totalPrice
 *    demo = false
 *
 * Never generate placeholder predictions
 * in production mode.
 */

http_response_code(503);

echo json_encode([
    "error" => "Computer-vision model is not yet integrated.",
    "demo" => false
]);