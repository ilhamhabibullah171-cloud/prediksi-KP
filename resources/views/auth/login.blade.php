<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Kerja Praktik FT UIM</title>
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
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Ambient Gradient Blur Orbs -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-emerald-500/15 blur-3xl"></div>
        <div class="absolute top-1/2 -left-32 w-80 h-80 rounded-full bg-teal-500/15 blur-3xl"></div>
        <div class="absolute -bottom-32 right-1/4 w-96 h-96 rounded-full bg-cyan-500/10 blur-3xl"></div>
    </div>

    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 border border-slate-100 relative z-10"
         x-data="{ roleTab: 'mahasiswa' }">
        
        <!-- Left Side: Branding & 5 Core Features -->
        <div class="bg-gradient-to-br from-emerald-800 to-teal-950 p-8 sm:p-12 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-emerald-500/10 blur-2xl"></div>
            <div class="absolute -left-16 -bottom-16 w-56 h-56 rounded-full bg-teal-400/10 blur-2xl"></div>

            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white text-2xl shadow-inner">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-widest font-semibold text-emerald-300 block">Fakultas Teknik</span>
                        <h2 class="text-lg font-extrabold tracking-tight">Universitas Islam Madura</h2>
                    </div>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight mt-4">
                    Sistem Informasi <span class="text-emerald-400">Kerja Praktik (SIMKP)</span>
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 mt-3 leading-relaxed">
                    Sistem administrasi Kerja Praktik (KP) Fakultas Teknik Universitas Islam Madura
                </p>

                <!-- Fitur Unggulan -->
                <div class="mt-6 space-y-2">
                    <div class="flex items-center space-x-3 text-xs text-emerald-100 bg-white/5 p-2 rounded-xl border border-white/10">
                        <i class="fa-solid fa-file-excel text-emerald-400 w-4 text-center"></i>
                        <span><strong>Admin FT:</strong> Upload template massal XLSX & kelola nilai mahasiswa</span>
                    </div>
                    <div class="flex items-center space-x-3 text-xs text-emerald-100 bg-white/5 p-2 rounded-xl border border-white/10">
                        <i class="fa-solid fa-file-signature text-emerald-400 w-4 text-center"></i>
                        <span><strong>Pendaftaran KP:</strong> Registrasi instansi & jadwal pelaksanaan 30 hari</span>
                    </div>
                    <div class="flex items-center space-x-3 text-xs text-emerald-100 bg-white/5 p-2 rounded-xl border border-white/10">
                        <i class="fa-solid fa-calendar-week text-emerald-400 w-4 text-center"></i>
                        <span><strong>E-Logbook Mingguan:</strong> 7 hari per minggu selama 30 hari</span>
                    </div>
                    <div class="flex items-center space-x-3 text-xs text-emerald-100 bg-white/5 p-2 rounded-xl border border-white/10">
                        <i class="fa-solid fa-award text-emerald-400 w-4 text-center"></i>
                        <span><strong>Penilaian & Sertifikat:</strong> Evaluasi nilai & cetak sertifikat resmi A4</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-emerald-700/50 text-[11px] text-emerald-300/70 flex items-center justify-between">
                <span>&copy; 2026 FT UIM Pamekasan.</span>
                <span class="font-mono text-[10px] bg-white/10 px-2 py-0.5 rounded">SIMKP FT UIM</span>
            </div>
        </div>

        <!-- Right Side: 3 Login Tabs (Mahasiswa, Dosen, Admin FT) -->
        <div class="p-8 sm:p-12 flex flex-col justify-center bg-white">

            <div class="mb-5">
                <h3 class="text-xl font-extrabold text-slate-800">Masuk ke Portal</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pilih peran login Anda di bawah ini:</p>
            </div>

            <!-- 3 Tabs: Mahasiswa, Dosen, Admin FT -->
            <div class="grid grid-cols-3 gap-1.5 p-1.5 bg-slate-100 rounded-2xl mb-6 text-center">
                <button type="button"
                        @click="roleTab = 'mahasiswa'"
                        :class="roleTab === 'mahasiswa' ? 'bg-white text-emerald-800 shadow-sm font-extrabold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="py-2.5 px-2 rounded-xl text-xs transition">
                    <i class="fa-solid fa-user-graduate block mb-1"></i>
                    <span>Mahasiswa</span>
                </button>
                <button type="button"
                        @click="roleTab = 'dosen'"
                        :class="roleTab === 'dosen' ? 'bg-white text-emerald-800 shadow-sm font-extrabold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="py-2.5 px-2 rounded-xl text-xs transition">
                    <i class="fa-solid fa-chalkboard-user block mb-1"></i>
                    <span>Dosen</span>
                </button>
                <button type="button"
                        @click="roleTab = 'admin'"
                        :class="roleTab === 'admin' ? 'bg-white text-emerald-800 shadow-sm font-extrabold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="py-2.5 px-2 rounded-xl text-xs transition">
                    <i class="fa-solid fa-shield-halved block mb-1"></i>
                    <span>Admin FT</span>
                </button>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                
                <!-- Input Username -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5"
                           x-text="roleTab === 'mahasiswa' ? 'NIM (Nomor Induk Mahasiswa)' : (roleTab === 'dosen' ? 'NIDN (Nomor Induk Dosen Nasional)' : 'Username Admin FT')">
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-solid" :class="roleTab === 'mahasiswa' ? 'fa-id-card' : (roleTab === 'dosen' ? 'fa-user-tie' : 'fa-user-gear')"></i>
                        </span>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                               :placeholder="roleTab === 'mahasiswa' ? 'Contoh: 2023020100066' : (roleTab === 'dosen' ? 'Contoh: 0715088201' : 'admin')"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    </div>
                    @error('username')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" required
                               placeholder="Masukkan password akun Anda"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-700/20 hover:shadow-xl transition transform active:scale-[0.99] flex items-center justify-center space-x-2">
                    <span x-text="roleTab === 'mahasiswa' ? 'Masuk sebagai Mahasiswa' : (roleTab === 'dosen' ? 'Masuk sebagai Dosen Pembimbing' : 'Masuk sebagai Admin FT')"></span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Khusus Mahasiswa: Tombol Buat Akun Baru -->
            <div x-show="roleTab === 'mahasiswa'" class="mt-4 p-3 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="text-xs text-emerald-950 text-center sm:text-left">
                    <span class="font-extrabold block">Belum punya akun?</span>
                    <span class="text-[11px] text-emerald-700">Buat akun terlebih dahulu untuk akses SIMKP.</span>
                </div>
                <a href="{{ route('register') }}"
                   class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5 flex-shrink-0">
                    <i class="fa-solid fa-user-plus text-[11px]"></i>
                    <span>Buat Akun Mahasiswa</span>
                </a>
            </div>

            <!-- Petunjuk Akun -->
            <div class="mt-5 pt-3 border-t border-slate-100 text-[11px] text-slate-500 space-y-1">
                <template x-if="roleTab === 'mahasiswa'">
                    <p class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80 text-emerald-900 leading-relaxed">
                        <i class="fa-solid fa-info-circle text-emerald-600 mr-1"></i>
                        Jika sudah punya akun, login menggunakan <strong>Username / NIM</strong> & Password Anda. Jika belum punya akun, silakan klik tombol <strong>Buat Akun Mahasiswa</strong> di atas.
                    </p>
                </template>
                <template x-if="roleTab === 'dosen'">
                    <p class="p-2.5 rounded-xl bg-blue-50/70 border border-blue-200/80 text-blue-900">
                        <i class="fa-solid fa-info-circle text-blue-600 mr-1"></i>
                        Gunakan <strong>NIDN</strong> Anda (Contoh: <code class="font-bold">0715088201</code>) & Password: <code class="font-bold">password123</code>.
                    </p>
                </template>
                <template x-if="roleTab === 'admin'">
                    <p class="p-2.5 rounded-xl bg-purple-50/70 border border-purple-200/80 text-purple-900">
                        <i class="fa-solid fa-info-circle text-purple-600 mr-1"></i>
                        Gunakan Username: <code class="font-bold">admin</code> & Password: <code class="font-bold">admin123</code>.
                    </p>
                </template>
            </div>

        </div>

    </div>

    <script>
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
