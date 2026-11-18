<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SIMKP Fakultas Teknik UIM</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        uim: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ============================================================== */
        /* EFEK TRANSISI ZOOM IN / ZOOM OUT KETIKA DIKLIK                 */
        /* ============================================================== */
        
        /* Elemen tombol, link, tab, dan kontrol interaktif */
        button:not(.no-zoom),
        a.btn,
        a[class*="rounded"]:not(table a:not([class*="bg-"]):not([class*="btn"])),
        input[type="submit"],
        input[type="button"],
        nav a,
        .btn-zoom,
        [role="button"] {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease, background-color 0.15s ease !important;
            will-change: transform;
        }

        /* Sedikit membesar saat disentuh/hover (Zoom In awal) */
        button:hover:not(:disabled):not(.no-zoom),
        a.btn:hover:not(.no-zoom),
        a[class*="rounded"]:hover:not(table a:not([class*="bg-"]):not([class*="btn"])):not(.no-zoom),
        nav a:hover:not(.no-zoom),
        [role="button"]:hover:not(.no-zoom) {
            transform: scale(1.025);
        }

        /* Saat tombol ditekan/diklik (:active) - Zoom Out presisi & responsif */
        button:active:not(:disabled):not(.no-zoom),
        a.btn:active:not(.no-zoom),
        a[class*="rounded"]:active:not(.no-zoom),
        input[type="submit"]:active:not(.no-zoom),
        input[type="button"]:active:not(.no-zoom),
        nav a:active:not(.no-zoom),
        [role="button"]:active:not(.no-zoom) {
            transform: scale(0.93) !important;
        }

        /* Animasi Klik Zoom In - Zoom Out (Spring Pulse saat diklik/dilepas) */
        @keyframes clickZoomPulse {
            0% { transform: scale(1); }
            35% { transform: scale(0.92); }
            70% { transform: scale(1.06); }
            100% { transform: scale(1); }
        }

        .zoom-click-anim {
            animation: clickZoomPulse 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }

        /* ============================================================== */
        /* PENGECUALIAN KETAT: BAGIAN DATA TIDAK BOLEH DIBERI TRANSISI   */
        /* (Sesuai instruksi: "kecuali pada bagian datanya tidak usah")   */
        /* ============================================================== */
        table,
        thead,
        tbody,
        tfoot,
        tr,
        th,
        td,
        .data-table,
        .data-row,
        .data-cell,
        .data-text,
        .data-value,
        .no-zoom {
            transform: none !important;
            transition: none !important;
            animation: none !important;
        }

        /* Pastikan teks atau baris di dalam tabel data tidak bergeser */
        table tr:hover {
            transform: none !important;
        }
        table td {
            transform: none !important;
        }

        /* Tombol aksi di dalam baris tabel tetap memiliki efek zoom in/out yang halus saat diklik */
        table td button,
        table td a[class*="bg-"],
        table td a.btn {
            display: inline-flex;
            transform: none;
            transition: transform 0.15s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
        table td button:hover,
        table td a[class*="bg-"]:hover {
            transform: scale(1.05) !important;
        }
        table td button:active,
        table td a[class*="bg-"]:active {
            transform: scale(0.92) !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-full {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
        }

        /* Latar Belakang Gradasi Ambient yang Estetik, Sejuk & Enak Dipandang */
        .mesh-gradient-bg {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.09) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(20, 184, 166, 0.10) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.07) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(16, 185, 129, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(248, 250, 252, 0.5) 0px, transparent 100%),
                linear-gradient(135deg, #f0fdf4 0%, #f8fafc 35%, #f0fdfa 70%, #f8fafc 100%);
            background-attachment: fixed;
            background-size: cover;
        }
    </style>
    @stack('styles')
</head>
<body class="mesh-gradient-bg text-slate-800 antialiased min-h-screen relative selection:bg-emerald-500 selection:text-white" x-data="{ sidebarOpen: false }">

    <!-- Ambient Gradient Blur Accents (Enak Dipandang & Lembut) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden no-print">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-gradient-to-br from-emerald-300/20 to-teal-400/20 blur-3xl"></div>
        <div class="absolute top-1/3 -left-32 w-80 h-80 rounded-full bg-gradient-to-tr from-teal-300/15 to-emerald-200/20 blur-3xl"></div>
        <div class="absolute -bottom-32 right-1/4 w-96 h-96 rounded-full bg-gradient-to-tr from-cyan-300/15 to-emerald-300/15 blur-3xl"></div>
    </div>

    <div class="min-h-screen flex flex-col md:flex-row relative z-10">
        <!-- Sidebar -->
        <aside class="no-print bg-gradient-to-b from-slate-950 via-slate-900 to-emerald-950 text-slate-200 w-full md:w-64 flex-shrink-0 flex flex-col z-30 transition-all duration-300 md:relative fixed inset-y-0 left-0 transform shadow-2xl border-r border-slate-800/80"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            
            <!-- Sidebar Header / Logo -->
            <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-900/30 font-bold text-lg">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-base tracking-tight text-white">SIMKP FT UIM</h1>
                        <p class="text-xs text-emerald-400 font-medium">Fakultas Teknik</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Role Badge -->
            <div class="px-5 py-3 bg-slate-800/60 border-b border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ Auth::user()->role === 'admin' ? 'bg-purple-400 animate-pulse' : (Auth::user()->role === 'dosen' ? 'bg-blue-400' : 'bg-emerald-400') }}"></span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-300">
                        {{ Auth::user()->role }}
                    </span>
                </div>
                <span class="text-xs text-slate-400 font-mono">{{ Auth::user()->username }}</span>
            </div>

            <!-- Nav Links (Hanya 5 Fitur Utama Sesuai Instruksi) -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                @if(Auth::user()->role === 'mahasiswa')
                    <a href="{{ route('mahasiswa.dashboard') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-house-chimney w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('mahasiswa.pendaftaran') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('mahasiswa.pendaftaran*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-file-signature w-5 text-center"></i>
                        <span>1. Form Pendaftaran KP</span>
                    </a>
                    <a href="{{ route('mahasiswa.logbook') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('mahasiswa.logbook*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-calendar-week w-5 text-center"></i>
                        <span>2. E-Logbook Mingguan</span>
                    </a>
                    <a href="{{ route('mahasiswa.penilaian') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('mahasiswa.penilaian*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-award w-5 text-center"></i>
                        <span>3. Penilaian KP</span>
                    </a>
                    <a href="{{ route('mahasiswa.sertifikat.cetak') }}" target="_blank"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('mahasiswa.sertifikat*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-certificate w-5 text-center"></i>
                        <span>4. Cetak Sertifikat KP</span>
                    </a>

                @elseif(Auth::user()->role === 'dosen')
                    <a href="{{ route('dosen.dashboard') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dosen.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('dosen.pendaftaran.index') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dosen.pendaftaran*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-file-signature w-5 text-center"></i>
                        <span>1. Pendaftaran Mahasiswa</span>
                    </a>
                    <a href="{{ route('dosen.logbook.index') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dosen.logbook*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-calendar-week w-5 text-center"></i>
                        <span>2. E-Logbook Mingguan</span>
                    </a>
                    <a href="{{ route('dosen.penilaian.index') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dosen.penilaian*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-award w-5 text-center"></i>
                        <span>3. Penilaian KP</span>
                    </a>
                    <a href="{{ route('dosen.sertifikat.index') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dosen.sertifikat*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-certificate w-5 text-center"></i>
                        <span>4. Cetak Sertifikat KP</span>
                    </a>

                @elseif(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                        <span>Dashboard Admin FT</span>
                    </a>
                    <a href="{{ route('admin.mahasiswa.template-xlsx') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition text-slate-300 hover:bg-slate-800 hover:text-white">
                        <i class="fa-solid fa-file-excel w-5 text-center text-emerald-400"></i>
                        <span>Unduh Template XLSX</span>
                    </a>
                @endif
            </nav>

            <!-- User Footer in Sidebar -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 truncate">
                        <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-slate-200 text-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email ?? Auth::user()->username }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Logout" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg transition hover:bg-slate-800">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header Navbar -->
            <header class="no-print bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-20 shadow-xs">
                <div class="px-4 sm:px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <button @click="sidebarOpen = true" class="md:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-800">@yield('page_heading', 'Dashboard')</h2>
                            <p class="text-xs text-slate-500 hidden sm:block">Sistem Informasi Kerja Praktik (SIMKP) - Fakultas Teknik UIM</p>
                        </div>
                    </div>

                    <!-- Top Action / Badges -->
                    <div class="flex items-center space-x-3">
                        @if(Auth::check() && Auth::user()->role === 'admin')
                            <a href="{{ route('admin.mahasiswa.template-xlsx') }}"
                               class="hidden sm:flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs transition">
                                <i class="fa-solid fa-file-excel text-[11px]"></i>
                                <span>Unduh Template XLSX</span>
                            </a>
                        @elseif(Auth::check() && Auth::user()->role === 'dosen')
                            <a href="{{ route('dosen.pendaftaran.index') }}"
                               class="hidden sm:flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                                <i class="fa-solid fa-user-plus text-[10px]"></i>
                                <span>+ Tambah Data Mahasiswa</span>
                            </a>
                        @elseif(Auth::check() && Auth::user()->role === 'mahasiswa')
                            <a href="{{ route('mahasiswa.logbook') }}"
                               class="hidden sm:flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>+ Tambah Data Logbook</span>
                            </a>
                        @endif

                        <div class="hidden sm:flex items-center space-x-2 text-xs bg-emerald-50 text-emerald-800 px-3 py-1.5 rounded-full border border-emerald-200/60 font-medium">
                            <i class="fa-solid fa-graduation-cap text-emerald-600"></i>
                            <span>SIMKP Online</span>
                        </div>
                        <div class="text-right hidden md:block">
                            <span class="text-xs font-semibold text-slate-700 block">{{ Auth::user()->name }}</span>
                            <span class="text-[11px] text-slate-400 capitalize">{{ Auth::user()->role }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 relative z-10">
                <!-- Alert Notifications -->
                @if(session('success'))
                    <div class="no-print mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-3 shadow-xs">
                        <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-lg"></i>
                        <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="no-print mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start space-x-3 shadow-xs">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 mt-0.5 text-lg"></i>
                        <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="no-print mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start space-x-3 shadow-xs">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5 text-lg"></i>
                        <div class="flex-1">
                            <p class="text-sm font-bold">Harap periksa kembali isian formulir:</p>
                            <ul class="list-disc list-inside text-xs mt-1 space-y-0.5 text-amber-800">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="no-print border-t border-slate-200/70 bg-white/75 backdrop-blur-md py-4 px-6 text-center text-xs text-slate-500 relative z-10">
                <p>&copy; 2026 Fakultas Teknik Universitas Islam Madura (UIM). Sistem Informasi Kerja Praktik & Early Warning System.</p>
            </footer>
        </div>
    </div>

    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Efek Zoom In - Zoom Out dinamis saat elemen interaktif diklik
        document.addEventListener('click', function (e) {
            const target = e.target.closest('button, a, input[type="submit"], input[type="button"], [role="button"], .tab-btn, .clickable-card');
            
            // Pengecualian ketat: abaikan elemen tabel data non-tombol
            if (!target) return;
            if (target.classList.contains('no-zoom')) return;
            if (target.tagName === 'TR' || target.tagName === 'TD' || target.tagName === 'TH' || target.tagName === 'TABLE') return;
            if (target.closest('.no-zoom')) return;

            // Trigger micro-bounce zoom in - zoom out
            target.classList.remove('zoom-click-anim');
            void target.offsetWidth; // Force reflow
            target.classList.add('zoom-click-anim');
            
            setTimeout(function() {
                target.classList.remove('zoom-click-anim');
            }, 320);
        }, { passive: true });
    });
    </script>
</body>
</html>

