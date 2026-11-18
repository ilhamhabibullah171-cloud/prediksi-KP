<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kerja Praktik - {{ $kp->mahasiswa->nama_lengkap ?? 'Mahasiswa' }} ({{ $kp->nim }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
        }
        .font-cinzel {
            font-family: 'Cinzel', serif;
        }
        .font-playfair {
            font-family: 'Playfair Display', serif;
        }

        /* Certificate Dimensions for Standard A4 Landscape */
        .cert-container {
            width: 297mm;
            min-height: 210mm;
            margin: 20px auto;
            position: relative;
            background: #ffffff;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            box-sizing: border-box;
        }

        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .cert-container {
                width: 100% !important;
                min-height: 100vh !important;
                margin: 0 !important;
                box-shadow: none !important;
                page-break-after: avoid;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

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
            <span>Cetak Sertifikat / Unduh PDF</span>
        </button>
    </div>

    <!-- Certificate Sheet -->
    <div class="cert-container p-8 relative flex flex-col justify-between overflow-hidden">
        
        <!-- Outer Decorative Gold/Emerald Border -->
        <div class="absolute inset-4 border-[6px] border-emerald-800 pointer-events-none"></div>
        <div class="absolute inset-6 border-[2px] border-amber-600/80 pointer-events-none"></div>
        <div class="absolute inset-7 border border-emerald-700/40 pointer-events-none"></div>

        <!-- Corner Ornaments -->
        <div class="absolute top-8 left-8 text-amber-600 opacity-80 text-2xl font-cinzel"><i class="fa-solid fa-certificate"></i></div>
        <div class="absolute top-8 right-8 text-amber-600 opacity-80 text-2xl font-cinzel"><i class="fa-solid fa-certificate"></i></div>
        <div class="absolute bottom-8 left-8 text-amber-600 opacity-80 text-2xl font-cinzel"><i class="fa-solid fa-certificate"></i></div>
        <div class="absolute bottom-8 right-8 text-amber-600 opacity-80 text-2xl font-cinzel"><i class="fa-solid fa-certificate"></i></div>

        <!-- Watermark Background -->
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
            <i class="fa-solid fa-graduation-cap text-[380px] text-emerald-900"></i>
        </div>

        <div class="relative z-10 p-6 flex flex-col justify-between h-full">

            <!-- Certificate Header / Kop -->
            <div class="text-center pt-2">
                <div class="flex items-center justify-center space-x-4 mb-2">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-800 text-white flex items-center justify-center text-3xl shadow-md border-2 border-amber-400">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div class="text-left">
                        <h4 class="text-xs uppercase tracking-widest font-extrabold text-amber-700">Yayasan Bina Insan Madura</h4>
                        <h2 class="text-xl font-black tracking-tight text-slate-900 font-cinzel">UNIVERSITAS ISLAM MADURA</h2>
                        <h3 class="text-sm font-bold text-emerald-800 tracking-wide uppercase">FAKULTAS TEKNIK &bull; PROGRAM STUDI {{ strtoupper($kp->mahasiswa->prodi ?? 'Teknik Informatika') }}</h3>
                        <p class="text-[10px] text-slate-500 italic">Jl. Pondok Pesantren Miftahul Ulum Bettet, Pamekasan, Madura - Jawa Timur</p>
                    </div>
                </div>

                <div class="w-3/4 mx-auto my-3 border-t-2 border-amber-600/70"></div>

                <!-- Certificate Title -->
                <h1 class="text-3xl font-extrabold text-emerald-900 tracking-wider font-cinzel uppercase mt-2">
                    SERTIFIKAT KERJA PRAKTIK
                </h1>
                <p class="text-xs font-mono font-semibold text-slate-600 tracking-widest mt-1">
                    NOMOR: FT-UIM/KP/2026/{{ str_pad($kp->id_kp, 4, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            <!-- Body / Recipient Info -->
            <div class="text-center my-auto py-4">
                <p class="text-xs uppercase tracking-widest text-slate-500 font-semibold mb-2">
                    Dekan Fakultas Teknik Universitas Islam Madura menerangkan bahwa:
                </p>

                <!-- Mahasiswa Name -->
                <div class="inline-block relative">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-playfair tracking-wide px-8 py-1 border-b-2 border-emerald-700">
                        {{ strtoupper($kp->mahasiswa->nama_lengkap ?? 'NAMA MAHASISWA') }}
                    </h2>
                </div>
                <p class="text-sm font-bold text-emerald-800 mt-2 font-mono">
                    NIM: {{ $kp->nim }} &bull; Program Studi: {{ $kp->mahasiswa->prodi ?? 'Teknik Informatika' }}
                </p>

                <!-- Description -->
                <div class="max-w-3xl mx-auto mt-4 text-xs sm:text-sm text-slate-700 leading-relaxed">
                    <p>
                        Telah melaksanakan dan menyelesaikan program <strong>Kerja Praktik (KP)</strong> selama 30 hari kalender pada:
                    </p>
                    <p class="text-base font-extrabold text-emerald-950 mt-1 uppercase font-cinzel">
                        {{ $kp->instansi }}
                    </p>
                    @if($kp->alamat_instansi)
                        <p class="text-[11px] text-slate-500">{{ $kp->alamat_instansi }}</p>
                    @endif
                    <p class="mt-2 text-xs text-slate-600">
                        Terhitung sejak tanggal <strong>{{ \Carbon\Carbon::parse($kp->tanggal_mulai)->translatedFormat('d F Y') }}</strong> sampai dengan <strong>{{ \Carbon\Carbon::parse($kp->tanggal_selesai)->translatedFormat('d F Y') }}</strong>.
                    </p>
                </div>

                <!-- Predikat / Grade Badge -->
                <div class="mt-4 flex items-center justify-center space-x-3">
                    <div class="inline-flex items-center space-x-2 bg-emerald-50 border border-emerald-300 px-5 py-2 rounded-xl text-emerald-900 font-bold text-xs shadow-xs">
                        <i class="fa-solid fa-award text-amber-600 text-base"></i>
                        <span>PREDIKAT KELULUSAN: 
                            <strong class="text-emerald-700 text-sm">
                                @if($penilaian && $penilaian->grade)
                                    LULUS - GRADE {{ $penilaian->grade }} (Nilai: {{ $penilaian->nilai_akhir }})
                                @else
                                    LULUS DENGAN PREDIKAT BAIK
                                @endif
                            </strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="pt-4 grid grid-cols-3 gap-6 text-center text-xs">
                
                <!-- Left: Dekan Fakultas Teknik -->
                <div>
                    <p class="text-[11px] text-slate-500">Pamekasan, {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-slate-800 uppercase mt-1">Dekan Fakultas Teknik UIM,</p>
                    <div class="h-16 flex items-center justify-center my-1 relative">
                        <!-- Digital Seal & Signature -->
                        <div class="w-14 h-14 rounded-full border-2 border-emerald-800/40 text-emerald-800 flex items-center justify-center text-xs font-bold uppercase rotate-12 opacity-80">
                            FT UIM
                        </div>
                        <span class="absolute font-serif italic text-emerald-900/60 text-lg font-bold">Harun, M.T.</span>
                    </div>
                    <p class="font-extrabold text-slate-900 underline">Dr. H. Mohammad Harun, M.T.</p>
                    <p class="text-[10px] text-slate-500 font-mono">NIDN: 0715087501</p>
                </div>

                <!-- Center: Official Seal / QR -->
                <div class="flex flex-col items-center justify-center my-auto">
                    <div class="w-20 h-20 rounded-2xl border border-amber-600/50 bg-amber-50/50 flex flex-col items-center justify-center p-2 text-center shadow-xs">
                        <i class="fa-solid fa-qrcode text-3xl text-emerald-900 mb-1"></i>
                        <span class="text-[9px] font-mono text-slate-600 font-semibold tracking-tighter uppercase">VERIFIED FT-UIM</span>
                    </div>
                    <span class="text-[9px] text-slate-400 mt-1 italic">Sistem Informasi KP FT UIM</span>
                </div>

                <!-- Right: Dosen Pembimbing KP -->
                <div>
                    <p class="text-[11px] text-slate-500">Mengetahui,</p>
                    <p class="font-bold text-slate-800 uppercase mt-1">Dosen Pembimbing KP,</p>
                    <div class="h-16 flex items-center justify-center my-1">
                        <span class="font-serif italic text-emerald-900/60 text-lg font-bold">
                            {{ substr($kp->dosen->nama_dosen ?? 'Pembimbing', 0, 15) }}
                        </span>
                    </div>
                    <p class="font-extrabold text-slate-900 underline">
                        {{ $kp->dosen->nama_dosen ?? 'Dosen Pembimbing KP' }}
                    </p>
                    <p class="text-[10px] text-slate-500 font-mono">
                        NIDN: {{ $kp->nidn ?? '-' }}
                    </p>
                </div>

            </div>

        </div>

    </div>

</body>
</html>

