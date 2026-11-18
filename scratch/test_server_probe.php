<?php

$serverCmd = 'php artisan serve --host=127.0.0.1 --port=8088';
$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];

$proc = proc_open($serverCmd, $descriptors, $pipes, __DIR__ . '/../');

usleep(1500000); // 1.5 seconds

$ctx = stream_context_create([
    'http' => [
        'timeout' => 3,
        'ignore_errors' => true,
    ]
]);

$start = microtime(true);
$content = @file_get_contents('http://127.0.0.1:8088/login', false, $ctx);
$duration = round((microtime(true) - $start) * 1000, 2);

echo "HTTP Response time: {$duration} ms\n";
echo "Content length: " . strlen($content) . " bytes\n";
if (str_contains($content, 'Masuk ke Portal')) {
    echo "SUCCESS: Login page rendered cleanly!\n";
} else {
    echo "FAILED: Could not load login page\n";
}

// Check headers
if (isset($http_response_header)) {
    echo "Headers:\n" . implode("\n", array_slice($http_response_header, 0, 5)) . "\n";
}

proc_terminate($proc);

