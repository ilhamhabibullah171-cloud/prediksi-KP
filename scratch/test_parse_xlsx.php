<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\SpreadsheetService;

$service = new SpreadsheetService();
$records = $service->parseFile(storage_path('app/data_mahasiswa_kp_ft_uim_104.xlsx'));
echo "Parsed " . count($records) . " records from data_mahasiswa_kp_ft_uim_104.xlsx:\n";
foreach (array_slice($records, 0, 5) as $r) {
    print_r($r);
}

