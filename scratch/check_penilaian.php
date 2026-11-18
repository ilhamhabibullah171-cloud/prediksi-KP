<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pen = App\Models\Penilaian::take(5)->get();
foreach ($pen as $p) {
    echo "ID: {$p->id_nilai}, KP: {$p->id_kp}, Nilai Dosen: '{$p->nilai_dosen_pembimbing}', Akhir: '{$p->nilai_akhir}', Grade: '{$p->grade}'\n";
}

