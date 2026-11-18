<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\KerjaPraktik;
use App\Models\Logbook;
use App\Models\Penilaian;
use App\Models\Dosen;
use Carbon\Carbon;

class DosenController extends Controller
{
    /**
     * Dashboard Dosen Pembimbing: Menampilkan Ringkasan 4 Fitur Inti
     */
    public function dashboard()
    {
        $dosen = Auth::user()->dosen;
        if (!$dosen) {
            return view('dosen.no_profile');
        }

        $bimbinganList = KerjaPraktik::with(['mahasiswa', 'logbooks', 'penilaian'])
            ->where('nidn', $dosen->nidn)
            ->orderByRaw("CASE WHEN status_kp = 'Pengajuan' THEN 1 WHEN status_kp = 'Berjalan' THEN 2 ELSE 3 END")
            ->latest('id_kp')
            ->get();

        // 1. Pendaftaran count
        $pengajuanCount = $bimbinganList->where('status_kp', 'Pengajuan')->count();

        // 2. Logbook pending count
        $pendingLogbookCount = Logbook::whereHas('kerjaPraktik', function ($q) use ($dosen) {
            $q->where('nidn', $dosen->nidn);
        })->where('status_validasi', 'Pending')->count();

        // 3. Penilaian count
        $belumDinilaiCount = $bimbinganList->where('status_kp', 'Berjalan')->whereNull('penilaian')->count();

        // 4. Sertifikat siap cetak
        $siapSertifikatCount = $bimbinganList->filter(fn($kp) => $kp->penilaian !== null || $kp->status_kp === 'Selesai')->count();

        return view('dosen.dashboard', compact(
            'dosen', 'bimbinganList', 'pengajuanCount', 'pendingLogbookCount', 'belumDinilaiCount', 'siapSertifikatCount'
        ));
    }

    /**
     * Fitur 1: Form & Data Pendaftaran Mahasiswa KP
     */
    public function pendaftaranIndex()
    {
        $dosen = Auth::user()->dosen;
        if (!$dosen) {
            return view('dosen.no_profile');
        }

        $pendaftaranList = KerjaPraktik::with(['mahasiswa'])
            ->where('nidn', $dosen->nidn)
            ->orderByRaw("CASE WHEN status_kp = 'Pengajuan' THEN 1 WHEN status_kp = 'Berjalan' THEN 2 ELSE 3 END")
            ->latest('id_kp')
            ->get();

        return view('dosen.pendaftaran_index', compact('dosen', 'pendaftaranList'));
    }

    public function storeMahasiswaKp(Request $request)
    {
        $dosen = Auth::user()->dosen;
        if (!$dosen) {
            return back()->with('error', 'Profil Dosen tidak ditemukan.');
        }

        $validated = $request->validate([
            'nim' => 'required|string|max:30',
            'nama_lengkap' => 'required|string|max:150',
            'prodi' => 'nullable|string|in:Teknik Informatika,Sistem Informasi',
            'angkatan' => 'required|integer|min:2018|max:2030',
            'ipk_terakhir' => 'required|numeric|min:0|max:4.00',
            'sks_lulus' => 'required|integer|min:0|max:160',
            'instansi' => 'required|string|max:200',
            'alamat_instansi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'status_kp' => 'nullable|in:Pengajuan,Berjalan',
        ]);

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = !empty($validated['tanggal_selesai']) 
            ? Carbon::parse($validated['tanggal_selesai']) 
            : (clone $tanggalMulai)->addDays(30);

        // Akun login mahasiswa
        $user = User::firstOrCreate(
            ['username' => $validated['nim']],
            [
                'name' => $validated['nama_lengkap'],
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
            ]
        );

        $prodi = $validated['prodi'] ?? (str_contains($validated['nim'], '0202') ? 'Sistem Informasi' : 'Teknik Informatika');

        // Profil data mahasiswa
        $mahasiswa = Mahasiswa::updateOrCreate(
            ['nim' => $validated['nim']],
            [
                'user_id' => $user->id,
                'nama_lengkap' => $validated['nama_lengkap'],
                'angkatan' => $validated['angkatan'],
                'ipk_terakhir' => $validated['ipk_terakhir'],
                'sks_lulus' => $validated['sks_lulus'],
                'prodi' => $prodi,
            ]
        );

        // Data transaksi KP
        $kp = KerjaPraktik::create([
            'nim' => $validated['nim'],
            'nidn' => $dosen->nidn,
            'instansi' => $validated['instansi'],
            'alamat_instansi' => $validated['alamat_instansi'] ?? 'Pamekasan',
            'tanggal_mulai' => $tanggalMulai->toDateString(),
            'tanggal_selesai' => $tanggalSelesai->toDateString(),
            'status_kp' => $validated['status_kp'] ?? 'Berjalan',
            'catatan_koordinator' => 'Data ditambahkan langsung oleh Dosen Pembimbing.',
        ]);

        return back()->with('success', "Data mahasiswa {$validated['nama_lengkap']} (NIM: {$validated['nim']}) berhasil ditambahkan ke bimbingan Anda.");
    }

    public function updateStatusKp(Request $request, $id_kp)
    {
        $dosen = Auth::user()->dosen;
        $kp = KerjaPraktik::where('nidn', $dosen->nidn)->where('id_kp', $id_kp)->firstOrFail();

        $validated = $request->validate([
            'status_kp' => 'required|in:Pengajuan,Berjalan,Selesai,Batal',
            'catatan' => 'nullable|string',
        ]);

        $kp->update([
            'status_kp' => $validated['status_kp'],
            'catatan_koordinator' => $validated['catatan'] ?? $kp->catatan_koordinator,
        ]);

        return back()->with('success', "Status Pendaftaran KP {$kp->mahasiswa->nama_lengkap} berhasil diperbarui menjadi {$validated['status_kp']}.");
    }

    public function deleteMahasiswaKp($id_kp)
    {
        $dosen = Auth::user()->dosen;
        $kp = KerjaPraktik::where('nidn', $dosen->nidn)->where('id_kp', $id_kp)->firstOrFail();
        $nama = $kp->mahasiswa->nama_lengkap ?? $kp->nim;
        $kp->delete();

        return back()->with('success', "Data pendaftaran KP mahasiswa {$nama} berhasil dihapus.");
    }

    /**
     * Fitur 2: E-Logbook Mingguan Mahasiswa (Validasi & Review)
     */
    public function logbookIndex()
    {
        $dosen = Auth::user()->dosen;
        $bimbinganList = KerjaPraktik::with(['mahasiswa', 'logbooks'])
            ->where('nidn', $dosen->nidn)
            ->get();

        return view('dosen.logbook_index', compact('dosen', 'bimbinganList'));
    }

    public function logbookDetail($id_kp)
    {
        $dosen = Auth::user()->dosen;
        $kp = KerjaPraktik::with(['mahasiswa', 'logbooks'])
            ->where('nidn', $dosen->nidn)
            ->where('id_kp', $id_kp)
            ->firstOrFail();

        $weeks = $this->generateWeeklyLogbookForDosen($kp);

        return view('dosen.logbook_detail', compact('dosen', 'kp', 'weeks'));
    }

    public function validateLogbook(Request $request, $id_logbook)
    {
        $dosen = Auth::user()->dosen;
        $logbook = Logbook::whereHas('kerjaPraktik', function ($q) use ($dosen) {
            $q->where('nidn', $dosen->nidn);
        })->where('id_logbook', $id_logbook)->firstOrFail();

        $validated = $request->validate([
            'status_validasi' => 'required|in:Approved,Revisi',
            'catatan_dosen' => 'nullable|string',
        ]);

        $logbook->update([
            'status_validasi' => $validated['status_validasi'],
            'catatan_dosen' => $validated['catatan_dosen'],
        ]);

        return back()->with('success', "Logbook Hari ke-{$logbook->hari_ke} berhasil diperbarui: {$validated['status_validasi']}.");
    }

    /**
     * Fitur 3: Penilaian KP
     */
    public function penilaianIndex()
    {
        $dosen = Auth::user()->dosen;
        $bimbinganList = KerjaPraktik::with(['mahasiswa', 'penilaian', 'sertifikat', 'logbooks'])
            ->where('nidn', $dosen->nidn)
            ->whereIn('status_kp', ['Berjalan', 'Selesai'])
            ->get();

        return view('dosen.penilaian_index', compact('dosen', 'bimbinganList'));
    }

    public function penilaianForm($id_kp)
    {
        $dosen = Auth::user()->dosen;
        $kp = KerjaPraktik::with(['mahasiswa', 'penilaian', 'logbooks'])
            ->where('nidn', $dosen->nidn)
            ->where('id_kp', $id_kp)
            ->firstOrFail();

        $penilaian = $kp->penilaian;
        return view('dosen.penilaian_form', compact('dosen', 'kp', 'penilaian'));
    }

    public function storePenilaian(Request $request, $id_kp)
    {
        $dosen = Auth::user()->dosen;
        $kp = KerjaPraktik::where('nidn', $dosen->nidn)->where('id_kp', $id_kp)->firstOrFail();

        $validated = $request->validate([
            'nilai_pembimbing_instansi' => 'required|numeric|min:0|max:100',
            'nilai_disiplin' => 'required|numeric|min:0|max:100',
            'nilai_laporan' => 'required|numeric|min:0|max:100',
            'nilai_presentasi' => 'required|numeric|min:0|max:100',
            'catatan_evaluasi' => 'nullable|string',
        ]);

        $nilaiDosen = round(($validated['nilai_disiplin'] + $validated['nilai_laporan'] + $validated['nilai_presentasi']) / 3, 2);
        $nilaiAkhir = round((0.4 * $validated['nilai_pembimbing_instansi']) + (0.6 * $nilaiDosen), 2);
        $grade = Penilaian::determineGrade($nilaiAkhir);

        Penilaian::updateOrCreate(
            ['id_kp' => $kp->id_kp],
            [
                'nilai_pembimbing_instansi' => $validated['nilai_pembimbing_instansi'],
                'nilai_dosen_pembimbing' => $nilaiDosen,
                'nilai_disiplin' => $validated['nilai_disiplin'],
                'nilai_laporan' => $validated['nilai_laporan'],
                'nilai_presentasi' => $validated['nilai_presentasi'],
                'nilai_akhir' => $nilaiAkhir,
                'grade' => $grade,
                'catatan_evaluasi' => $validated['catatan_evaluasi'] ?? null,
            ]
        );

        $kp->update(['status_kp' => 'Selesai']);

        return redirect()->route('dosen.penilaian.index')->with('success', "Penilaian untuk {$kp->mahasiswa->nama_lengkap} berhasil disimpan. Nilai Akhir: {$nilaiAkhir} ({$grade}).");
    }

    public function cetakNilai($id_kp)
    {
        $dosen = Auth::user()->dosen;
        $kp = KerjaPraktik::with(['mahasiswa', 'dosen', 'penilaian'])
            ->where('nidn', $dosen->nidn)
            ->where('id_kp', $id_kp)
            ->firstOrFail();

        if (!$kp->penilaian) {
            return back()->with('error', 'Mahasiswa belum memiliki penilaian.');
        }

        $penilaian = $kp->penilaian;
        return view('penilaian.cetak', compact('kp', 'penilaian'));
    }

    /**
     * Fitur 4: Cetak Sertifikat KP
     */
    public function sertifikatIndex()
    {
        $dosen = Auth::user()->dosen;
        $bimbinganList = KerjaPraktik::with(['mahasiswa', 'penilaian'])
            ->where('nidn', $dosen->nidn)
            ->whereIn('status_kp', ['Berjalan', 'Selesai'])
            ->get();

        return view('dosen.sertifikat_index', compact('dosen', 'bimbinganList'));
    }

    public function cetakSertifikat($id_kp)
    {
        $dosen = Auth::user()->dosen;
        $kp = KerjaPraktik::with(['mahasiswa', 'dosen', 'penilaian'])
            ->where('nidn', $dosen->nidn)
            ->where('id_kp', $id_kp)
            ->firstOrFail();

        $penilaian = $kp->penilaian;
        return view('sertifikat.cetak', compact('kp', 'penilaian'));
    }

    /**
     * Helper: Generate E-Logbook Mingguan
     */
    protected function generateWeeklyLogbookForDosen(KerjaPraktik $kp): array
    {
        if (empty($kp->instansi) || !$kp->tanggal_mulai) {
            return [];
        }

        $startDate = Carbon::parse($kp->tanggal_mulai);
        $today = Carbon::today();
        $existingLogbooks = $kp->logbooks ? $kp->logbooks->keyBy('hari_ke') : collect();

        $weeks = [
            1 => ['minggu_ke' => 1, 'label' => 'Minggu 1', 'hari_range' => 'Hari 1 - 7', 'days' => []],
            2 => ['minggu_ke' => 2, 'label' => 'Minggu 2', 'hari_range' => 'Hari 8 - 14', 'days' => []],
            3 => ['minggu_ke' => 3, 'label' => 'Minggu 3', 'hari_range' => 'Hari 15 - 21', 'days' => []],
            4 => ['minggu_ke' => 4, 'label' => 'Minggu 4', 'hari_range' => 'Hari 22 - 28', 'days' => []],
            5 => ['minggu_ke' => 5, 'label' => 'Minggu 5', 'hari_range' => 'Hari 29 - 30', 'days' => []],
        ];

        for ($i = 1; $i <= 30; $i++) {
            $targetDate = (clone $startDate)->addDays($i - 1);
            $entry = $existingLogbooks->get($i);

            $dayData = [
                'hari_ke' => $i,
                'tanggal' => $targetDate,
                'status' => $entry ? $entry->status_validasi : ($targetDate->lt($today) ? 'Terlewat' : 'Belum Diisi'),
                'entry' => $entry,
            ];

            if ($i <= 7) {
                $weeks[1]['days'][] = $dayData;
            } elseif ($i <= 14) {
                $weeks[2]['days'][] = $dayData;
            } elseif ($i <= 21) {
                $weeks[3]['days'][] = $dayData;
            } elseif ($i <= 28) {
                $weeks[4]['days'][] = $dayData;
            } else {
                $weeks[5]['days'][] = $dayData;
            }
        }

        foreach ($weeks as &$w) {
            $w['filled_count'] = collect($w['days'])->filter(fn($d) => $d['entry'] !== null)->count();
            $w['approved_count'] = collect($w['days'])->filter(fn($d) => $d['entry'] && $d['entry']->status_validasi === 'Approved')->count();
            $w['pending_count'] = collect($w['days'])->filter(fn($d) => $d['entry'] && $d['entry']->status_validasi === 'Pending')->count();
            $w['total_days'] = count($w['days']);
        }

        return $weeks;
    }
}
