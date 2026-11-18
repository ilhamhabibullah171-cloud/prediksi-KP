<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\KerjaPraktik;
use App\Models\Penilaian;
use App\Models\Logbook;
use App\Services\SpreadsheetService;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard Admin Fakultas Teknik:
     * Menampilkan data mahasiswa KP yang diupload melalui template (Nama, NIM, Prodi, Nilai Dosen)
     */
    public function dashboard()
    {
        // Data Mahasiswa KP berserta Penilaian & Dosen
        $kpList = KerjaPraktik::with(['mahasiswa', 'dosen', 'penilaian'])
            ->latest('id_kp')
            ->get();

        $dosenList = Dosen::withCount('kerjaPraktik')->orderBy('nama_dosen')->get();

        $totalMahasiswa = Mahasiswa::count();
        $totalTI = Mahasiswa::where('prodi', 'Teknik Informatika')->count();
        $totalSI = Mahasiswa::where('prodi', 'Sistem Informasi')->count();
        $totalDitetapkan = KerjaPraktik::whereNotNull('nidn')->where('status_kp', '!=', 'Pengajuan')->count();
        $totalBelumDitetapkan = KerjaPraktik::where(function($q) {
            $q->whereNull('nidn')->orWhere('status_kp', 'Pengajuan');
        })->count();
        $totalDinilai = Penilaian::where(function($q) {
            $q->whereNotNull('nilai_akhir')->orWhereNotNull('nilai_dosen_pembimbing');
        })->count();
        $rataRataNilai = $totalDinilai > 0 ? round(Penilaian::whereNotNull('nilai_akhir')->avg('nilai_akhir') ?: Penilaian::whereNotNull('nilai_dosen_pembimbing')->avg('nilai_dosen_pembimbing'), 1) : 0;
        $jumlahLulus = Penilaian::whereIn('grade', ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'])->count();

        return view('admin.dashboard', compact(
            'kpList',
            'dosenList',
            'totalMahasiswa',
            'totalTI',
            'totalSI',
            'totalDitetapkan',
            'totalBelumDitetapkan',
            'totalDinilai',
            'rataRataNilai',
            'jumlahLulus'
        ));
    }

    /**
     * Admin Menetapkan / Mengedit Tempat & Jadwal Kerja Praktik Mahasiswa
     * serta Menentukan Dosen Pembimbing KP
     */
    public function updateInstansi(Request $request, $id_kp)
    {
        $kp = KerjaPraktik::with(['mahasiswa', 'dosen'])->findOrFail($id_kp);

        $validated = $request->validate([
            'instansi' => 'required|string|max:200',
            'alamat_instansi' => 'nullable|string|max:255',
            'judul_kp' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'nidn' => 'required|string|exists:dosen,nidn',
            'status_kp' => 'nullable|in:Pengajuan,Berjalan,Selesai,Batal',
            'catatan' => 'nullable|string|max:500',
        ], [
            'instansi.required' => 'Nama instansi/perusahaan tempat KP wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai pelaksanaan KP wajib diisi.',
            'nidn.required' => 'Dosen Pembimbing wajib ditentukan oleh Admin Fakultas Teknik.',
            'nidn.exists' => 'Dosen Pembimbing yang dipilih tidak terdaftar di sistem.',
        ]);

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = !empty($validated['tanggal_selesai'])
            ? Carbon::parse($validated['tanggal_selesai'])
            : (clone $tanggalMulai)->addDays(30);

        $dosen = Dosen::where('nidn', $validated['nidn'])->first();

        $kp->update([
            'instansi' => $validated['instansi'],
            'alamat_instansi' => $validated['alamat_instansi'] ?? ($kp->alamat_instansi ?? 'Pamekasan, Madura'),
            'judul_kp' => $validated['judul_kp'] ?? $kp->judul_kp,
            'tanggal_mulai' => $tanggalMulai->toDateString(),
            'tanggal_selesai' => $tanggalSelesai->toDateString(),
            'nidn' => $validated['nidn'],
            'status_kp' => $validated['status_kp'] ?? 'Berjalan',
            'catatan_koordinator' => $validated['catatan'] ?? ("Dosen Pembimbing ({$dosen->nama_dosen}) dan jadwal pelaksanaan telah resmi ditetapkan oleh Admin Fakultas Teknik."),
        ]);

        $namaMahasiswa = $kp->mahasiswa->nama_lengkap ?? "NIM {$kp->nim}";
        return back()->with('success', "Dosen Pembimbing ({$dosen->nama_dosen}) untuk {$namaMahasiswa} berhasil ditetapkan di {$validated['instansi']} ({$tanggalMulai->format('d/m/Y')} s/d {$tanggalSelesai->format('d/m/Y')}). Status KP sekarang aktif ('Berjalan').");
    }

    /**
     * Unduh Template File XLSX Resmi:
     * Berisikan kolom: NIM, NAMA, PRODI, dan NILAI_DOSEN_PEMBIMBING
     */
    public function downloadTemplateXlsx(SpreadsheetService $spreadsheetService)
    {
        $filePath = storage_path('app/template_mahasiswa_kp_ft_uim.xlsx');
        if (!file_exists($filePath)) {
            $spreadsheetService->generateTemplate($filePath);
        }

        return response()->download($filePath, 'Template_Mahasiswa_KP_FT_UIM.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Unduh File Data 104 Mahasiswa KP (XLSX)
     */
    public function downloadData104Xlsx(SpreadsheetService $spreadsheetService)
    {
        $filePath = storage_path('app/data_mahasiswa_kp_ft_uim_104.xlsx');
        if (!file_exists($filePath)) {
            return $this->downloadTemplateXlsx($spreadsheetService);
        }

        return response()->download($filePath, 'Data_104_Mahasiswa_KP_FT_UIM.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Unggah / Import File Template XLSX atau CSV Data Mahasiswa KP
     * (Nama, NIM, Prodi, dan Nilai dari Dosen Pembimbing)
     */
    public function importXlsx(Request $request, SpreadsheetService $spreadsheetService)
    {
        $request->validate([
            'file_xlsx' => 'required|file|max:10240',
        ]);

        $file = $request->file('file_xlsx');
        $extension = $file->getClientOriginalExtension();
        $realPath = $file->getRealPath();

        $records = $spreadsheetService->parseFile($realPath, $extension);

        if (empty($records)) {
            return back()->with('error', 'File yang diunggah kosong atau format kolom tidak sesuai dengan template (NIM, NAMA, PRODI, NILAI_DOSEN_PEMBIMBING).');
        }

        $dosenDefault = Dosen::first();
        $nidnDefault = $dosenDefault ? $dosenDefault->nidn : '0715088201';

        $imported = 0;
        $updated = 0;
        $defaultPasswordHash = Hash::make('password123');

        DB::transaction(function () use ($records, $nidnDefault, $defaultPasswordHash, &$imported, &$updated) {
            foreach ($records as $r) {
                $nim = trim($r['nim']);
                $nama = trim($r['nama']);
                $prodi = trim($r['prodi']) ?: 'Teknik Informatika';
                $nilaiDosen = (float) $r['nilai_dosen_pembimbing'];

                // Ekstrak angkatan dari 4 atau 2 digit awal NIM
                $prefix4 = substr($nim, 0, 4);
                if (is_numeric($prefix4) && (int)$prefix4 >= 2018 && (int)$prefix4 <= 2026) {
                    $angkatan = (int)$prefix4;
                } else {
                    $prefix2 = substr($nim, 0, 2);
                    $angkatan = (is_numeric($prefix2) && (int)$prefix2 >= 18 && (int)$prefix2 <= 30)
                        ? (2000 + (int)$prefix2)
                        : 2023;
                }

                // 1. Akun Login Mahasiswa (Username: NIM, Password: password123)
                $user = User::where('username', $nim)->first();
                if (!$user) {
                    $user = User::create([
                        'username' => $nim,
                        'name' => $nama,
                        'email' => $nim . '@student.uim.ac.id',
                        'password' => $defaultPasswordHash,
                        'role' => 'mahasiswa',
                    ]);
                } else {
                    $user->update([
                        'name' => $nama,
                        'role' => 'mahasiswa',
                    ]);
                }

                // 2. Data Profil Mahasiswa
                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'user_id' => $user->id,
                        'nama_lengkap' => $nama,
                        'prodi' => $prodi,
                        'angkatan' => $angkatan,
                        'ipk_terakhir' => $nilaiDosen >= 80 ? 3.65 : ($nilaiDosen >= 70 ? 3.30 : 2.85),
                        'sks_lulus' => 105,
                    ]
                );

                // 3. Data Transaksi Kerja Praktik
                $kp = KerjaPraktik::where('nim', $nim)->latest('id_kp')->first();

                if (!$kp) {
                    $kp = KerjaPraktik::create([
                        'nim' => $nim,
                        'nidn' => $nidnDefault,
                        'instansi' => null,
                        'alamat_instansi' => null,
                        'tanggal_mulai' => null,
                        'tanggal_selesai' => null,
                        'status_kp' => 'Pengajuan',
                        'catatan_koordinator' => 'Menunggu penetapan tempat dan jadwal Kerja Praktik oleh Admin Fakultas Teknik.',
                    ]);
                    $imported++;
                } else {
                    $updated++;
                }

                // 4. Data Nilai dari Dosen Pembimbing (jika ada nilai > 0)
                if ($nilaiDosen > 0) {
                    $grade = Penilaian::determineGrade($nilaiDosen);
                    Penilaian::updateOrCreate(
                        ['id_kp' => $kp->id_kp],
                        [
                            'nilai_dosen_pembimbing' => $nilaiDosen,
                            'nilai_pembimbing_instansi' => $nilaiDosen,
                            'nilai_disiplin' => $nilaiDosen,
                            'nilai_laporan' => $nilaiDosen,
                            'nilai_presentasi' => $nilaiDosen,
                            'nilai_akhir' => $nilaiDosen,
                            'grade' => $grade,
                            'catatan_evaluasi' => 'Nilai diunggah oleh Admin Fakultas Teknik dari template resmi dosen pembimbing.',
                        ]
                    );
                }
            }
        });

        $total = $imported + $updated;
        return back()->with('success', "Berhasil memproses {$total} data mahasiswa KP ({$imported} baru, {$updated} diperbarui) dari file {$file->getClientOriginalName()}.");
    }

    /**
     * Hapus Data Mahasiswa KP
     */
    public function deleteMahasiswaKp($id_kp)
    {
        $kp = KerjaPraktik::findOrFail($id_kp);
        $nama = $kp->mahasiswa->nama_lengkap ?? $kp->nim;
        $kp->delete();

        return back()->with('success', "Data mahasiswa KP {$nama} berhasil dihapus dari sistem.");
    }
}
