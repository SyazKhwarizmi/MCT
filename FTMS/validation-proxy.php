<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($action !== 'setValidation') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Unknown action']);
    exit;
}

$configPath = __DIR__ . '/config.js';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'config.js not found']);
    exit;
}

$configText = file_get_contents($configPath);
$scriptUrl = '';
if (preg_match("/validationScriptUrl:\s*'([^']*)'/", $configText, $matches)) {
    $scriptUrl = trim($matches[1]);
}

if ($scriptUrl === '' || strpos($scriptUrl, 'YOUR_') === 0) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Set validationScriptUrl in config.js to your Apps Script web app URL'
    ]);
    exit;
}

$row = isset($_GET['row']) ? $_GET['row'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sheet = isset($_GET['sheet']) ? $_GET['sheet'] : 'submission';

$query = http_build_query([
    'action' => 'setValidation',
    'row' => $row,
    'status' => $status,
    'sheet' => $sheet,
]);

$url = rtrim($scriptUrl, '?&') . (strpos($scriptUrl, '?') === false ? '?' : '&') . $query;

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 20,
        'ignore_errors' => true,
        'header' => "User-Agent: FTMS-Validation\r\n",
    ],
]);

$response = @file_get_contents($url, false, $context);
if ($response === false) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Could not reach Apps Script. Check the web app URL and deployment.']);
    exit;
}

$decoded = json_decode($response, true);
if (!is_array($decoded)) {
    // Apps Script sometimes returns an HTML redirect interstitial; try once more after following redirects via curl if available.
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'FTMS-Validation',
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($response, true);
    }
}

if (!is_array($decoded)) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Unexpected response from Apps Script', 'raw' => substr((string)$response, 0, 300)]);
    exit;
}

echo json_encode($decoded);
