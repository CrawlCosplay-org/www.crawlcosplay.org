<?php

$url = 'https://dcss-stats.com/api/live-games';

$context = stream_context_create([
    'http' => [
        'timeout' => 20,
        'ignore_errors' => true,
    ],
]);

$response = @file_get_contents($url, false, $context);

header('Content-Type: text/plain; charset=utf-8');

if ($response === false) {
    echo "API request failed.\n";
    var_export(error_get_last());
    exit;
}

echo "HTTP response headers:\n";
print_r($http_response_header);

$data = json_decode($response, true);

echo "\nJSON valid: ";
echo json_last_error() === JSON_ERROR_NONE ? "yes\n" : "no\n";

if (isset($data['data']['games']) && is_array($data['data']['games'])) {
    echo "Live-game entries returned: " . count($data['data']['games']) . "\n";
    echo "Total live games reported: " . ($data['data']['total'] ?? 'unknown') . "\n";
} else {
    echo "Expected data.games array was not found.\n";
}
