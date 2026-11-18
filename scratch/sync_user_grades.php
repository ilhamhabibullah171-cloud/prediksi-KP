<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KerjaPraktik;
use App\Models\Penilaian;
use App\Services\SpreadsheetService;

$service = new SpreadsheetService();
$records = $service->parseFile(storage_path('app/data_mahasiswa_kp_ft_uim_104.xlsx'));

echo "Syncing " . count($records) . " student grades...\n";

$updated = 0;
foreach ($records as $r) {
    $nim = $r['nim'];
    $nilai = (float) $r['nilai_dosen_pembimbing'];
    $grade = Penilaian::determineGrade($nilai);

    $kp = KerjaPraktik::where('nim', $nim)->first();
    if ($kp) {
        Penilaian::updateOrCreate(
            ['id_kp' => $kp->id_kp],
            [
                'nilai_dosen_pembimbing' => $nilai,
                'nilai_pembimbing_instansi' => $nilai,
                'nilai_disiplin' => $nilai,
                'nilai_laporan' => $nilai,
                'nilai_presentasi' => $nilai,
                'nilai_akhir' => $nilai,
                'grade' => $grade,
                'catatan_evaluasi' => 'Penilaian KP resmi Fakultas Teknik UIM.',
            ]
        );
        $updated++;
    }
}

echo "Successfully synced {$updated} student grades!\n";

