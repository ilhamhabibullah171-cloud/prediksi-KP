<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran KP - {{ $mahasiswa->nama_lengkap }} ({{ $mahasiswa->nim }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Times+New+Roman&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        .font-serif-official {
            font-family: 'Times New Roman', Times, serif;
        }
        .page-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 20mm 20mm 20mm 25mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }
        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .page-container {
                width: 100% !important;
                min-height: 100vh !important;
                margin: 0 !important;
                padding: 15mm 15mm 15mm 20mm !important;
                box-shadow: none !important;
                page-break-after: avoid;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 text-slate-900">

    <!-- Floating Action Bar -->
    <div class="no-print fixed top-5 right-5 z-50 flex items-center space-x-3 bg-slate-900/90 backdrop-blur-md p-3 rounded-2xl border border-slate-700 shadow-2xl">
        <button onclick="window.history.back()"
                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold flex items-center space-x-2 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </button>
        <button onclick="window.print()"
                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center space-x-2 shadow-lg shadow-emerald-600/30 transition transform hover:scale-105 active:scale-95">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Formulir (A4)</span>
        </button>
    </div>

    <!-- Halaman Dokumen A4 Portrait -->
    <div class="page-container flex flex-col justify-between">
        
        <div>
            <!-- Kop Surat Resmi FT UIM -->
            <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                <div class="w-20 h-20 rounded-2xl bg-emerald-800 text-white flex items-center justify-center text-3xl font-black shadow-xs flex-shrink-0">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="flex-1 text-center px-4 font-serif-official">
                    <h3 class="text-xs uppercase tracking-wider font-semibold text-slate-700">Yayasan Universitas Islam Madura</h3>
                    <h1 class="text-xl font-bold uppercase tracking-wide text-slate-900 mt-0.5">Universitas Islam Madura</h1>
                    <h2 class="text-lg font-extrabold uppercase text-emerald-800">Fakultas Teknik</h2>
                    <p class="text-[11px] text-slate-600 italic mt-0.5">
                        Program Studi: S-1 Teknik Informatika &bull; S-1 Sistem Informasi
                    </p>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        Jl. Pondok Pesantren Miftahul Ulum Bettet, Pamekasan 69351 - Madura, Jawa Timur | Telp: (0324) 321746
                    </p>
                </div>
                <div class="w-20 text-center flex-shrink-0 flex flex-col items-center">
                    <div class="w-16 h-16 border-2 border-slate-300 rounded-xl p-1 flex items-center justify-center bg-slate-50">
                        <i class="fa-solid fa-qrcode text-3xl text-slate-700"></i>
                    </div>
                    <span class="text-[9px] font-mono text-slate-400 mt-1">VERIFIED</span>
                </div>
            </div>
            <!-- Garis ganda kop -->
            <div class="border-b border-black mt-0.5 mb-6"></div>

            <!-- Judul Formulir -->
            <div class="text-center space-y-1 mb-6 font-serif-official">
                <h2 class="text-base font-bold uppercase tracking-wider underline">
                    FORMULIR PENDAFTARAN KERJA PRAKTIK (KP)
                </h2>
                <p class="text-xs text-slate-600 font-mono">
                    Nomor Registrasi: FT-UIM/KP/{{ date('Y') }}/{{ str_pad($kp->id_kp, 4, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            <!-- Bagian 1: Identitas Mahasiswa -->
            <div class="space-y-3 mb-6">
                <div class="flex items-center space-x-2 border-b border-slate-300 pb-1 font-bold text-xs uppercase tracking-wider text-slate-800">
                    <span class="w-5 h-5 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px]">I</span>
                    <span>Identitas Mahasiswa Pemohon</span>
                </div>
                
                <table class="w-full text-xs leading-relaxed">
                    <tbody>
                        <tr>
                            <td class="py-1 w-44 font-semibold text-slate-700">Nama Lengkap</td>
                            <td class="w-4">:</td>
                            <td class="font-bold text-slate-900 uppercase">{{ $mahasiswa->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Nomor Induk Mahasiswa (NIM)</td>
                            <td>:</td>
                            <td class="font-mono font-bold text-slate-900">{{ $mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Program Studi / Fakultas</td>
                            <td>:</td>
                            <td class="font-medium text-slate-900">{{ $mahasiswa->prodi }} / Fakultas Teknik</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Angkatan Masuk</td>
                            <td>:</td>
                            <td class="text-slate-900">{{ $mahasiswa->angkatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">IPK Terakhir / SKS Lulus</td>
                            <td>:</td>
                            <td class="font-semibold text-emerald-800">
                                IPK {{ number_format($mahasiswa->ipk_terakhir, 2) }} / {{ $mahasiswa->sks_lulus }} SKS (Memenuhi Syarat Akademik KP)
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">No. WhatsApp / HP Aktif</td>
                            <td>:</td>
                            <td class="font-mono text-slate-900">{{ $mahasiswa->no_hp ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian 2: Data Instansi & Rencana KP -->
            <div class="space-y-3 mb-6">
                <div class="flex items-center space-x-2 border-b border-slate-300 pb-1 font-bold text-xs uppercase tracking-wider text-slate-800">
                    <span class="w-5 h-5 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px]">II</span>
                    <span>Rencana Lokasi & Topik Kerja Praktik</span>
                </div>
                
                <table class="w-full text-xs leading-relaxed">
                    <tbody>
                        <tr>
                            <td class="py-1 w-44 font-semibold text-slate-700">Nama Instansi / Perusahaan</td>
                            <td class="w-4">:</td>
                            <td class="font-bold text-slate-900">{{ $kp->instansi ?? 'Belum Ditentukan' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Alamat Lengkap Instansi</td>
                            <td>:</td>
                            <td class="text-slate-900">{{ $kp->alamat_instansi ?? 'Pamekasan, Madura' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Judul / Topik Rencana KP</td>
                            <td>:</td>
                            <td class="font-semibold text-slate-900 leading-snug">
                                {{ $kp->judul_kp ?? 'Pelaksanaan Kegiatan Kerja Praktik Bidang ' . ($mahasiswa->prodi ?? 'Teknologi Informasi') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Kontak Person / PIC Instansi</td>
                            <td>:</td>
                            <td class="text-slate-900">{{ $kp->kontak_instansi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Periode Pelaksanaan (30 Hari)</td>
                            <td>:</td>
                            <td class="font-semibold text-slate-900">
                                {{ $kp->tanggal_mulai ? $kp->tanggal_mulai->format('d F Y') : '-' }} s/d {{ $kp->tanggal_selesai ? $kp->tanggal_selesai->format('d F Y') : '-' }} (30 Hari Kerja)
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian 3: Penetapan Dosen Pembimbing oleh Admin Fakultas -->
            <div class="space-y-3 mb-6">
                <div class="flex items-center space-x-2 border-b border-slate-300 pb-1 font-bold text-xs uppercase tracking-wider text-slate-800">
                    <span class="w-5 h-5 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px]">III</span>
                    <span>Penetapan Dosen Pembimbing (Wewenang Fakultas Teknik)</span>
                </div>
                
                <table class="w-full text-xs leading-relaxed">
                    <tbody>
                        <tr>
                            <td class="py-1 w-44 font-semibold text-slate-700">Dosen Pembimbing KP</td>
                            <td class="w-4">:</td>
                            <td class="font-extrabold text-slate-900 text-sm">
                                @if($kp->dosen)
                                    {{ $kp->dosen->nama_dosen }}
                                @else
                                    <span class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                        Dalam Proses Penetapan oleh Admin Fakultas Teknik
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">NIDN Pembimbing</td>
                            <td>:</td>
                            <td class="font-mono text-slate-900">{{ $kp->dosen->nidn ?? ($kp->nidn ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Status Pengajuan</td>
                            <td>:</td>
                            <td class="font-bold {{ $kp->status_kp === 'Berjalan' ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $kp->status_kp === 'Berjalan' ? 'DISETUJUI & AKTIF (BERJALAN)' : 'PENGAJUAN MASUK FAKULTAS' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 font-semibold text-slate-700">Catatan Koordinator/Admin FT</td>
                            <td>:</td>
                            <td class="italic text-slate-600">{{ $kp->catatan_koordinator ?? 'Menunggu proses verifikasi dan penetapan dosen pembimbing oleh Admin Fakultas Teknik.' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pernyataan Mahasiswa -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-[10px] text-slate-600 leading-relaxed font-serif-official italic">
                "Saya menyatakan bahwa data yang diisikan dalam formulir pendaftaran ini adalah benar dan saya bersedia mematuhi seluruh peraturan pelaksanaan Kerja Praktik di Fakultas Teknik Universitas Islam Madura serta petunjuk dari Dosen Pembimbing yang ditetapkan."
            </div>

        </div>

        <!-- Bagian Pengesahan & Tanda Tangan -->
        <div class="pt-6 font-serif-official">
            <div class="text-right text-xs mb-4">
                Pamekasan, {{ date('d F Y') }}
            </div>

            <div class="grid grid-cols-3 gap-4 text-center text-xs">
                <!-- TTD Mahasiswa -->
                <div class="flex flex-col justify-between h-28">
                    <span>Mahasiswa Pemohon,</span>
                    <div>
                        <strong class="underline block font-bold">{{ $mahasiswa->nama_lengkap }}</strong>
                        <span class="font-mono text-[11px] block">NIM. {{ $mahasiswa->nim }}</span>
                    </div>
                </div>

                <!-- TTD Dosen Pembimbing -->
                <div class="flex flex-col justify-between h-28">
                    <span>Dosen Pembimbing KP,</span>
                    <div>
                        <strong class="underline block font-bold">{{ $kp->dosen->nama_dosen ?? '( .......................................... )' }}</strong>
                        <span class="font-mono text-[11px] block">NIDN. {{ $kp->dosen->nidn ?? '............................' }}</span>
                    </div>
                </div>

                <!-- TTD Koordinator / Admin Fakultas -->
                <div class="flex flex-col justify-between h-28">
                    <span>Mengetahui,<br>Admin / Koordinator KP FT,</span>
                    <div>
                        <strong class="underline block font-bold">Zainal Abidin, M.T.</strong>
                        <span class="font-mono text-[11px] block">NIDN. 0722038502</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

