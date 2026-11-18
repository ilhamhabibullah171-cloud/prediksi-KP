@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')
@section('page_heading', 'Dashboard Mahasiswa KP')

@section('content')
<div class="space-y-8" x-data="mahasiswaDashboard()">

    <!-- Header Mahasiswa -->
    <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-widest font-semibold text-emerald-300">Sistem Informasi Kerja Praktik FT UIM</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">{{ $mahasiswa->nama_lengkap }}</h1>
                <p class="text-xs sm:text-sm text-emerald-100 mt-1">
                    NIM: <strong class="text-white">{{ $mahasiswa->nim }}</strong> &bull; Prodi: {{ $mahasiswa->prodi }} &bull; Angkatan: {{ $mahasiswa->angkatan }} &bull; IPK: {{ number_format($mahasiswa->ipk_terakhir, 2) }} &bull; SKS Lulus: {{ $mahasiswa->sks_lulus }}
                </p>
            </div>
            @if($kp)
                <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/20 text-right">
                    <span class="text-[11px] text-emerald-200 uppercase font-medium block">Status KP</span>
                    <span class="text-sm font-bold text-white flex items-center justify-end space-x-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full {{ $kp->status_kp === 'Berjalan' ? 'bg-emerald-400 animate-pulse' : ($kp->status_kp === 'Selesai' ? 'bg-blue-300' : 'bg-amber-300') }}"></span>
                        <span>{{ $kp->status_kp }}</span>
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Banner Info Hak Akses Mahasiswa (Read-Only) -->
    <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200/80 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-xs flex-shrink-0">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-slate-800">Hak Akses Mahasiswa: Mode Tinjauan Lengkap (Read-Only)</h4>
                <p class="text-[11px] text-slate-600 mt-0.5">Anda dapat memantau seluruh proses pendaftaran, E-Logbook 30 hari, nilai akhir, serta mencetak sertifikat resmi. Perubahan data dikelola oleh Dosen Pembimbing.</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 flex-shrink-0">
            <i class="fa-solid fa-lock text-[9px] mr-1"></i> Data Tervalidasi
        </span>
    </div>

    <!-- 4 Fitur Quick Navigation Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="#fitur-pendaftaran" class="p-3.5 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition text-center group">
            <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold mx-auto mb-1 group-hover:scale-110 transition">1</span>
            <span class="text-xs font-extrabold text-slate-800 block">Form Pendaftaran</span>
            <span class="text-[10px] text-slate-400">{{ $kp ? $kp->status_kp : 'Belum Daftar' }}</span>
        </a>
        <a href="#fitur-logbook" class="p-3.5 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition text-center group">
            <span class="w-7 h-7 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-xs font-bold mx-auto mb-1 group-hover:scale-110 transition">2</span>
            <span class="text-xs font-extrabold text-slate-800 block">E-Logbook 30 Hari</span>
            <span class="text-[10px] text-slate-400">{{ $kp ? $kp->logbook_count . '/30 Hari' : '0/30 Hari' }}</span>
        </a>
        <a href="#fitur-penilaian" class="p-3.5 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition text-center group">
            <span class="w-7 h-7 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold mx-auto mb-1 group-hover:scale-110 transition">3</span>
            <span class="text-xs font-extrabold text-slate-800 block">Penilaian KP</span>
            <span class="text-[10px] text-slate-400">{{ $penilaian ? 'Grade: ' . $penilaian->grade : 'Belum Ada' }}</span>
        </a>
        <a href="#fitur-sertifikat" class="p-3.5 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition text-center group">
            <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold mx-auto mb-1 group-hover:scale-110 transition">4</span>
            <span class="text-xs font-extrabold text-slate-800 block">Cetak Sertifikat</span>
            <span class="text-[10px] text-slate-400">{{ $penilaian ? 'Siap Cetak' : 'Menunggu Nilai' }}</span>
        </a>
    </div>

    <!-- ============================================================== -->
    <!-- FITUR 1: FORM & STATUS PENDAFTARAN KP                          -->
    <!-- ============================================================== -->
    <div id="fitur-pendaftaran" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-base font-bold shadow-md">
                    1
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">Form & Status Pendaftaran KP</h2>
                    <p class="text-xs text-slate-500">Pendaftaran lokasi instansi, jadwal 30 hari, dan dosen pembimbing</p>
                </div>
            </div>
            @if($kp)
                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold {{ $kp->status_kp === 'Berjalan' ? 'bg-emerald-100 text-emerald-800' : ($kp->status_kp === 'Selesai' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                    Status: {{ $kp->status_kp }}
                </span>
            @endif
        </div>

        @if($kp && !empty($kp->instansi))
            <!-- Info Data Pendaftaran KP yang Sedang Aktif -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                    <span class="text-slate-400 block font-medium">Instansi / Perusahaan:</span>
                    <strong class="text-slate-800 text-sm block mt-1">{{ $kp->instansi }}</strong>
                    <p class="text-slate-500 mt-0.5 text-[11px]">{{ $kp->alamat_instansi ?? '-' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                    <span class="text-slate-400 block font-medium">Dosen Pembimbing:</span>
                    @if($kp->dosen)
                        <strong class="text-emerald-800 text-sm block mt-1 font-extrabold">{{ $kp->dosen->nama_dosen }}</strong>
                        <p class="text-slate-500 mt-0.5 text-[11px] font-mono">NIDN: {{ $kp->nidn }}</p>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 mt-1">
                            <i class="fa-solid fa-clock mr-1"></i> Menunggu Penetapan Admin FT
                        </span>
                        <p class="text-slate-400 mt-0.5 text-[10px]">Wewenang Admin Fakultas</p>
                    @endif
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                    <span class="text-slate-400 block font-medium">Jadwal Pelaksanaan (30 Hari):</span>
                    <strong class="text-slate-800 text-sm block mt-1">
                        {{ $kp->tanggal_mulai ? $kp->tanggal_mulai->format('d M Y') : '-' }} s/d {{ $kp->tanggal_selesai ? $kp->tanggal_selesai->format('d M Y') : '-' }}
                    </strong>
                    <p class="text-slate-500 mt-0.5 text-[11px]">Target 30 Hari Kegiatan</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                    <span class="text-slate-400 block font-medium">Catatan / Status:</span>
                    <p class="text-slate-700 italic mt-1 text-[11px] leading-relaxed">{{ $kp->catatan_koordinator ?? 'Pendaftaran dalam proses verifikasi.' }}</p>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <a href="{{ route('mahasiswa.pendaftaran') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-file-lines text-xs"></i>
                    <span>Buka Rincian Formulir</span>
                </a>
                <a href="{{ route('mahasiswa.pendaftaran.cetak') }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-print text-xs"></i>
                    <span>Cetak Formulir Pendaftaran (A4)</span>
                </a>
            </div>
        @else
            <!-- Info Menunggu Penetapan / Belum Isi Formulir -->
            <div class="p-8 text-center text-xs text-amber-800 bg-amber-50/70 rounded-2xl border border-amber-200/80 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mx-auto shadow-xs">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <h4 class="font-extrabold text-sm text-slate-800">Formulir Pendaftaran Kerja Praktik (KP)</h4>
                <p class="max-w-lg mx-auto text-slate-600 leading-relaxed">
                    Silakan isi dan kirimkan template formulir pendaftaran Kerja Praktik Anda. Data pendaftaran akan masuk ke Admin Fakultas Teknik untuk penentuan dan penetapan resmi Dosen Pembimbing KP Anda.
                </p>
                <div>
                    <a href="{{ route('mahasiswa.pendaftaran') }}"
                       class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Buka & Isi Formulir Pendaftaran KP Sekarang</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- FITUR 2: E-LOGBOOK MINGGUAN (7 HARI SELAMA 30 HARI)             -->
    <!-- ============================================================== -->
    <div id="fitur-logbook" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-600 text-white flex items-center justify-center text-base font-bold shadow-md">
                    2
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">E-Logbook Mingguan (7 Hari x 30 Hari)</h2>
                    <p class="text-xs text-slate-500">Terdiri dari 5 Minggu (Minggu 1-4: 7 hari/minggu, Minggu 5: 2 hari penutup)</p>
                </div>
            </div>
            @if($kp && !empty($kp->instansi))
                <div class="flex items-center space-x-2">
                    <a href="{{ route('mahasiswa.logbook') }}" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-expand"></i>
                        <span>Buka E-Logbook Penuh</span>
                    </a>
                </div>
            @endif
        </div>

        @if(!$kp || empty($kp->instansi) || !$kp->tanggal_mulai)
            <div class="p-8 text-center text-xs text-amber-800 bg-amber-50/70 rounded-2xl border border-amber-200/80 space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mx-auto shadow-xs">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <h4 class="font-extrabold text-sm text-slate-800">E-Logbook Belum Aktif</h4>
                <p class="max-w-md mx-auto text-slate-600 leading-relaxed">
                    Laporan E-Logbook mingguan hanya dapat ditambahkan ketika Anda sudah resmi melaksanakan Kerja Praktik di instansi yang ditetapkan oleh Admin Fakultas.
                </p>
            </div>
        @else
            <!-- Logbook Progress Stats -->
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold text-slate-700">
                    <span>Progress: {{ $kp->logbook_count }} dari 30 Hari Terisi</span>
                    <span class="text-emerald-700 font-extrabold">{{ $kp->rasio_logbook }}% Kepatuhan</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-500"
                         style="width: {{ min(100, $kp->rasio_logbook) }}%"></div>
                </div>
            </div>

            <!-- Tab Minggu (Minggu 1 s/d Minggu 5) -->
            <div x-data="{ activeWeek: 1 }">
                <div class="flex items-center space-x-2 overflow-x-auto pb-2 border-b border-slate-100">
                    @foreach($weeks as $wIndex => $w)
                        <button type="button" @click="activeWeek = {{ $wIndex }}"
                                :class="activeWeek === {{ $wIndex }} ? 'bg-emerald-600 text-white font-extrabold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                                class="px-4 py-2 rounded-xl text-xs transition flex-shrink-0 flex items-center space-x-1.5">
                            <span>{{ $w['label'] }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full"
                                  :class="activeWeek === {{ $wIndex }} ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'">
                                {{ $w['filled_count'] }}/{{ $w['total_days'] }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Konten Tiap Minggu -->
                @foreach($weeks as $wIndex => $w)
                    <div x-show="activeWeek === {{ $wIndex }}" class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Jadwal: <strong>{{ $w['hari_range'] }}</strong> ({{ $w['total_days'] }} Hari)</span>
                            <span>Approved: <strong class="text-emerald-600">{{ $w['approved_count'] }}</strong> &bull; Terisi: <strong>{{ $w['filled_count'] }}</strong></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
                            @foreach($w['days'] as $day)
                                @php
                                    $entry = $day['entry'];
                                    $status = $day['status'];
                                @endphp
                                <div class="p-3 rounded-2xl border flex flex-col justify-between text-xs space-y-2
                                    {{ $status === 'Approved' ? 'bg-emerald-50/70 border-emerald-200' : ($status === 'Pending' ? 'bg-amber-50/70 border-amber-200' : ($status === 'Revisi' ? 'bg-orange-50/70 border-orange-200' : ($status === 'Terlewat' ? 'bg-rose-50/70 border-rose-200' : 'bg-slate-50 border-slate-200'))) }}">
                                    <div class="flex items-center justify-between">
                                        <span class="font-extrabold text-slate-800">Hari {{ $day['hari_ke'] }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold
                                            {{ $status === 'Approved' ? 'bg-emerald-200 text-emerald-900' : ($status === 'Pending' ? 'bg-amber-200 text-amber-900' : ($status === 'Revisi' ? 'bg-orange-200 text-orange-900' : ($status === 'Terlewat' ? 'bg-rose-200 text-rose-900' : 'bg-slate-200 text-slate-600'))) }}">
                                            {{ $status }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 block">{{ $day['tanggal']->format('d M Y') }}</span>
                                    
                                    @if($entry)
                                        <p class="text-[11px] text-slate-700 line-clamp-2 leading-tight">
                                            {{ $entry->deskripsi_pekerjaan }}
                                        </p>
                                    @else
                                        <p class="text-[10px] text-slate-400 italic">Belum ada catatan</p>
                                    @endif

                                    <div class="pt-1 border-t border-slate-200/60 flex items-center justify-between">
                                        @if($entry && $entry->dokumentasi)
                                            <a href="{{ route('storage.local', $entry->dokumentasi) }}" target="_blank" class="text-emerald-700 hover:text-emerald-900 text-[10px] font-bold flex items-center space-x-1">
                                                <i class="fa-solid fa-paperclip"></i>
                                                <span>Foto</span>
                                            </a>
                                        @else
                                            <span></span>
                                        @endif

                                        @if($entry && $status === 'Approved')
                                            <button type="button" @click="openLogbookModal({{ json_encode($day) }}, 'view')"
                                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] transition inline-flex items-center space-x-1">
                                                <i class="fa-solid fa-eye text-[9px]"></i>
                                                <span>Lihat Detail</span>
                                            </button>
                                        @elseif($entry)
                                            <button type="button" @click="openLogbookModal({{ json_encode($day) }}, 'edit')"
                                                    class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] transition inline-flex items-center space-x-1 shadow-xs">
                                                <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                                                <span>Edit Laporan</span>
                                            </button>
                                        @else
                                            <button type="button" @click="openLogbookModal({{ json_encode($day) }}, 'create')"
                                                    class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[10px] transition inline-flex items-center space-x-1 shadow-xs">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                                <span>+ Isi Laporan</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- FITUR 3: PENILAIAN KP                                          -->
    <!-- ============================================================== -->
    <div id="fitur-penilaian" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-base font-bold shadow-md">
                    3
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">Penilaian Kerja Praktik</h2>
                    <p class="text-xs text-slate-500">Evaluasi Pembimbing Instansi (40%) dan Dosen Pembimbing (60%)</p>
                </div>
            </div>
            @if($penilaian)
                <a href="{{ route('mahasiswa.penilaian.cetak') }}" target="_blank"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Lembar Nilai Resmi</span>
                </a>
            @endif
        </div>

        @if(!$penilaian)
            <div class="p-8 text-center text-xs text-slate-500 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mx-auto">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <h4 class="font-bold text-slate-700">Penilaian Belum Diterbitkan</h4>
                <p class="max-w-md mx-auto text-slate-400">Dosen Pembimbing akan menginput rubrik penilaian setelah E-Logbook 30 hari selesai dan dievaluasi.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 text-center flex flex-col justify-center">
                    <span class="text-xs text-emerald-700 font-bold uppercase tracking-wider">Grade Kelulusan</span>
                    <div class="text-5xl font-black text-emerald-800 my-2">{{ $penilaian->grade }}</div>
                    <span class="text-xs text-slate-600 font-medium">Nilai Akhir: <strong>{{ number_format($penilaian->nilai_akhir, 2) }}</strong> / 100</span>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs space-y-1.5">
                    <span class="text-slate-400 block font-medium">Nilai Instansi (Bobot 40%):</span>
                    <strong class="text-2xl font-black text-slate-800">{{ number_format($penilaian->nilai_pembimbing_instansi, 2) }}</strong>
                    <p class="text-[11px] text-slate-500">Etika & kontribusi di tempat kerja praktik.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs space-y-1.5">
                    <span class="text-slate-400 block font-medium">Nilai Dosen (Bobot 60%):</span>
                    <strong class="text-2xl font-black text-slate-800">{{ number_format($penilaian->nilai_dosen_pembimbing, 2) }}</strong>
                    <p class="text-[11px] text-slate-500">Rata-rata disiplin, laporan, dan presentasi.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs space-y-1.5">
                    <span class="text-slate-400 block font-medium">Catatan Evaluasi:</span>
                    <p class="text-slate-700 italic text-[11px] mt-1">{{ $penilaian->catatan_evaluasi ?? 'Tidak ada catatan khusus.' }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- FITUR 4: CETAK SERTIFIKAT KP                                   -->
    <!-- ============================================================== -->
    <div id="fitur-sertifikat" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-600 text-white flex items-center justify-center text-base font-bold shadow-md">
                    4
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">Cetak Sertifikat Kerja Praktik</h2>
                    <p class="text-xs text-slate-500">Sertifikat Resmi Fakultas Teknik Universitas Islam Madura (Format A4 Landscape)</p>
                </div>
            </div>
            @if($kp)
                <a href="{{ route('mahasiswa.sertifikat.cetak') }}" target="_blank"
                   class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                    <i class="fa-solid fa-certificate"></i>
                    <span>Cetak Sertifikat KP (A4 PDF)</span>
                </a>
            @endif
        </div>

        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-xs">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900 uppercase">Dokumen Resmi Akademik</span>
                <h3 class="text-base font-extrabold text-slate-800">Sertifikat Kelulusan Kerja Praktik FT UIM</h3>
                <p class="text-slate-600 max-w-xl text-[11px] leading-relaxed">
                    Sertifikat ini memuat identitas mahasiswa, instansi tempat pelaksanaan KP selama 30 hari, nilai akhir dan grade, serta pengesahan bertanda tangan Dekan Fakultas Teknik dan Dosen Pembimbing lengkap dengan kode verifikasi QR.
                </p>
                <div class="text-[11px] text-slate-500 pt-1">
                    Penerima: <strong>{{ $mahasiswa->nama_lengkap }}</strong> (NIM: {{ $mahasiswa->nim }})
                </div>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('mahasiswa.sertifikat.cetak') }}" target="_blank"
                   class="inline-flex flex-col items-center justify-center p-4 bg-white hover:bg-amber-100/50 rounded-2xl border-2 border-dashed border-amber-300 text-amber-900 transition group shadow-xs">
                    <i class="fa-solid fa-file-pdf text-3xl text-amber-600 mb-2 group-hover:scale-110 transition"></i>
                    <span class="text-xs font-bold">Buka Preview & Cetak</span>
                    <span class="text-[10px] text-slate-400">Ukuran A4 Standar</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL DETAIL & INPUT E-LOGBOOK HARIAN                         -->
    <!-- ============================================================== -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 x-show="showModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showModal = false"></div>

            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 space-y-4"
                 x-show="showModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-black text-sm">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-teal-600 tracking-wider"
                                  x-text="activeDay.status === 'Approved' ? 'Detail E-Logbook (Disetujui)' : (activeDay.is_edit ? 'Edit Laporan Logbook' : 'Form Isi E-Logbook')"></span>
                            <h3 class="text-base font-extrabold text-slate-800">
                                Kegiatan Hari ke-<span x-text="activeDay.hari_ke"></span>
                            </h3>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Tampilan 1: Approved (Read-Only) -->
                <template x-if="activeDay.status === 'Approved'">
                    <div class="space-y-4 text-xs">
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-emerald-50 border border-emerald-200/60">
                            <div>
                                <span class="text-emerald-700 block text-[10px] font-bold uppercase">Tanggal Kegiatan</span>
                                <strong class="text-slate-800 text-xs" x-text="activeDay.tanggal_str"></strong>
                            </div>
                            <div class="text-right">
                                <span class="text-emerald-700 block text-[10px] font-bold uppercase">Status</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold inline-block mt-0.5 bg-emerald-200 text-emerald-900">
                                    <i class="fa-solid fa-check text-[9px] mr-1"></i>Approved
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                                Deskripsi Pekerjaan / Aktivitas Mahasiswa:
                            </label>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-xs leading-relaxed max-h-48 overflow-y-auto whitespace-pre-line"
                                 x-text="activeDay.deskripsi || 'Tidak ada deskripsi pekerjaan.'"></div>
                        </div>

                        <template x-if="activeDay.catatan_dosen">
                            <div class="p-3.5 rounded-2xl bg-orange-50 border border-orange-200 text-xs space-y-1">
                                <span class="font-bold text-orange-900 block flex items-center space-x-1">
                                    <i class="fa-solid fa-comment-dots text-orange-600"></i>
                                    <span>Catatan Dosen Pembimbing:</span>
                                </span>
                                <p class="text-orange-800 italic" x-text="activeDay.catatan_dosen"></p>
                            </div>
                        </template>

                        <template x-if="activeDay.dokumentasi_url">
                            <div class="pt-1">
                                <a :href="activeDay.dokumentasi_url" target="_blank"
                                   class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold hover:bg-emerald-100 transition text-xs">
                                    <i class="fa-solid fa-paperclip"></i>
                                    <span>Lihat File Dokumentasi Lampiran</span>
                                </a>
                            </div>
                        </template>

                        <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                            <span class="text-[11px] text-emerald-700 italic font-semibold">Telah disetujui Dosen Pembimbing</span>
                            <button type="button" @click="showModal = false" class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white shadow-xs transition">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Tampilan 2: Input / Edit Form (Belum Approved) -->
                <template x-if="activeDay.status !== 'Approved'">
                    <form method="POST" action="{{ route('mahasiswa.logbook.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                        @csrf
                        <input type="hidden" name="hari_ke" :value="activeDay.hari_ke">

                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-200/60">
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Hari Pelaksanaan</span>
                                <strong class="text-slate-800 text-xs">Hari ke-<span x-text="activeDay.hari_ke"></span></strong>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Status Saat Ini</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold inline-block mt-0.5"
                                      :class="activeDay.status === 'Pending' ? 'bg-amber-100 text-amber-800' : (activeDay.status === 'Revisi' ? 'bg-orange-100 text-orange-800' : 'bg-slate-200 text-slate-700')"
                                      x-text="activeDay.status || 'Belum Diisi'"></span>
                            </div>
                        </div>

                        <template x-if="activeDay.catatan_dosen">
                            <div class="p-3.5 rounded-2xl bg-orange-50 border border-orange-200 text-xs space-y-1">
                                <span class="font-bold text-orange-900 block flex items-center space-x-1">
                                    <i class="fa-solid fa-triangle-exclamation text-orange-600"></i>
                                    <span>Catatan Perbaikan dari Dosen Pembimbing:</span>
                                </span>
                                <p class="text-orange-800 italic" x-text="activeDay.catatan_dosen"></p>
                            </div>
                        </template>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                                Tanggal Kegiatan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_kegiatan" x-model="activeDay.tanggal_str" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                                Deskripsi Pekerjaan / Aktivitas KP <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="deskripsi_pekerjaan" x-model="activeDay.deskripsi" rows="4" required minlength="5" maxlength="2000"
                                      placeholder="Jelaskan secara rinci pekerjaan atau tugas yang Anda selesaikan hari ini di tempat KP..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                            <span class="text-[10px] text-slate-400 block mt-1">Minimal 5 karakter, maksimal 2.000 karakter.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                                Upload File Lampiran / Foto Dokumentasi (Opsional)
                            </label>
                            <input type="file" name="dokumentasi" accept="image/jpeg,image/png,image/jpg,application/pdf"
                                   class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition">
                            <span class="text-[10px] text-slate-400 block mt-1">Format: JPG, PNG, atau PDF (Maks. 2MB).</span>

                            <template x-if="activeDay.dokumentasi_url">
                                <div class="mt-2 text-[11px] text-slate-600">
                                    Dokumen saat ini: <a :href="activeDay.dokumentasi_url" target="_blank" class="text-teal-600 underline font-bold">Lihat Lampiran</a> (unggah file baru bila ingin mengganti)
                                </div>
                            </template>
                        </div>

                        <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                            <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 transition flex items-center space-x-2">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Simpan Laporan Logbook</span>
                            </button>
                        </div>
                    </form>
                </template>

            </div>
        </div>
    </div>

</div>

<script>
function mahasiswaDashboard() {
    return {
        showModal: false,
        activeDay: {
            hari_ke: 1,
            tanggal_str: '{{ date('Y-m-d') }}',
            status: 'Belum Diisi',
            is_edit: false,
            deskripsi: '',
            catatan_dosen: '',
            dokumentasi_url: null,
        },
        openLogbookModal(day, mode) {
            const entry = day.entry || {};
            const rawDate = day.tanggal || '{{ date('Y-m-d') }}';
            let dateStr = '';
            if (typeof rawDate === 'string') {
                dateStr = rawDate.split('T')[0];
            } else if (rawDate && rawDate.date) {
                dateStr = rawDate.date.split(' ')[0];
            } else {
                dateStr = '{{ date('Y-m-d') }}';
            }

            this.activeDay = {
                hari_ke: day.hari_ke,
                tanggal_str: (entry.tanggal_kegiatan ? entry.tanggal_kegiatan.split('T')[0] : dateStr),
                status: day.status || (entry.status_validasi || 'Belum Diisi'),
                is_edit: !!entry.id_logbook,
                deskripsi: entry.deskripsi_pekerjaan || '',
                catatan_dosen: entry.catatan_dosen || '',
                dokumentasi_url: entry.dokumentasi ? '{{ url('/storage') }}/' + entry.dokumentasi : null,
            };
            this.showModal = true;
        }
    }
}
</script>
@endsection
