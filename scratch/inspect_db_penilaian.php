<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Penilaian;
use App\Models\KerjaPraktik;

$all = Penilaian::all();
echo "Total Penilaian: " . $all->count() . "\n";
foreach ($all->take(10) as $p) {
    $kp = KerjaPraktik::find($p->id_kp);
    echo "KP ID: {$p->id_kp}, NIM: {$kp->nim}, Nilai Dosen: {$p->nilai_dosen_pembimbing}, Grade: {$p->grade}\n";
}

