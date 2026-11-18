<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KerjaPraktik;

$allKp = KerjaPraktik::all();
echo "Total KP: " . $allKp->count() . "\n";
foreach ($allKp->take(10) as $kp) {
    echo "ID: {$kp->id_kp}, NIM: {$kp->nim}, Status: {$kp->status_kp}\n";
}

