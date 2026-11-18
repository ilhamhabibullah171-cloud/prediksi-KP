<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\KerjaPraktik;
use App\Models\Logbook;
use App\Models\Sertifikat;
use App\Models\Penilaian;
use App\Models\HasilPrediksiMl;
use App\Services\PredictionService;
use App\Services\SpreadsheetService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== MEMULAI IMPORT DATA MAHASISWA DARI PENGGUNA ===\n\n";

$rawCsv = <<<'CSV'
1,Moh. Hasan,2023020100066,TEKNIK INFORMATIKA,90.2,A
2,Faruq,2023020100068,TEKNIK INFORMATIKA,90.2,A
3,Ach. Taufik,2023020100010,TEKNIK INFORMATIKA,90.2,A
4,Fauzan Wildany,2023020100103,TEKNIK INFORMATIKA,94.8,A
5,Moh. Ali Ghufron,2023020100107,TEKNIK INFORMATIKA,94.8,A
6,Moh. Jufri,2023020100012,TEKNIK INFORMATIKA,94.8,A
7,Uswatun hasanah,2023020100024,TEKNIK INFORMATIKA,93.83,A
8,Ilham maulana,2023020200006,SISTEM INFORMASI,93.83,A
9,Suryani amilia putri,2023020100105,TEKNIK INFORMATIKA,93.83,A
10,HERMANTO,2023020100104,TEKNIK INFORMATIKA,83.26,B+
11,AMIR ROZY,2023020100072,TEKNIK INFORMATIKA,83.26,B+
12,AHMAD DAHLAN,2023020100023,TEKNIK INFORMATIKA,54.3,D
13,MOH SAIIIH,202«020200016,SISľEM INIORMASI,51.96,D
14,LAILY IAľHAľIN NIKMAH,202«02010010®,ľEKNIK INIORMAľIKA,51.96,D
15,ADI PRANANľA SEľIAWAN,202«020100100,ľEKNIK INIORMAľIKA,51.66,D
16,Elsa Belqis rafita,2023020100003,TEKNIK INFORMATIKA,94.55,A
17,Maftahatul Khair,2023020100098,TEKNIK INFORMATIKA,94.55,A
18,Desy mamluah himaya,2023020100017,TEKNIK INFORMATIKA,65.2,C+
19,Faridatul jannah,2023020100045,TEKNIK INFORMATIKA,64.6,C
20,Mok Roriqi,202«020100102,TEKNIK INFORMATIKA,0,E
21,Ackmad Jaila⭲i,202«0201000®6,TEKNIK INFORMATIKA,0,E
22,Yunita Rosadiya,2023020100027,TEKNIK INFORMATIKA,66.89,C+
23,Nurul Hijroh,2023020100079,TEKNIK INFORMATIKA,67.46,C+
24,Effatul Azizah,2023020100046,TEKNIK INFORMATIKA,66.8,C+
25,ACH ubaidillah,2021020100125,TEKNIK INFORMATIKA,69.75,C+
26,Rian Achmad Hidayatullah,2023020100083,TEKNIK INFORMATIKA,69.9,C+
27,Alful Hilal,2023020100081,TEKNIK INFORMATIKA,70.05,B-
28,Siatun,2023020200030,SISTEM INFORMASI,91.5,A
29,Nur Hasanah Sariatun,2023020200014,SISTEM INFORMASI,91.5,A
30,Riska Amalia,2023020200004,SISTEM INFORMASI,91.8,A
31,Nasrullah,2023020100090,TEKNIK INFORMATIKA,46.65,D
32,Moh.Farhan,2023020100097,TEKNIK INFORMATIKA,82.35,B+
33,ADDIS ROFIK ERLANGGA,2023020100032,TEKNIK INFORMATIKA,94.11,A
34,KHOLILUR ROHMAN,2023020100039,TEKNIK INFORMATIKA,94.11,A
35,ACHMAD SAFARI ASSYDDYQQI,2023020100036,TEKNIK INFORMATIKA,94.11,A
36,Mokammad Rizki sctiawa⭲,202«020100101,ľEKNIK INIORMAľIKA,57.57,D
37,Habíi Jarra⭲ M""kaimi⭲,202«0201000®®,ľEKNIK INIORMAľIKA,57.69,D
38,Nabil Qistubillah,2023020100060,TEKNIK INFORMATIKA,94.95,A
39,Fitri Aulia,2023020100062,TEKNIK INFORMATIKA,94.95,A
40,Aisah,2023020100070,TEKNIK INFORMATIKA,92.24,A
41,Fina Nurul Layali,2023020100033,TEKNIK INFORMATIKA,90.77,A
42,Maftuhah,2023020100015,TEKNIK INFORMATIKA,92.24,A
43,SYAIIUR ROHMAN,202«02020000«,SISľEM INIORMASI,29.43,E
44,ABDI MAULANA HOSIN,202«020200015,SISľEM INIORMASI,61.43,C
45,IMRON ZAINULLAH,202«020100094,ľEKNIK INIORMAľIKA,61.43,C
46,Aisyatur Rodiyah,2023020100051,TEKNIK INFORMATIKA,95.52,A+
47,Khoirin Nisya,2023020100016,TEKNIK INFORMATIKA,95.52,A+
48,Ella Ainurriska,2023020100014,TEKNIK INFORMATIKA,95.52,A+
49,Makdum ibrohim,2023020100037,TEKNIK INFORMATIKA,90.2,A
50,Muhammad Yahya abdullahissala,2023020100025,TEKNIK INFORMATIKA,90.2,A
51,Ismail,202202010010®,ľEKNIK INIORMAľIKA,55.32,D
52,Erc⭲di,20220201000«1,ľEKNIK INIORMAľIKA,79.92,B
53,Akmad Ya⭲i,202«02010007«,ľEKNIK INIORMAľIKA,56.52,D
54,MOH.IMAM KUSAIRI,2023020100021,TEKNIK INFORMATIKA,88.78,A-
55,Suterisno Supriyanto,2023020100053,TEKNIK INFORMATIKA,88.48,A-
56,SULAIMAN,2023020100043,TEKNIK INFORMATIKA,88.66,A-
57,ifadatul mukarromah,2023020100112,TEKNIK INFORMATIKA,94.6,A
58,faradila,2023020100050,TEKNIK INFORMATIKA,94.6,A
59,willi ariyanto,2023020100047,TEKNIK INFORMATIKA,94.3,A
60,DWI AYU WINARNI,2023020200005,SISTEM INFORMASI,93.56,A
61,MOH ľAUIIK,202«020200029,SISľEM INIORMASI,87.55,A-
62,Mahmud Riady,2023020200018,SISTEM INFORMASI,92.82,A
63,Abd Qarib,2023020200025,SISTEM INFORMASI,92.82,A
64,Rifki Amin,2023020200028,SISTEM INFORMASI,92.82,A
65,Ilkam Bay,202«020100092,ľEKNIK INIORMAľIKA,86.86,A-
66,Hclmi Aídabili,202«020100054,ľEKNIK INIORMAľIKA,87.58,A-
67,Nafis Skoral Hidayak,202«02010000®,ľEKNIK INIORMAľIKA,87.58,A-
68,MOH. AHNAF AR ROZIQI,2023020100020,TEKNIK INFORMATIKA,88.63,A-
69,M. ZAINAL ABIDIN,2023020100064,TEKNIK INFORMATIKA,88.45,A-
70,NUVI SUSANTI,20251223124,TEKNIK INFORMATIKA,93.35,A
71,ACH. NASRULLAH,20251223123,TEKNIK INFORMATIKA,93.35,A
72,Dimas Bagas Firmansyah Arifin,2023020100011,TEKNIK INFORMATIKA,95,A+
73,Muslimah Qurniawati,2023020100048,TEKNIK INFORMATIKA,95,A+
74,Tamara Adjuah,2023020100007,TEKNIK INFORMATIKA,95,A+
75,Moh Roihan Efendi,2023020100106,TEKNIK INFORMATIKA,92.69,A
76,Mutawakkiel Rahmatillah,2023020100028,TEKNIK INFORMATIKA,92.69,A
77,Ach Deny Ghasmal Fuadi,2023020100029,TEKNIK INFORMATIKA,92.69,A
78,Indriana Agustin,2023020100076,TEKNIK INFORMATIKA,89.32,A-
79,Siti Afizah,2023020100071,TEKNIK INFORMATIKA,89.32,A-
80,Wuli Diana Fitriyah,2023020100077,TEKNIK INFORMATIKA,89.32,A-
81,Gila⭲g skc:a íamadka⭲,202«02010001«,ľEKNIK INIORMAľIKA,57,D
82,Zainul bakri,202«0201000®0,ľEKNIK INIORMAľIKA,89.32,A-
83,Ramadani,2023020100057,TEKNIK INFORMATIKA,87.82,A-
84,ACH Dani,2023020100074,TEKNIK INFORMATIKA,87.82,A-
85,Naufal Arkan Hadi Putra,2023020100067,TEKNIK INFORMATIKA,87.82,A-
86,maulidia,2023020200027,SISTEM INFORMASI,91.3,A
87,agustina maputri,2023020200032,SISTEM INFORMASI,91.3,A
88,viona dewi royati,2023020200019,SISTEM INFORMASI,91.45,A
89,PANJI DEFA PRATAMA,2023020100030,TEKNIK INFORMATIKA,94.28,A
90,AYDI SOFYAN,2023020100084,TEKNIK INFORMATIKA,94.76,A
91,Suradi,2023020100031,TEKNIK INFORMATIKA,59.69,D
92,Suci Nurul Hidayati,2023020200020,SISTEM INFORMASI,60.89,C
93,Indah Sari Maghfiroh,2023020200002,SISTEM INFORMASI,60.89,C
94,Wildan Ramadhani,2023020200021,SISTEM INFORMASI,29.25,E
95,NURUL HAMIDI,2023020200033,SISTEM INFORMASI,61.76,C
96,AKHMAD FAUZAN BASORI,2023020200012,SISTEM INFORMASI,61.43,C
97,Moh. Aqilul kamal,2022020100006,TEKNIK INFORMATIKA,59.15,D
98,Lukman hakim,2023020200001,SISTEM INFORMASI,58.85,D
99,M Erico Febriyan,2023020100055,TEKNIK INFORMATIKA,64.35,C
100,Ferdiansyah,2023020100018,TEKNIK INFORMATIKA,63.66,C
101,Muhammad Farhan Amien,2023020100042,TEKNIK INFORMATIKA,63.6,C
102,Imam syafi'i,2023020100034,TEKNIK INFORMATIKA,59.95,D
103,Badrus salam,2023020200011,SISTEM INFORMASI,59.05,D
104,Ridoi,2023020200010,SISTEM INFORMASI,60.1,C
CSV;

// Parse data
$lines = explode("\n", trim($rawCsv));
$students = [];

$nameCleanMap = [
    'Moh Saiiih' => 'Moh. Salih',
    'Laily Iathatin Nikmah' => 'Laily Fathatin Nikmah',
    'Mok Roriqi' => 'Moh. Rofiqi',
    'Ackmad Jailani' => 'Achmad Jailani',
    'Mokammad Rizki Sctiawan' => 'Mohammad Rizki Setiawan',
    'Habii Jarran Mkaimin' => 'Habibi Jarwan Mukaimin',
    'Syaiiur Rohman' => 'Syaifur Rohman',
    'Ercndi' => 'Efendi',
    'Akmad Yani' => 'Akhmad Yani',
    'Moh Tauiik' => 'Moh. Taufik',
    'Ilkam Bay' => 'Ilham Bayhaqi',
    'Hclmi Aidabili' => 'Helmi Afdabili',
    'Nafis Skoral Hidayak' => 'Nafis Shorotul Hidayat',
    'Gilang Skc:a Iamadkan' => 'Gilang Setya Ramadhan',
    'Zainul Bakri' => 'Zainul Bahri',
];

foreach ($lines as $line) {
    $p = str_getcsv(trim($line));
    if (count($p) < 5) continue;

    $nimRaw = trim($p[2]);
    $nim = preg_replace('/[^0-9]/', '', str_replace(['«', '®'], ['3', '8'], $nimRaw));

    $namaRaw = trim($p[1], " \t\n\r\0\x0B\"");
    $namaRaw = str_replace(['ľ', '⭲', 'í', '®', '«', '""', '"'], ['t', 'n', 'i', '', '', '', ''], $namaRaw);
    $namaRaw = preg_replace('/\s+/', ' ', $namaRaw);
    $nama = ucwords(strtolower(trim($namaRaw)));
    if (isset($nameCleanMap[$nama])) {
        $nama = $nameCleanMap[$nama];
    }

    $prodiRaw = strtoupper(trim($p[3]));
    $prodi = (str_contains($prodiRaw, 'SISTEM') || str_contains($prodiRaw, 'SIS') || str_contains($prodiRaw, 'INIORMASI'))
        ? 'Sistem Informasi'
        : 'Teknik Informatika';

    $nilai = (float) $p[4];
    $grade = isset($p[5]) ? trim($p[5]) : Penilaian::determineGrade($nilai);

    $students[] = [
        'nim' => $nim,
        'nama' => $nama,
        'prodi' => $prodi,
        'nilai' => $nilai,
        'grade' => $grade,
    ];
}

echo "Total data parsed: " . count($students) . "\n\n";

// 1. HAPUS DATA LAMA DARI SISTEM (Sesuai instruksi pengguna)
echo "1. Menghapus data lama dari sistem...\n";

// Hapus relasi mahasiswa
HasilPrediksiMl::query()->delete();
Penilaian::query()->delete();
Logbook::query()->delete();
Sertifikat::query()->delete();
KerjaPraktik::query()->delete();
Mahasiswa::query()->delete();

// Hapus akun user ber-role 'mahasiswa'
User::where('role', 'mahasiswa')->delete();

echo "   -> Data mahasiswa, KP, logbook, nilai, dan prediksi lama berhasil dibersihkan.\n";

// 2. PASTIKAN AKUN ADMIN DAN DOSEN TETAP ADA
echo "2. Memverifikasi akun Admin & Dosen...\n";

$adminUser = User::firstOrCreate(
    ['username' => 'admin'],
    [
        'name' => 'Admin Fakultas Teknik UIM',
        'email' => 'admin.ft@uim.ac.id',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
    ]
);

$dosenUser = User::firstOrCreate(
    ['username' => '0715088201'],
    [
        'name' => 'Dr. H. Akhmad Fauzi, M.Kom.',
        'email' => 'akhmad.fauzi@uim.ac.id',
        'password' => Hash::make('password123'),
        'role' => 'dosen',
    ]
);

$dosen = Dosen::firstOrCreate(
    ['nidn' => '0715088201'],
    [
        'user_id' => $dosenUser->id,
        'nama_dosen' => 'Dr. H. Akhmad Fauzi, M.Kom.',
        'bidang_keahlian' => 'Kecerdasan Buatan & Rekayasa Perangkat Lunak',
        'kuota_bimbingan' => 150,
    ]
);

echo "   -> Admin: {$adminUser->username} (role: {$adminUser->role})\n";
echo "   -> Dosen: {$dosen->nama_dosen} (NIDN: {$dosen->nidn})\n";

// 3. MASUKKAN 104 DATA MAHASISWA & NILAINYA
echo "3. Memasukkan 104 data mahasiswa KP & nilainya...\n";

$predictionService = new PredictionService();
$insertedCount = 0;
$xlsxRows = [];

foreach ($students as $s) {
    $nim = $s['nim'];
    $nama = $s['nama'];
    $prodi = $s['prodi'];
    $nilai = $s['nilai'];
    $grade = $s['grade'];

    // Baris untuk template file XLSX
    $xlsxRows[] = [
        $nim,
        $nama,
        $prodi,
        (string) $nilai,
    ];

    // Angkatan dari NIM
    $prefix = substr($nim, 0, 4);
    $angkatan = (is_numeric($prefix) && (int)$prefix >= 2018 && (int)$prefix <= 2026) ? (int)$prefix : 2023;

    // IPK proporsional dari nilai
    if ($nilai >= 85) {
        $ipk = round(3.60 + (($nilai - 85) / 15) * 0.40, 2);
    } elseif ($nilai >= 70) {
        $ipk = round(3.00 + (($nilai - 70) / 15) * 0.55, 2);
    } elseif ($nilai >= 55) {
        $ipk = round(2.50 + (($nilai - 55) / 15) * 0.45, 2);
    } else {
        $ipk = round(max(1.80, 2.00 + ($nilai / 55) * 0.45), 2);
    }

    // A. Akun User Mahasiswa
    $user = User::create([
        'username' => $nim,
        'name' => $nama,
        'email' => $nim . '@student.uim.ac.id',
        'password' => Hash::make('password123'),
        'role' => 'mahasiswa',
    ]);

    // B. Profil Mahasiswa
    $mhs = Mahasiswa::create([
        'nim' => $nim,
        'user_id' => $user->id,
        'nama_lengkap' => $nama,
        'prodi' => $prodi,
        'angkatan' => $angkatan,
        'ipk_terakhir' => $ipk,
        'sks_lulus' => 105,
    ]);

    // C. Data Kerja Praktik (Status: Selesai)
    $startDate = Carbon::today()->subDays(30);
    $endDate = Carbon::today();

    $instansi = ($prodi === 'Sistem Informasi')
        ? 'Dinas Komunikasi dan Informatika (Diskominfo)'
        : 'PT Teknologi Digital Nusantara';

    $kp = KerjaPraktik::create([
        'nim' => $nim,
        'nidn' => $dosen->nidn,
        'instansi' => $instansi,
        'alamat_instansi' => 'Pamekasan, Madura, Jawa Timur',
        'tanggal_mulai' => $startDate->toDateString(),
        'tanggal_selesai' => $endDate->toDateString(),
        'status_kp' => 'Selesai',
        'catatan_koordinator' => 'Data mahasiswa KP diunggah oleh Admin Fakultas Teknik.',
    ]);

    // D. Penilaian KP dari Dosen Pembimbing
    Penilaian::create([
        'id_kp' => $kp->id_kp,
        'nilai_dosen_pembimbing' => $nilai,
        'nilai_pembimbing_instansi' => $nilai,
        'nilai_disiplin' => $nilai,
        'nilai_laporan' => $nilai,
        'nilai_presentasi' => $nilai,
        'nilai_akhir' => $nilai,
        'grade' => $grade,
        'catatan_evaluasi' => 'Penilaian KP resmi Fakultas Teknik UIM Tahun Akademik 2025/2026.',
    ]);

    // E. Prediksi EWS ML
    $predictionService->predict($kp);

    $insertedCount++;
}

echo "   -> Sukses memasukkan {$insertedCount} data mahasiswa beserta nilai dan prediksinya!\n\n";

// 4. BUAT TEMPLATE FILE XLSX RESMI DENGAN 4 KOLOM
echo "4. Membuat template file XLSX resmi (NIM, NAMA, PRODI, NILAI_DOSEN_PEMBIMBING)...\n";
$spreadsheetService = new SpreadsheetService();

// A. File Template Resmi Kosong/Contoh
$templatePath = storage_path('app/template_mahasiswa_kp_ft_uim.xlsx');
$spreadsheetService->generateTemplate($templatePath, array_slice($xlsxRows, 0, 5));
echo "   -> File template disimpan di: {$templatePath}\n";

// B. File Template Berisi 104 Data Lengkap
$fullDataPath = storage_path('app/data_mahasiswa_kp_ft_uim_104.xlsx');
$spreadsheetService->createXlsx($fullDataPath, SpreadsheetService::HEADERS, $xlsxRows);
echo "   -> File data 104 mahasiswa disimpan di: {$fullDataPath}\n\n";

echo "=== SELESAI: SEMUA DATA TELAH DISESUAIKAN DENGAN FILE PENGGUNA ===\n";

