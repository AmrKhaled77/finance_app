<?php
require_once 'connection.php';

// ================= HELPER: CALL API WITH FALLBACK =================
function callApi($endpoint) {

    $primaryUrl = API_PRIMARY . $endpoint;
    $response = file_get_contents($primaryUrl);

    if ($response === false) {
        $fallbackUrl = API_FALLBACK . $endpoint;
        $response = file_get_contents($fallbackUrl);
    }

    if ($response === false) {
        return null;
    }

    return json_decode($response, true);
}


// ================= GET RATE =================
function getRate($from, $to) {

    $from = strtolower($from); // "EGP" → "egp"
    $to   = strtolower($to);   // "USD" → "usd"

    $data = callApi('/currencies/' . $from . '.json');

    // API returns: ["date" => "...", "egp" => ["usd" => 0.02, "eur" => 0.018, ...]]
    if ($data === null || !isset($data[$from][$to])) {
        return null;
    }

    return $data[$from][$to]; // this is the rate e.g. 0.02
}


// ================= ENTRY POINT (what JS hits) =================
$from   = $_GET['from']   ?? '';
$to     = $_GET['to']     ?? '';
$amount = $_GET['amount'] ?? 0;

header('Content-Type: application/json');

if ($from === '' || $to === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$rate = getRate($from, $to);

if ($rate === null) {
    echo json_encode(['status' => 'error', 'message' => 'Could not fetch rate']);
    exit;
}

$result = round($amount * $rate, 2);

echo json_encode([
    'status' => 'success',
    'result' => $result
]);