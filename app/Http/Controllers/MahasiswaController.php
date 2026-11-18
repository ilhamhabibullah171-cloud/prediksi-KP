<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\KerjaPraktik;
use App\Models\Logbook;
use App\Models\Penilaian;
use App\Models\Dosen;
use Carbon\Carbon;

class MahasiswaController extends Controller
{
    /**
     * Dashboard Mahasiswa: Menampilkan 4 Fitur Inti
     * 1. Form Pendaftaran KP
     * 2. E-Logbook Mingguan (7 hari x 30 hari)
     * 3. Penilaian KP
     * 4. Cetak Sertifikat KP
     */
    public function dashboard()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            return view('mahasiswa.no_profile');
        }

        $kp = $mahasiswa->activeKp ?? $mahasiswa->kerjaPraktik()->latest('id_kp')->first();
        $dosenList = Dosen::orderBy('nama_dosen')->get();
        $penilaian = $kp ? $kp->penilaian : null;

        // Weekly Logbook 7 Hari selama 30 Hari
        $weeks = $kp ? $this->generateWeeklyLogbook($kp) : [];

        // Daily status reminder
        $todayLogbookStatus = null;
        $currentDayNumber = null;
        if ($kp && $kp->status_kp === 'Berjalan') {
            $startDate = Carbon::parse($kp->tanggal_mulai);
            $today = Carbon::today();
            if ($today->between($startDate, Carbon::parse($kp->tanggal_selesai))) {
                $currentDayNumber = $startDate->diffInDays($today) + 1;
                $todayLogbook = $kp->logbooks()->where('hari_ke', $currentDayNumber)->first();
                $todayLogbookStatus = $todayLogbook ? $todayLogbook->status_validasi : 'Belum';
            }
        }

        return view('mahasiswa.dashboard', compact(
            'mahasiswa', 'kp', 'weeks', 'penilaian', 'dosenList', 'todayLogbookStatus', 'currentDayNumber'
        ));
    }

    /**
     * Fitur 1: Form Pendaftaran KP
     */
    public function pendaftaran()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $kp = $mahasiswa ? KerjaPraktik::where('nim', $mahasiswa->nim)->latest('id_kp')->first() : null;
        $dosenList = Dosen::orderBy('nama_dosen')->get();

        return view('mahasiswa.pengajuan', compact('mahasiswa', 'kp', 'dosenList'));
    }

    public function storePendaftaran(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $validated = $request->validate([
            'instansi' => 'required|string|max:200',
            'alamat_instansi' => 'required|string|max:255',
            'judul_kp' => 'required|string|max:255',
            'kontak_instansi' => 'nullable|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'usulan_dosen' => 'nullable|string|max:150',
            'berkas_kp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ], [
            'instansi.required' => 'Nama instansi/perusahaan tempat KP wajib diisi.',
            'alamat_instansi.required' => 'Alamat lengkap instansi tempat KP wajib diisi.',
            'judul_kp.required' => 'Judul atau topik rencana Kerja Praktik wajib diisi.',
            'tanggal_mulai.required' => 'Rencana tanggal mulai KP wajib dipilih.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'berkas_kp.max' => 'Ukuran berkas lampiran maksimal 3MB.',
            'berkas_kp.mimes' => 'Format berkas harus PDF, JPG, atau PNG.',
        ]);

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = !empty($validated['tanggal_selesai'])
            ? Carbon::parse($validated['tanggal_selesai'])
            : (clone $tanggalMulai)->addDays(30);

        $kp = KerjaPraktik::where('nim', $mahasiswa->nim)->latest('id_kp')->first();

        $filePath = null;
        if ($request->hasFile('berkas_kp')) {
            $file = $request->file('berkas_kp');
            $fileName = 'proposal_' . $mahasiswa->nim . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/berkas_pendaftaran', $fileName);
            $filePath = 'berkas_pendaftaran/' . $fileName;
        }

        $dataKp = [
            'nim' => $mahasiswa->nim,
            'instansi' => $validated['instansi'],
            'alamat_instansi' => $validated['alamat_instansi'],
            'judul_kp' => $validated['judul_kp'],
            'kontak_instansi' => $validated['kontak_instansi'] ?? null,
            'berkas_kp' => $filePath ?? ($kp ? $kp->berkas_kp : null),
            'tanggal_mulai' => $tanggalMulai->toDateString(),
            'tanggal_selesai' => $tanggalSelesai->toDateString(),
            'usulan_dosen' => $validated['usulan_dosen'] ?? null,
            'status_kp' => 'Pengajuan',
            'nidn' => null, // Wewenang mutlak Admin Fakultas Teknik untuk menentukan dan menetapkan Dosen Pembimbing
            'catatan_koordinator' => 'Formulir pendaftaran diajukan oleh mahasiswa. Menunggu penetapan Dosen Pembimbing oleh Admin Fakultas Teknik.',
        ];

        if ($kp) {
            $kp->update($dataKp);
        } else {
            $kp = KerjaPraktik::create($dataKp);
        }

        return redirect()->route('mahasiswa.pendaftaran')
            ->with('success', 'Formulir pendaftaran Kerja Praktik berhasil dikirim! Data telah masuk ke Admin Fakultas Teknik untuk penentuan Dosen Pembimbing.');
    }

    public function cetakPendaftaran()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $kp = KerjaPraktik::with(['mahasiswa', 'dosen'])->where('nim', $mahasiswa->nim)->latest('id_kp')->first();
        if (!$kp) {
            return redirect()->route('mahasiswa.pendaftaran')->with('error', 'Data pendaftaran KP belum ditemukan. Silakan isi formulir pendaftaran terlebih dahulu.');
        }

        return view('mahasiswa.pendaftaran_cetak', compact('mahasiswa', 'kp'));
    }

    /**
     * Fitur 2: E-Logbook Mingguan (7 Hari per Minggu selama 30 Hari)
     */
    public function logbook()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $kp = $mahasiswa ? KerjaPraktik::where('nim', $mahasiswa->nim)->latest('id_kp')->first() : null;

        if (!$kp) {
            return redirect()->route('mahasiswa.pendaftaran')->with('error', 'Data Kerja Praktik tidak ditemukan.');
        }

        $weeks = $this->generateWeeklyLogbook($kp);

        return view('mahasiswa.logbook', compact('mahasiswa', 'kp', 'weeks'));
    }

    public function storeLogbook(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $kp = $mahasiswa ? KerjaPraktik::where('nim', $mahasiswa->nim)->latest('id_kp')->first() : null;

        if (!$kp) {
            return back()->with('error', 'Data Kerja Praktik tidak ditemukan.');
        }

        if (empty($kp->instansi) || !$kp->tanggal_mulai) {
            return back()->with('error', 'Anda belum dapat menambahkan laporan E-Logbook karena tempat dan jadwal pelaksanaan KP belum ditetapkan oleh Admin Fakultas Teknik.');
        }

        $validated = $request->validate([
            'hari_ke' => 'required|integer|min:1|max:30',
            'tanggal_kegiatan' => 'required|date',
            'deskripsi_pekerjaan' => 'required|string|min:5|max:2000',
            'dokumentasi' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:2048',
        ], [
            'hari_ke.required' => 'Hari ke pelaksanaan kegiatan wajib dipilih.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'deskripsi_pekerjaan.required' => 'Deskripsi pekerjaan/aktivitas kegiatan wajib diisi.',
            'deskripsi_pekerjaan.min' => 'Deskripsi pekerjaan minimal 5 karakter.',
            'dokumentasi.max' => 'Ukuran berkas dokumentasi maksimal 2MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('dokumentasi')) {
            $file = $request->file('dokumentasi');
            $fileName = 'logbook_' . $kp->id_kp . '_h' . $validated['hari_ke'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/logbook_docs', $fileName);
            $filePath = 'logbook_docs/' . $fileName;
        }

        $logbook = Logbook::where('id_kp', $kp->id_kp)->where('hari_ke', $validated['hari_ke'])->first();

        if ($logbook) {
            $logbook->update([
                'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
                'deskripsi_pekerjaan' => $validated['deskripsi_pekerjaan'],
                'dokumentasi' => $filePath ?? $logbook->dokumentasi,
                'status_validasi' => 'Pending',
                'waktu_submit' => Carbon::now(),
            ]);
        } else {
            Logbook::create([
                'id_kp' => $kp->id_kp,
                'hari_ke' => $validated['hari_ke'],
                'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
                'deskripsi_pekerjaan' => $validated['deskripsi_pekerjaan'],
                'dokumentasi' => $filePath,
                'status_validasi' => 'Pending',
                'waktu_submit' => Carbon::now(),
            ]);
        }

        return back()->with('success', "Laporan kegiatan Hari ke-{$validated['hari_ke']} berhasil disimpan dan menunggu validasi dari Dosen Pembimbing.");
    }

    /**
     * Fitur 3: Penilaian KP
     */
    public function penilaian()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $kp = $mahasiswa ? KerjaPraktik::where('nim', $mahasiswa->nim)->latest('id_kp')->first() : null;
        $penilaian = $kp ? $kp->penilaian : null;

        return view('mahasiswa.penilaian', compact('mahasiswa', 'kp', 'penilaian'));
    }

    public function cetakNilai()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $kp = $mahasiswa->kerjaPraktik()->with(['mahasiswa', 'dosen', 'penilaian'])->latest('id_kp')->first();
        if (!$kp || !$kp->penilaian) {
            return redirect()->route('mahasiswa.penilaian')->with('error', 'Penilaian KP belum diterbitkan oleh Dosen Pembimbing.');
        }

        $penilaian = $kp->penilaian;
        return view('penilaian.cetak', compact('kp', 'penilaian'));
    }

    /**
     * Fitur 4: Cetak Sertifikat KP
     */
    public function cetakSertifikat()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $kp = $mahasiswa->kerjaPraktik()->with(['mahasiswa', 'dosen', 'penilaian'])->latest('id_kp')->first();
        if (!$kp) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data Kerja Praktik tidak ditemukan.');
        }

        $penilaian = $kp->penilaian;
        return view('sertifikat.cetak', compact('kp', 'penilaian'));
    }

    /**
     * Helper: Generate E-Logbook Mingguan (7 Hari per Minggu selama 30 Hari)
     */
    protected function generateWeeklyLogbook(KerjaPraktik $kp): array
    {
        if (empty($kp->instansi) || !$kp->tanggal_mulai) {
            return [];
        }

        $startDate = Carbon::parse($kp->tanggal_mulai);
        $today = Carbon::today();
        $existingLogbooks = $kp->logbooks ? $kp->logbooks->keyBy('hari_ke') : collect();

        $weeks = [
            1 => ['minggu_ke' => 1, 'label' => 'Minggu 1', 'hari_range' => 'Hari 1 - 7', 'start_day' => 1, 'end_day' => 7, 'days' => []],
            2 => ['minggu_ke' => 2, 'label' => 'Minggu 2', 'hari_range' => 'Hari 8 - 14', 'start_day' => 8, 'end_day' => 14, 'days' => []],
            3 => ['minggu_ke' => 3, 'label' => 'Minggu 3', 'hari_range' => 'Hari 15 - 21', 'start_day' => 15, 'end_day' => 21, 'days' => []],
            4 => ['minggu_ke' => 4, 'label' => 'Minggu 4', 'hari_range' => 'Hari 22 - 28', 'start_day' => 22, 'end_day' => 28, 'days' => []],
            5 => ['minggu_ke' => 5, 'label' => 'Minggu 5', 'hari_range' => 'Hari 29 - 30', 'start_day' => 29, 'end_day' => 30, 'days' => []],
        ];

        for ($i = 1; $i <= 30; $i++) {
            $targetDate = (clone $startDate)->addDays($i - 1);
            $entry = $existingLogbooks->get($i);

            $isPassed = $targetDate->lt($today);
            $isToday = $targetDate->isToday();

            if ($entry) {
                $status = $entry->status_validasi;
            } elseif ($isToday) {
                $status = 'Hari Ini';
            } elseif ($isPassed) {
                $status = 'Belum Diisi';
            } else {
                $status = 'Belum Tiba';
            }

            $dayData = [
                'hari_ke' => $i,
                'tanggal' => $targetDate,
                'status' => $status,
                'entry' => $entry,
                'can_fill' => true,
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
            $w['total_days'] = count($w['days']);
        }

        return $weeks;
    }
}
