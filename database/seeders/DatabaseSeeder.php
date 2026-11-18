<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\KerjaPraktik;
use App\Models\Logbook;
use App\Models\Sertifikat;
use App\Models\Penilaian;
use App\Services\PredictionService;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        User::create([
            'username' => 'admin',
            'name' => 'Koordinator Kerja Praktik FT UIM',
            'email' => 'admin.kp@uim.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 2. Create Dosen Pembimbing
        $dosenList = [
            ['nidn' => '0715088201', 'nama' => 'Dr. H. Akhmad Fauzi, M.Kom.', 'kuota' => 10, 'jabatan' => 'Lektor Kepala'],
            ['nidn' => '0722038502', 'nama' => 'Zainal Abidin, M.T.', 'kuota' => 8, 'jabatan' => 'Lektor'],
            ['nidn' => '0711118903', 'nama' => 'Nurul Hidayati, M.Kom.', 'kuota' => 10, 'jabatan' => 'Asisten Ahli'],
            ['nidn' => '0704059004', 'nama' => 'Achmad Rofiq, S.Kom., M.Cs.', 'kuota' => 10, 'jabatan' => 'Lektor'],
            ['nidn' => '0718098705', 'nama' => 'Siti Robiatul, M.Kom.', 'kuota' => 8, 'jabatan' => 'Asisten Ahli'],
        ];

        foreach ($dosenList as $d) {
            $user = User::create([
                'username' => $d['nidn'],
                'name' => $d['nama'],
                'email' => strtolower(str_replace([' ', '.', ','], '', $d['nama'])) . '@uim.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
            ]);

            Dosen::create([
                'nidn' => $d['nidn'],
                'user_id' => $user->id,
                'nama_dosen' => $d['nama'],
                'kuota_bimbingan' => $d['kuota'],
                'jabatan_akademik' => $d['jabatan'],
                'no_hp' => '0812' . rand(10000000, 99999999),
            ]);
        }

        // 3. Create Mahasiswa with varying performance profiles
        $mhsData = [
            // Safe / High achiever
            ['nim' => '21063110001', 'nama' => 'Ahmad Muzakki', 'angkatan' => 2021, 'ipk' => 3.75, 'sks' => 125, 'profile' => 'safe'],
            ['nim' => '21063110002', 'nama' => 'Nurul Hamidi', 'angkatan' => 2021, 'ipk' => 3.60, 'sks' => 120, 'profile' => 'safe'],
            ['nim' => '21063110003', 'nama' => 'Fatimatus Zahra', 'angkatan' => 2021, 'ipk' => 3.82, 'sks' => 130, 'profile' => 'safe'],
            ['nim' => '21063110004', 'nama' => 'Moh. Ilham Pratama', 'angkatan' => 2021, 'ipk' => 3.45, 'sks' => 118, 'profile' => 'safe'],
            
            // At Risk / Rentan / Terlambat
            ['nim' => '21063110005', 'nama' => 'Syarif Hidayatullah', 'angkatan' => 2021, 'ipk' => 2.95, 'sks' => 102, 'profile' => 'warning'],
            ['nim' => '21063110006', 'nama' => 'Badrus Sholeh', 'angkatan' => 2021, 'ipk' => 2.85, 'sks' => 98, 'profile' => 'warning'],
            ['nim' => '21063110007', 'nama' => 'Lailatul Qomariyah', 'angkatan' => 2021, 'ipk' => 3.10, 'sks' => 106, 'profile' => 'warning'],
            
            // Critical / Bahaya / Gagal
            ['nim' => '21063110008', 'nama' => 'Dani Firmansyah', 'angkatan' => 2020, 'ipk' => 2.30, 'sks' => 82, 'profile' => 'danger'],
            ['nim' => '21063110009', 'nama' => 'Farid Hasyim', 'angkatan' => 2020, 'ipk' => 2.15, 'sks' => 78, 'profile' => 'danger'],
            
            // Newly Registered / Pengajuan
            ['nim' => '21063110010', 'nama' => 'Khoirul Anam', 'angkatan' => 2022, 'ipk' => 3.35, 'sks' => 95, 'profile' => 'new'],
        ];

        $companies = [
            ['name' => 'PT Telekomunikasi Indonesia (Telkom) Pamekasan', 'address' => 'Jl. Jokotole No. 45, Pamekasan'],
            ['name' => 'Dinas Komunikasi dan Informatika (Diskominfo) Kab. Pamekasan', 'address' => 'Jl. Kabupaten No. 107, Pamekasan'],
            ['name' => 'BPRS Bhakti Sumekar Pamekasan', 'address' => 'Jl. Trunojoyo No. 12, Pamekasan'],
            ['name' => 'Badan Pusat Statistik (BPS) Kab. Sampang', 'address' => 'Jl. Jaksa Agung Suprapto No. 34, Sampang'],
            ['name' => 'CV Madura Teknologi Kreatif', 'address' => 'Jl. Raya Tlanakan No. 88, Pamekasan'],
        ];

        $dosenNidns = ['0715088201', '0722038502', '0711118903', '0704059004', '0718098705'];
        $predictionService = app(PredictionService::class);

        foreach ($mhsData as $idx => $m) {
            $userMhs = User::create([
                'username' => $m['nim'],
                'name' => $m['nama'],
                'email' => $m['nim'] . '@student.uim.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
            ]);

            $mhs = Mahasiswa::create([
                'nim' => $m['nim'],
                'user_id' => $userMhs->id,
                'nama_lengkap' => $m['nama'],
                'angkatan' => $m['angkatan'],
                'ipk_terakhir' => $m['ipk'],
                'sks_lulus' => $m['sks'],
                'prodi' => 'Teknik Informatika',
                'no_hp' => '0878' . rand(10000000, 99999999),
            ]);

            $company = $companies[$idx % count($companies)];
            $assignedDosenNidn = $dosenNidns[$idx % count($dosenNidns)];

            if ($m['profile'] === 'new') {
                KerjaPraktik::create([
                    'nim' => $mhs->nim,
                    'nidn' => null, // Pending plotting
                    'instansi' => $company['name'],
                    'alamat_instansi' => $company['address'],
                    'tanggal_mulai' => Carbon::now()->addDays(7)->format('Y-m-d'),
                    'tanggal_selesai' => Carbon::now()->addDays(37)->format('Y-m-d'),
                    'status_kp' => 'Pengajuan',
                    'usulan_dosen' => 'Dr. H. Akhmad Fauzi, M.Kom.',
                    'catatan_koordinator' => 'Menunggu verifikasi syarat akademik & plotting pembimbing.',
                ]);
                continue;
            }

            // Dates: started 35 days ago, finished 5 days ago
            $startDate = Carbon::now()->subDays(35);
            $endDate = Carbon::now()->subDays(5);
            $statusKp = 'Berjalan';

            if ($m['profile'] === 'safe' && $idx === 0) {
                $statusKp = 'Selesai';
            }

            $kp = KerjaPraktik::create([
                'nim' => $mhs->nim,
                'nidn' => $assignedDosenNidn,
                'instansi' => $company['name'],
                'alamat_instansi' => $company['address'],
                'tanggal_mulai' => $startDate->format('Y-m-d'),
                'tanggal_selesai' => $endDate->format('Y-m-d'),
                'status_kp' => $statusKp,
                'usulan_dosen' => null,
                'catatan_koordinator' => 'Disetujui. Silakan laksanakan KP sesuai pedoman FT UIM.',
            ]);

            // Populate Logbooks based on profile
            if ($m['profile'] === 'safe') {
                // 28 - 30 days filled
                $totalDaysToFill = ($idx === 0) ? 30 : rand(28, 30);
                for ($d = 1; $d <= $totalDaysToFill; $d++) {
                    $dayDate = (clone $startDate)->addDays($d - 1);
                    Logbook::create([
                        'id_kp' => $kp->id_kp,
                        'hari_ke' => $d,
                        'tanggal_kegiatan' => $dayDate->format('Y-m-d'),
                        'deskripsi_pekerjaan' => "Melakukan analisis sistem, instalasi modul operasional, pendampingan user, dan pengujian fitur harian hari ke-{$d}.",
                        'dokumentasi' => null,
                        'status_validasi' => ($d <= 25) ? 'Approved' : 'Pending',
                        'catatan_dosen' => ($d <= 25) ? 'Laporan harian sangat baik, lanjutkan.' : null,
                        'waktu_submit' => (clone $dayDate)->setHour(17)->setMinute(rand(10, 50)),
                    ]);
                }

                // Upload certificate
                Sertifikat::create([
                    'id_kp' => $kp->id_kp,
                    'file_path' => 'sertifikat/dummy_cert.pdf',
                    'tanggal_upload' => (clone $endDate)->addDays(rand(1, 3))->format('Y-m-d'),
                    'status_verifikasi' => ($idx === 0) ? 'Valid' : 'Menunggu',
                    'catatan_admin' => ($idx === 0) ? 'Berkas lengkap dan memenuhi syarat ujian sidang KP.' : null,
                ]);

                // Completed assessment for idx 0
                if ($idx === 0) {
                    Penilaian::create([
                        'id_kp' => $kp->id_kp,
                        'nilai_pembimbing_instansi' => 88.0,
                        'nilai_dosen_pembimbing' => 87.5,
                        'nilai_disiplin' => 90.0,
                        'nilai_laporan' => 86.0,
                        'nilai_presentasi' => 88.0,
                        'nilai_akhir' => 87.75,
                        'grade' => 'A',
                        'catatan_evaluasi' => 'Kerja praktik terlaksana dengan sangat baik. Sikap dan kedisiplinan memuaskan.',
                    ]);
                }
            } elseif ($m['profile'] === 'warning') {
                // Warning profile: only filled 17-20 days, some missing days
                $daysToFill = rand(16, 20);
                for ($d = 1; $d <= $daysToFill; $d++) {
                    $dayDate = (clone $startDate)->addDays($d * 1.4); // irregular intervals
                    if ($d <= 30) {
                        Logbook::create([
                            'id_kp' => $kp->id_kp,
                            'hari_ke' => $d,
                            'tanggal_kegiatan' => $dayDate->format('Y-m-d'),
                            'deskripsi_pekerjaan' => "Melaksanakan tugas rutin operasional dan pembuatan dokumentasi modul hari ke-{$d}.",
                            'dokumentasi' => null,
                            'status_validasi' => ($d % 3 === 0) ? 'Revisi' : 'Approved',
                            'catatan_dosen' => ($d % 3 === 0) ? 'Deskripsi pekerjaan terlalu singkat, mohon lengkapi rincian teknis.' : 'Cukup baik.',
                            'waktu_submit' => (clone $dayDate)->addDays(rand(2, 5)), // submitted late
                        ]);
                    }
                }
                // No certificate yet or uploaded late
            } elseif ($m['profile'] === 'danger') {
                // Danger profile: only 5-7 days filled, massive missed days
                $daysToFill = rand(5, 7);
                for ($d = 1; $d <= $daysToFill; $d++) {
                    $dayDate = (clone $startDate)->addDays($d);
                    Logbook::create([
                        'id_kp' => $kp->id_kp,
                        'hari_ke' => $d,
                        'tanggal_kegiatan' => $dayDate->format('Y-m-d'),
                        'deskripsi_pekerjaan' => "Pengenalan lingkungan kerja hari ke-{$d}.",
                        'dokumentasi' => null,
                        'status_validasi' => 'Pending',
                        'catatan_dosen' => 'Harap segera mengejar ketertinggalan logbook harian!',
                        'waktu_submit' => (clone $dayDate)->addDays(rand(7, 14)), // submitted very late
                    ]);
                }
            }

            // Run Prediction immediately so records have EWS ML results
            $predictionService->predict($kp);
        }
    }
}
