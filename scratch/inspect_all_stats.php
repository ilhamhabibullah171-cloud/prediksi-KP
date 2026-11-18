<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\KerjaPraktik;
use App\Models\Logbook;
use App\Models\Penilaian;

echo "Dosen count: " . Dosen::count() . "\n";
foreach (Dosen::all() as $d) {
    $kpCount = KerjaPraktik::where('nidn', $d->nidn)->count();
    echo "  - NIDN: {$d->nidn} | {$d->nama_dosen} | Bimbingan: {$kpCount} mahasiswa\n";
}

echo "Mahasiswa count: " . Mahasiswa::count() . "\n";
echo "KerjaPraktik count: " . KerjaPraktik::count() . "\n";
echo "Logbook count: " . Logbook::count() . "\n";
echo "Penilaian count: " . Penilaian::count() . "\n";

