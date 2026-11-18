<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa Kerja Praktik (KP) - FT UIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 min-h-screen py-10 px-4 flex items-center justify-center">

    <div class="max-w-3xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-emerald-800 to-teal-900 p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-emerald-500/10 blur-xl"></div>
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white text-2xl shadow-inner">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-widest font-semibold text-emerald-300 block">Fakultas Teknik</span>
                        <h2 class="text-base font-extrabold tracking-tight">Universitas Islam Madura</h2>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-sm border border-white/20 transition flex items-center space-x-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Masuk / Login</span>
                </a>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight">
                Formulir Pendaftaran Mahasiswa KP
            </h1>
            <p class="text-xs sm:text-sm text-emerald-100/90 mt-2">
                Silakan isi data akademik mahasiswa dan detail tempat pelaksanaan Kerja Praktik 30 hari kalender.
            </p>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-10">

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center space-x-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                    <p class="font-bold flex items-center space-x-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                        <span>Harap periksa kembali isian formulir:</span>
                    </p>
                    <ul class="list-disc list-inside mt-2 space-y-1 text-amber-800">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pendaftaran-kp.store') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Data Akademik Mahasiswa -->
                <div>
                    <h3 class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider mb-4 flex items-center space-x-2">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>1. Data Akademik Mahasiswa</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                NIM (Nomor Induk Mahasiswa) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nim" value="{{ old('nim') }}" required placeholder="Contoh: 21063110001"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-[11px] text-slate-400 mt-1 block">NIM ini sekaligus akan menjadi username login Anda.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Masukkan nama lengkap sesuai KRS"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Angkatan Masuk <span class="text-rose-500">*</span>
                            </label>
                            <select name="angkatan" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach([2020, 2021, 2022, 2023, 2024, 2025] as $thn)
                                    <option value="{{ $thn }}" {{ old('angkatan', 2021) == $thn ? 'selected' : '' }}>Angkatan {{ $thn }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    IPK Terakhir <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" max="4.00" name="ipk_terakhir" value="{{ old('ipk_terakhir', '3.45') }}" required
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    SKS Lulus <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" min="0" max="160" name="sks_lulus" value="{{ old('sks_lulus', '105') }}" required
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6"></div>

                <!-- Section 2: Detail Tempat & Jadwal KP -->
                <div>
                    <h3 class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider mb-4 flex items-center space-x-2">
                        <i class="fa-solid fa-building"></i>
                        <span>2. Lokasi & Jadwal Pelaksanaan KP</span>
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Instansi / Perusahaan Tempat KP <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="instansi" value="{{ old('instansi') }}" required placeholder="Contoh: Dinas Komunikasi dan Informatika (Diskominfo) Kab. Pamekasan"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Lengkap Instansi <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="alamat_instansi" rows="2" required placeholder="Jl. Raya Kabupaten No. ..., Pamekasan"
                                      class="w-full px-4 py-2 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('alamat_instansi') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Mulai KP <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Target Selesai KP (Otomatis 30 Hari)
                                </label>
                                <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 font-medium flex items-center justify-between">
                                    <span id="target_selesai_label">30 Hari Kalender sejak Mulai</span>
                                    <i class="fa-solid fa-calendar-check text-emerald-600"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Pilihan Dosen Pembimbing KP <span class="text-rose-500">*</span>
                            </label>
                            <select name="nidn" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- Pilih Dosen Pembimbing --</option>
                                @foreach($dosenList as $d)
                                    <option value="{{ $d->nidn }}" {{ old('nidn') == $d->nidn ? 'selected' : '' }}>
                                        {{ $d->nama_dosen }} (NIDN: {{ $d->nidn }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-slate-400 mt-1 block">Dosen pembimbing yang dipilih akan memvalidasi E-Logbook dan memberikan penilaian akhir.</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6"></div>

                <!-- Action Button -->
                <button type="submit"
                        class="w-full py-3.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-700/20 hover:shadow-xl transition transform active:scale-[0.99] flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Formulir Pendaftaran Mahasiswa KP</span>
                </button>

                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-emerald-700 font-medium">
                        Sudah pernah mendaftar? <span class="underline font-bold text-emerald-600">Masuk ke Akun Anda di sini</span>
                    </a>
                </div>
            </form>

        </div>

    </div>

</body>
</html>

