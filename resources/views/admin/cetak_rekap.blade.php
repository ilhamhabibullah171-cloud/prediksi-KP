<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Nilai Yudisium KP - FT UIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Times New Roman', serif; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
            @page { size: A4 landscape; margin: 15mm; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center">

    <!-- Action Bar -->
    <div class="no-print max-w-5xl w-full mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center space-x-2 text-xs font-bold text-slate-700">
            <i class="fa-solid fa-file-invoice text-emerald-600 text-base"></i>
            <span>Laporan Rekapitulasi Nilai Akhir Kerja Praktik (Sidang Yudisium FT UIM)</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()"
                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Rekapitulasi</span>
            </button>
            <button onclick="window.close()"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Document Container (Landscape) -->
    <div class="max-w-5xl w-full bg-white p-8 sm:p-12 shadow-xl border border-slate-200 text-black leading-relaxed">
        
        <!-- Kop Surat -->
        <div class="text-center border-b-4 border-double border-black pb-4 mb-6">
            <h3 class="text-xs font-bold uppercase tracking-wider">YAYASAN PEMBINA PENDIDIKAN TINGGI ISLAM MADURA</h3>
            <h1 class="text-base font-extrabold uppercase tracking-tight">UNIVERSITAS ISLAM MADURA (UIM) PAMEKASAN</h1>
            <h2 class="text-sm font-bold uppercase tracking-wide">FAKULTAS TEKNIK - PROGRAM STUDI TEKNIK INFORMATIKA</h2>
            <p class="text-[10px] italic mt-0.5 text-slate-700">
                Alamat: Kompleks PP Miftahul Ulum Bettet, Pamekasan, Madura Telp. (0324) 321748 Website: www.uim.ac.id
            </p>
        </div>

        <div class="text-center my-4">
            <h2 class="text-sm font-bold uppercase underline tracking-wide">REKAPITULASI NILAI AKHIR KERJA PRAKTIK UNTUK SIDANG YUDISIUM</h2>
            <p class="text-xs mt-0.5">Tahun Akademik {{ date('Y') }}/{{ date('Y') + 1 }}</p>
        </div>

        <table class="w-full text-xs border border-black border-collapse my-6">
            <thead>
                <tr class="bg-slate-100 border border-black font-bold text-center">
                    <th class="border border-black p-2 w-10">No</th>
                    <th class="border border-black p-2 w-32">NIM</th>
                    <th class="border border-black p-2 text-left">Nama Mahasiswa</th>
                    <th class="border border-black p-2 text-left">Instansi Tempat KP</th>
                    <th class="border border-black p-2 text-left">Dosen Pembimbing</th>
                    <th class="border border-black p-2 w-20">Nilai Instansi</th>
                    <th class="border border-black p-2 w-20">Nilai Dosen</th>
                    <th class="border border-black p-2 w-20">Nilai Akhir</th>
                    <th class="border border-black p-2 w-16">Grade</th>
                    <th class="border border-black p-2 w-24">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kpList as $idx => $k)
                    @php $pen = $k->penilaian; @endphp
                    <tr>
                        <td class="border border-black p-2 text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="border border-black p-2 text-center font-mono">{{ $k->nim }}</td>
                        <td class="border border-black p-2 font-bold">{{ $k->mahasiswa->nama_lengkap ?? 'N/A' }}</td>
                        <td class="border border-black p-2">{{ $k->instansi }}</td>
                        <td class="border border-black p-2">{{ $k->dosen->nama_dosen ?? '-' }}</td>
                        <td class="border border-black p-2 text-center font-mono">{{ $pen ? number_format($pen->nilai_pembimbing_instansi, 1) : '-' }}</td>
                        <td class="border border-black p-2 text-center font-mono">{{ $pen ? number_format($pen->nilai_dosen_pembimbing, 1) : '-' }}</td>
                        <td class="border border-black p-2 text-center font-mono font-bold">{{ $pen ? number_format($pen->nilai_akhir, 2) : '-' }}</td>
                        <td class="border border-black p-2 text-center font-black text-sm">{{ $pen ? $pen->grade : '-' }}</td>
                        <td class="border border-black p-2 text-center font-semibold text-[11px]">LULUS</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 text-xs text-center mt-12">
            <div>
                <p>Mengetahui,<br><strong>Dekan Fakultas Teknik UIM,</strong></p>
                <div class="h-20"></div>
                <p class="font-bold underline">Dr. H. Akhmad Fauzi, M.Kom.</p>
                <p class="text-[11px] text-slate-600">NIDN. 0715088201</p>
            </div>

            <div>
                <p>Pamekasan, {{ now()->translatedFormat('d F Y') }}<br><strong>Koordinator Kerja Praktik,</strong></p>
                <div class="h-20"></div>
                <p class="font-bold underline">Zainal Abidin, M.T.</p>
                <p class="text-[11px] text-slate-600">NIDN. 0722038502</p>
            </div>
        </div>

    </div>

</body>
</html>

