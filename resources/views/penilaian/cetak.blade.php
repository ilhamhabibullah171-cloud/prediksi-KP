<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara & Lembar Nilai KP - {{ $kp->mahasiswa->nama_lengkap ?? 'Mahasiswa' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            @page { size: A4 portrait; margin: 15mm 15mm 15mm 15mm; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8">

    <!-- Floating Action Bar -->
    <div class="no-print max-w-3xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <button onclick="window.history.back()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl flex items-center space-x-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>
        <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center space-x-2 shadow-md">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Dokumen / Simpan PDF</span>
        </button>
    </div>

    <!-- Printable Paper -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0">
        
        <!-- Kop Surat Resmi -->
        <div class="flex items-center space-x-4 border-b-2 border-slate-900 pb-4 mb-6">
            <div class="w-16 h-16 rounded-2xl bg-emerald-800 text-white flex items-center justify-center text-3xl font-bold flex-shrink-0">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="text-center flex-1">
                <h4 class="text-xs font-bold tracking-wider uppercase text-slate-600">Yayasan Bina Insan Madura</h4>
                <h2 class="text-xl font-extrabold uppercase text-slate-900 tracking-tight">UNIVERSITAS ISLAM MADURA</h2>
                <h3 class="text-sm font-bold uppercase text-emerald-800">FAKULTAS TEKNIK &bull; PROGRAM STUDI {{ strtoupper($kp->mahasiswa->prodi ?? 'Teknik Informatika') }}</h3>
                <p class="text-[10px] text-slate-500 mt-0.5">Jl. PP Miftahul Ulum Bettet, Pamekasan, Jawa Timur | Telp: (0324) 321745</p>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h1 class="text-base font-extrabold uppercase text-slate-900 tracking-wide underline">
                BERITA ACARA & LEMBAR PENILAIAN KERJA PRAKTIK (KP)
            </h1>
            <p class="text-xs text-slate-500 font-mono mt-1">
                Nomor: BA-KP/FT-UIM/2026/{{ str_pad($kp->id_kp, 4, '0', STR_PAD_LEFT) }}
            </p>
        </div>

        <!-- Student Information -->
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 text-xs mb-6 space-y-2">
            <div class="grid grid-cols-3">
                <span class="text-slate-500 font-medium">Nama Mahasiswa</span>
                <span class="col-span-2 font-bold text-slate-800">: {{ $kp->mahasiswa->nama_lengkap ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-3">
                <span class="text-slate-500 font-medium">Nomor Induk Mahasiswa (NIM)</span>
                <span class="col-span-2 font-bold text-slate-800 font-mono">: {{ $kp->nim }}</span>
            </div>
            <div class="grid grid-cols-3">
                <span class="text-slate-500 font-medium">Program Studi</span>
                <span class="col-span-2 font-bold text-slate-800">: {{ $kp->mahasiswa->prodi ?? 'Teknik Informatika' }}</span>
            </div>
            <div class="grid grid-cols-3">
                <span class="text-slate-500 font-medium">Tempat / Instansi KP</span>
                <span class="col-span-2 font-bold text-slate-800">: {{ $kp->instansi }}</span>
            </div>
            <div class="grid grid-cols-3">
                <span class="text-slate-500 font-medium">Periode Pelaksanaan</span>
                <span class="col-span-2 font-bold text-slate-800">
                    : {{ \Carbon\Carbon::parse($kp->tanggal_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($kp->tanggal_selesai)->translatedFormat('d F Y') }} (30 Hari)
                </span>
            </div>
            <div class="grid grid-cols-3">
                <span class="text-slate-500 font-medium">Dosen Pembimbing KP</span>
                <span class="col-span-2 font-bold text-slate-800">: {{ $kp->dosen->nama_dosen ?? '-' }} (NIDN: {{ $kp->nidn ?? '-' }})</span>
            </div>
        </div>

        <!-- Rubric Table -->
        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
            Rincian Penilaian Aspek Kerja Praktik:
        </h3>
        <table class="w-full text-xs border border-slate-300 rounded-lg overflow-hidden mb-6">
            <thead class="bg-slate-100 text-slate-700">
                <tr class="divide-x divide-slate-300 border-b border-slate-300">
                    <th class="py-2.5 px-3 text-center w-12">No</th>
                    <th class="py-2.5 px-3 text-left">Komponen Penilaian</th>
                    <th class="py-2.5 px-3 text-center w-24">Bobot</th>
                    <th class="py-2.5 px-3 text-center w-28">Nilai Angka (0-100)</th>
                    <th class="py-2.5 px-3 text-center w-28">Nilai Terbobot</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <tr class="divide-x divide-slate-200">
                    <td class="py-2.5 px-3 text-center font-medium">1</td>
                    <td class="py-2.5 px-3">
                        <div class="font-bold text-slate-800">Nilai Pembimbing Instansi / Lapangan</div>
                        <div class="text-[11px] text-slate-500">Kinerja harian, kepatuhan, keaktifan di tempat KP</div>
                    </td>
                    <td class="py-2.5 px-3 text-center font-semibold">40%</td>
                    <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                        {{ number_format($penilaian->nilai_pembimbing_instansi ?? 0, 1) }}
                    </td>
                    <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                        {{ number_format((0.4 * ($penilaian->nilai_pembimbing_instansi ?? 0)), 2) }}
                    </td>
                </tr>

                <tr class="divide-x divide-slate-200 bg-slate-50/50">
                    <td class="py-2.5 px-3 text-center font-medium" rowspan="3">2</td>
                    <td class="py-2.5 px-3">
                        <div class="font-medium text-slate-700">a. Aspek Kedisiplinan & Logbook Harian (30 Hari)</div>
                    </td>
                    <td class="py-2.5 px-3 text-center font-semibold" rowspan="3">60%<br><span class="text-[10px] text-slate-400 font-normal">(Dosen)</span></td>
                    <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                        {{ number_format($penilaian->nilai_disiplin ?? 0, 1) }}
                    </td>
                    <td class="py-2.5 px-3 text-center text-slate-400" rowspan="3">
                        <span class="font-bold text-slate-800">
                            {{ number_format((0.6 * ($penilaian->nilai_dosen_pembimbing ?? 0)), 2) }}
                        </span>
                    </td>
                </tr>
                <tr class="divide-x divide-slate-200 bg-slate-50/50">
                    <td class="py-2.5 px-3">
                        <div class="font-medium text-slate-700">b. Aspek Sistematika Penulisan & Laporan KP</div>
                    </td>
                    <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                        {{ number_format($penilaian->nilai_laporan ?? 0, 1) }}
                    </td>
                </tr>
                <tr class="divide-x divide-slate-200 bg-slate-50/50">
                    <td class="py-2.5 px-3">
                        <div class="font-medium text-slate-700">c. Aspek Penguasaan Materi & Presentasi / Evaluasi</div>
                    </td>
                    <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                        {{ number_format($penilaian->nilai_presentasi ?? 0, 1) }}
                    </td>
                </tr>

                <!-- Summary Row -->
                <tr class="divide-x divide-slate-300 bg-emerald-50/80 font-bold text-emerald-950 border-t-2 border-slate-300">
                    <td class="py-3 px-3 text-center" colspan="3">NILAI AKHIR KUMULATIF & PREDIKAT KELULUSAN</td>
                    <td class="py-3 px-3 text-center text-sm font-extrabold text-emerald-800">
                        {{ number_format($penilaian->nilai_akhir ?? 0, 2) }}
                    </td>
                    <td class="py-3 px-3 text-center text-sm font-extrabold text-emerald-800">
                        GRADE: {{ $penilaian->grade ?? '-' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Catatan Evaluasi -->
        @if($penilaian && $penilaian->catatan_evaluasi)
            <div class="mb-6 p-3 rounded-lg border border-slate-200 bg-slate-50 text-xs">
                <span class="font-bold text-slate-700 block mb-0.5">Catatan Evaluasi Pembimbing:</span>
                <p class="text-slate-600 italic">"{{ $penilaian->catatan_evaluasi }}"</p>
            </div>
        @endif

        <!-- Decision Box -->
        <div class="border border-emerald-300 bg-emerald-50/50 rounded-xl p-4 text-xs text-center mb-8">
            <span class="text-slate-600">Berdasarkan hasil evaluasi dan penilaian di atas, mahasiswa bersangkutan dinyatakan:</span>
            <div class="text-base font-extrabold text-emerald-900 mt-1 uppercase tracking-wider">
                @if(($penilaian->nilai_akhir ?? 0) >= 55)
                    LULUS KERJA PRAKTIK (MEMENUHI SYARAT SIDANG/YUDISIUM)
                @else
                    MENGULANG / TIDAK LULUS
                @endif
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-xs text-center pt-2">
            <div>
                <p class="text-slate-500">Mengetahui,</p>
                <p class="font-bold text-slate-800 uppercase mt-0.5">Koordinator Kerja Praktik FT UIM</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="font-serif italic text-slate-400 text-sm">Ttd Digital</span>
                </div>
                <p class="font-bold text-slate-900 underline">Ach. Syarifuddin, M.Kom.</p>
                <p class="text-[10px] text-slate-500 font-mono">NIDN: 0722048501</p>
            </div>

            <div>
                <p class="text-slate-500">Pamekasan, {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-800 uppercase mt-0.5">Dosen Pembimbing KP</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="font-serif italic text-slate-700 font-semibold text-base">
                        {{ substr($kp->dosen->nama_dosen ?? 'Pembimbing', 0, 16) }}
                    </span>
                </div>
                <p class="font-bold text-slate-900 underline">{{ $kp->dosen->nama_dosen ?? 'Dosen Pembimbing' }}</p>
                <p class="text-[10px] text-slate-500 font-mono">NIDN: {{ $kp->nidn ?? '-' }}</p>
            </div>
        </div>

    </div>

</body>
</html>

