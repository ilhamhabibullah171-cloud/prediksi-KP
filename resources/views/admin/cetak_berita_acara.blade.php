<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Nilai KP - {{ $kp->nim }} - FT UIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Times+New+Roman&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Times New Roman', serif; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
            @page { size: A4 portrait; margin: 20mm; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center">

    <!-- Action Bar -->
    <div class="no-print max-w-3xl w-full mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center space-x-2 text-xs font-bold text-slate-700">
            <i class="fa-solid fa-file-circle-check text-emerald-600 text-base"></i>
            <span>Dokumen Resmi Berita Acara Nilai Kerja Praktik (FT UIM)</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()"
                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Dokumen</span>
            </button>
            <button onclick="window.close()"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Document Container (A4 style) -->
    <div class="max-w-3xl w-full bg-white p-8 sm:p-12 shadow-xl border border-slate-200 text-black leading-relaxed">
        
        <!-- Kop Surat -->
        <div class="text-center border-b-4 border-double border-black pb-4 mb-6">
            <h3 class="text-sm font-bold uppercase tracking-wider">YAYASAN PEMBINA PENDIDIKAN TINGGI ISLAM MADURA</h3>
            <h1 class="text-lg font-extrabold uppercase tracking-tight">UNIVERSITAS ISLAM MADURA (UIM) PAMEKASAN</h1>
            <h2 class="text-base font-bold uppercase tracking-wide">FAKULTAS TEKNIK</h2>
            <h4 class="text-xs font-semibold uppercase tracking-wider">PROGRAM STUDI {{ strtoupper($kp->mahasiswa->prodi ?? 'Teknik Informatika') }}</h4>
            <p class="text-[11px] italic mt-1 text-slate-700">
                Alamat: Kompleks Pondok Pesantren Miftahul Ulum Bettet, Pamekasan, Madura Telp. (0324) 321748 Website: www.uim.ac.id
            </p>
        </div>

        <!-- Document Title -->
        <div class="text-center my-6">
            <h2 class="text-base font-bold uppercase underline tracking-wide">BERITA ACARA NILAI AKHIR KERJA PRAKTIK (KP)</h2>
            <p class="text-xs mt-1">Nomor: {{ 100 + $kp->id_kp }}/BA-KP/FT-UIM/{{ date('Y') }}</p>
        </div>

        <!-- Opening Text -->
        <p class="text-xs leading-relaxed text-justify mb-4">
            Pada hari ini, tanggal <strong>{{ now()->translatedFormat('d F Y') }}</strong>, telah diselenggarakan rapat penetapan dan validasi evaluasi akhir mata kuliah Kerja Praktik (KP) bagi mahasiswa Fakultas Teknik Universitas Islam Madura berikut:
        </p>

        <!-- Student Identity Table -->
        <div class="text-xs mb-6 pl-4 space-y-1.5">
            <div class="grid grid-cols-4">
                <span class="font-bold">Nama Lengkap</span>
                <span class="col-span-3">: {{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</span>
            </div>
            <div class="grid grid-cols-4">
                <span class="font-bold">Nomor Induk Mahasiswa</span>
                <span class="col-span-3 font-mono">: {{ $kp->nim }}</span>
            </div>
            <div class="grid grid-cols-4">
                <span class="font-bold">Program Studi</span>
                <span class="col-span-3">: {{ $kp->mahasiswa->prodi ?? 'Teknik Informatika' }}</span>
            </div>
            <div class="grid grid-cols-4">
                <span class="font-bold">Instansi / Perusahaan</span>
                <span class="col-span-3">: {{ $kp->instansi }} ({{ $kp->alamat_instansi ?? '-' }})</span>
            </div>
            <div class="grid grid-cols-4">
                <span class="font-bold">Dosen Pembimbing</span>
                <span class="col-span-3">: {{ $kp->dosen->nama_dosen ?? '-' }} (NIDN: {{ $kp->dosen->nidn ?? '-' }})</span>
            </div>
            <div class="grid grid-cols-4">
                <span class="font-bold">Periode Pelaksanaan</span>
                <span class="col-span-3">: {{ $kp->tanggal_mulai->format('d/m/Y') }} s/d {{ $kp->tanggal_selesai->format('d/m/Y') }} (30 Hari)</span>
            </div>
        </div>

        <!-- Evaluation Rubric Table -->
        @php $pen = $kp->penilaian; @endphp
        <div class="mb-6">
            <h4 class="text-xs font-bold uppercase mb-2">I. Hasil Penilaian & Rubrik Evaluasi</h4>
            <table class="w-full text-xs border border-black border-collapse">
                <thead>
                    <tr class="bg-slate-100 border border-black font-bold text-center">
                        <th class="border border-black p-2 w-12">No</th>
                        <th class="border border-black p-2 text-left">Komponen Penilaian</th>
                        <th class="border border-black p-2 w-28">Bobot</th>
                        <th class="border border-black p-2 w-28">Nilai Angka</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black p-2 text-center font-mono">1</td>
                        <td class="border border-black p-2">
                            <strong>Pembimbing Lapangan / Instansi</strong>
                            <p class="text-[10px] text-slate-600">Aspek kedisiplinan, kerjasama tim, dan etika kerja di instansi</p>
                        </td>
                        <td class="border border-black p-2 text-center font-bold">40%</td>
                        <td class="border border-black p-2 text-center font-mono font-bold">
                            {{ $pen ? number_format($pen->nilai_pembimbing_instansi, 2) : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 text-center font-mono">2</td>
                        <td class="border border-black p-2">
                            <strong>Dosen Pembimbing Kerja Praktik</strong>
                            <ul class="text-[10px] text-slate-600 list-disc list-inside mt-0.5">
                                <li>Kedisiplinan & Logbook 30 Hari: {{ $pen ? number_format($pen->nilai_disiplin, 1) : '-' }}</li>
                                <li>Kualitas Laporan Teknis KP: {{ $pen ? number_format($pen->nilai_laporan, 1) : '-' }}</li>
                                <li>Presentasi & Ujian Sidang KP: {{ $pen ? number_format($pen->nilai_presentasi, 1) : '-' }}</li>
                            </ul>
                        </td>
                        <td class="border border-black p-2 text-center font-bold">60%</td>
                        <td class="border border-black p-2 text-center font-mono font-bold">
                            {{ $pen ? number_format($pen->nilai_dosen_pembimbing, 2) : '-' }}
                        </td>
                    </tr>
                    <tr class="bg-slate-50 font-bold">
                        <td colspan="3" class="border border-black p-2.5 text-right uppercase">NILAI AKHIR (ANGKA)</td>
                        <td class="border border-black p-2.5 text-center font-mono text-sm">
                            {{ $pen ? number_format($pen->nilai_akhir, 2) : '-' }}
                        </td>
                    </tr>
                    <tr class="bg-slate-100 font-extrabold text-sm">
                        <td colspan="3" class="border border-black p-2.5 text-right uppercase">GRADE KELULUSAN (HURUF)</td>
                        <td class="border border-black p-2.5 text-center text-base">
                            {{ $pen ? $pen->grade : '-' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($pen && $pen->catatan_evaluasi)
            <div class="text-xs mb-6 p-3 border border-dashed border-black">
                <strong>Catatan Evaluasi / Rekomendasi:</strong>
                <p class="italic mt-1 text-justify">{{ $pen->catatan_evaluasi }}</p>
            </div>
        @endif

        <p class="text-xs leading-relaxed text-justify mb-8">
            Demikian berita acara ini dibuat dan disahkan dengan sebenar-benarnya untuk dipergunakan sebagai kelengkapan berkas Yudisium kelulusan sarjana di Fakultas Teknik Universitas Islam Madura.
        </p>

        <!-- Official Signatures -->
        <div class="grid grid-cols-2 gap-8 text-xs text-center mt-8">
            <div>
                <p>Mahasiswa Yang Bersangkutan,</p>
                <div class="h-20"></div>
                <p class="font-bold underline">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</p>
                <p class="text-[11px] text-slate-600">NIM. {{ $kp->nim }}</p>
            </div>

            <div>
                <p>Pamekasan, {{ now()->translatedFormat('d F Y') }}<br>Dosen Pembimbing KP,</p>
                <div class="h-16"></div>
                <p class="font-bold underline">{{ $kp->dosen->nama_dosen ?? '-' }}</p>
                <p class="text-[11px] text-slate-600">NIDN. {{ $kp->dosen->nidn ?? '-' }}</p>
            </div>

            <div class="col-span-2 mt-4">
                <p>Mengetahui & Menyetujui,<br><strong>Dekan Fakultas Teknik UIM</strong></p>
                <div class="h-16"></div>
                <p class="font-bold underline">Dr. H. Akhmad Fauzi, M.Kom.</p>
                <p class="text-[11px] text-slate-600">NIDN. 0715088201</p>
            </div>
        </div>

    </div>

</body>
</html>

