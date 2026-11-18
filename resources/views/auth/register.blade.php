 <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun Mahasiswa KP - FT UIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Transisi Zoom In / Zoom Out saat elemen interaktif diklik */
        button, a, input[type="submit"] {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease !important;
            will-change: transform;
        }
        button:hover, a:hover {
            transform: scale(1.025);
        }
        button:active, a:active, input[type="submit"]:active {
            transform: scale(0.93) !important;
        }
        @keyframes clickZoomPulse {
            0% { transform: scale(1); }
            35% { transform: scale(0.92); }
            70% { transform: scale(1.06); }
            100% { transform: scale(1); }
        }
        .zoom-click-anim {
            animation: clickZoomPulse 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 min-h-screen flex items-center justify-center p-4 py-8 relative overflow-hidden">
    <!-- Ambient Gradient Blur Orbs -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="absolute top-1/2 -left-32 w-80 h-80 rounded-full bg-teal-500/15 blur-3xl"></div>
        <div class="absolute -bottom-32 right-1/4 w-96 h-96 rounded-full bg-cyan-500/10 blur-3xl"></div>
    </div>

    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 border border-slate-100 relative z-10"
         x-data="registerForm()">
        
        <!-- Left Side: Branding & Info -->
        <div class="bg-gradient-to-br from-emerald-800 via-teal-900 to-emerald-950 p-8 sm:p-12 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-emerald-500/10 blur-2xl"></div>
            <div class="absolute -left-16 -bottom-16 w-56 h-56 rounded-full bg-teal-400/10 blur-2xl"></div>

            <div class="relative z-10">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white text-2xl shadow-inner">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-widest font-semibold text-emerald-300 block">Fakultas Teknik</span>
                        <h2 class="text-lg font-extrabold tracking-tight">Universitas Islam Madura</h2>
                    </div>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight mt-4">
                    Buat Akun <span class="text-emerald-400">Mahasiswa KP</span>
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 mt-3 leading-relaxed">
                    Daftarkan akun resmi Anda untuk mengakses seluruh proses Kerja Praktik (KP), mulai dari pemantauan data pendaftaran, validasi E-Logbook 30 hari, nilai akhir dosen pembimbing, hingga cetak sertifikat resmi.
                </p>

                <!-- Syarat & Kemudahan Registrasi -->
                <div class="mt-6 space-y-2.5">
                    <div class="flex items-start space-x-3 text-xs text-emerald-100 bg-white/5 p-2.5 rounded-xl border border-white/10">
                        <i class="fa-solid fa-id-card text-emerald-400 mt-0.5 w-4 text-center"></i>
                        <span><strong>NIM Resmi:</strong> Gunakan NIM aktif yang terdaftar di Fakultas Teknik UIM.</span>
                    </div>
                    <div class="flex items-start space-x-3 text-xs text-emerald-100 bg-white/5 p-2.5 rounded-xl border border-white/10">
                        <i class="fa-solid fa-graduation-cap text-emerald-400 mt-0.5 w-4 text-center"></i>
                        <span><strong>2 Pilihan Prodi:</strong> Tersedia untuk Teknik Informatika & Sistem Informasi.</span>
                    </div>
                    <div class="flex items-start space-x-3 text-xs text-emerald-100 bg-white/5 p-2.5 rounded-xl border border-white/10">
                        <i class="fa-solid fa-key text-emerald-400 mt-0.5 w-4 text-center"></i>
                        <span><strong>Login Praktis:</strong> Setelah akun dibuat, Anda bisa langsung login dengan NIM/Username dan Password pilihan Anda.</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-emerald-700/50 text-[11px] text-emerald-300/70 flex items-center justify-between relative z-10">
                <span>Sudah punya akun?</span>
                <a href="{{ route('login') }}" class="font-bold text-white hover:text-emerald-300 underline flex items-center space-x-1">
                    <span>Masuk ke Portal Login</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Right Side: Registration Form -->
        <div class="p-8 sm:p-10 flex flex-col justify-center bg-white overflow-y-auto max-h-[90vh]">
            
            <div class="mb-5">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Registrasi Mandiri</span>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">Formulir Pendaftaran Akun</h3>
                <p class="text-xs text-slate-500 mt-0.5">Isi data akademik Anda dengan benar:</p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                    <p class="font-bold flex items-center space-x-1.5 mb-1 text-rose-900">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>Periksa kembali data yang dimasukkan:</span>
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-3.5">
                @csrf

                <!-- NIM -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        NIM (Nomor Induk Mahasiswa) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-solid fa-id-card text-xs"></i>
                        </span>
                        <input type="text" name="nim" x-model="nim" @input="detectProdi()" required
                               value="{{ old('nim') }}"
                               placeholder="Contoh: 2023020100066"
                               class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Lengkap Mahasiswa <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-solid fa-user text-xs"></i>
                        </span>
                        <input type="text" name="nama_lengkap" required
                               value="{{ old('nama_lengkap') }}"
                               placeholder="Nama lengkap sesuai KTM / SIAKAD"
                               class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <!-- Program Studi & Angkatan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Program Studi <span class="text-rose-500">*</span>
                        </label>
                        <select name="prodi" x-model="prodi" required
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Angkatan <span class="text-rose-500">*</span>
                        </label>
                        <select name="angkatan" required
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            @foreach([2020, 2021, 2022, 2023, 2024, 2025] as $thn)
                                <option value="{{ $thn }}" {{ old('angkatan', 2023) == $thn ? 'selected' : '' }}>Angkatan {{ $thn }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Username (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Username Login <span class="text-slate-400 font-normal">(Opsional, otomatis NIM jika kosong)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-solid fa-at text-xs"></i>
                        </span>
                        <input type="text" name="username" value="{{ old('username') }}"
                               :placeholder="nim ? nim : 'Default sama dengan NIM'"
                               class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <!-- Password & Konfirmasi Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                   placeholder="Minimal 6 karakter"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Ulangi Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" required
                                   placeholder="Ketik ulang password"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600 pt-0.5">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" x-model="showPassword" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-[11px]">Tampilkan Karakter Password</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-700/20 hover:shadow-xl transition transform active:scale-[0.99] flex items-center justify-center space-x-2 mt-2">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Daftar & Buat Akun Mahasiswa Sekarang</span>
                </button>
            </form>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-extrabold text-emerald-700 hover:text-emerald-800 hover:underline">
                        Masuk Menggunakan Username & Password
                    </a>
                </p>
            </div>

        </div>

    </div>

    <script>
        function registerForm() {
            return {
                nim: '{{ old('nim', '') }}',
                prodi: '{{ old('prodi', 'Teknik Informatika') }}',
                showPassword: false,
                detectProdi() {
                    const cleanNim = this.nim.trim();
                    if (cleanNim.includes('0202')) {
                        this.prodi = 'Sistem Informasi';
                    } else if (cleanNim.includes('0201')) {
                        this.prodi = 'Teknik Informatika';
                    }
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (e) {
                const target = e.target.closest('button, a, input[type="submit"]');
                if (!target) return;
                target.classList.remove('zoom-click-anim');
                void target.offsetWidth;
                target.classList.add('zoom-click-anim');
                setTimeout(function() {
                    target.classList.remove('zoom-click-anim');
                }, 320);
            }, { passive: true });
        });
    </script>
</body>
</html>

