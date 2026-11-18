@extends('layouts.app')

@section('title', 'Dashboard Dosen Pembimbing')
@section('page_heading', 'Dashboard Dosen Pembimbing KP')

@section('content')
<div class="space-y-8" x-data="{ showModal: false }">

    <!-- Header Dosen Pembimbing -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-widest font-semibold text-emerald-400">Portal Dosen Pembimbing Kerja Praktik FT UIM</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">{{ $dosen->nama_dosen }}</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    NIDN: <strong class="text-white">{{ $dosen->nidn }}</strong> &bull; Kapasitas: {{ $bimbinganList->where('status_kp', 'Berjalan')->count() }} / {{ $dosen->kuota_bimbingan }} Mahasiswa Aktif
                </p>
            </div>

            <button type="button" @click="showModal = true"
                    class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg transition flex items-center space-x-2">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>+ Tambah Data Mahasiswa KP</span>
            </button>
        </div>
    </div>

    <!-- 4 Fitur Utama Quick Shortcuts -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Fitur 1 -->
        <a href="{{ route('dosen.pendaftaran.index') }}"
           class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition text-center group">
            <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-black mx-auto mb-1.5 group-hover:scale-110 transition">1</span>
            <span class="text-xs font-extrabold text-slate-800 block">Pendaftaran KP</span>
            <span class="text-[10px] text-slate-400">{{ $bimbinganList->count() }} Mahasiswa</span>
        </a>

        <!-- Fitur 2 -->
        <a href="{{ route('dosen.logbook.index') }}"
           class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition text-center group">
            <span class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-xs font-black mx-auto mb-1.5 group-hover:scale-110 transition">2</span>
            <span class="text-xs font-extrabold text-slate-800 block">E-Logbook 30 Hari</span>
            <span class="text-[10px] {{ $pendingLogbookCount > 0 ? 'text-amber-600 font-bold' : 'text-slate-400' }}">
                {{ $pendingLogbookCount }} Perlu Validasi
            </span>
        </a>

        <!-- Fitur 3 -->
        <a href="{{ route('dosen.penilaian.index') }}"
           class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition text-center group">
            <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black mx-auto mb-1.5 group-hover:scale-110 transition">3</span>
            <span class="text-xs font-extrabold text-slate-800 block">Penilaian KP</span>
            <span class="text-[10px] text-slate-400">{{ $belumDinilaiCount }} Belum Dinilai</span>
        </a>

        <!-- Fitur 4 -->
        <a href="{{ route('dosen.sertifikat.index') }}"
           class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-md transition text-center group">
            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-black mx-auto mb-1.5 group-hover:scale-110 transition">4</span>
            <span class="text-xs font-extrabold text-slate-800 block">Cetak Sertifikat</span>
            <span class="text-[10px] text-slate-400">{{ $siapSertifikatCount }} Siap Cetak</span>
        </a>
    </div>

    <!-- Tabel Mahasiswa Bimbingan & Monitoring 5 Fitur Inti -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-800 flex items-center space-x-2">
                    <i class="fa-solid fa-users text-emerald-600"></i>
                    <span>Daftar Mahasiswa Bimbingan KP</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola pendaftaran, review logbook mingguan, input nilai, dan cetak sertifikat KP</p>
            </div>
            <button type="button" @click="showModal = true"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-1.5 flex-shrink-0">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Mahasiswa</span>
            </button>
        </div>

        @if($bimbinganList->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                Belum ada data mahasiswa bimbingan. Klik tombol <strong>+ Tambah Mahasiswa</strong> di atas untuk mendaftarkan mahasiswa bimbingan Anda.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3.5 px-4">1. Mahasiswa & Status</th>
                            <th class="py-3.5 px-4">Instansi & Jadwal</th>
                            <th class="py-3.5 px-4 text-center">2. E-Logbook 30 Hari</th>
                            <th class="py-3.5 px-4 text-center">3. Nilai KP</th>
                            <th class="py-3.5 px-4 text-right">4. Aksi & Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($bimbinganList as $kp)
                            @php
                                $pendingReview = $kp->logbooks->where('status_validasi', 'Pending')->count();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- 1. Mahasiswa & Status KP -->
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-slate-800 text-sm">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</div>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        <span class="text-slate-400 font-mono text-[11px]">NIM: {{ $kp->nim }}</span>
                                        @if(($kp->mahasiswa->prodi ?? '') === 'Sistem Informasi')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                SI
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-700 border border-teal-200">
                                                TI
                                            </span>
                                        @endif
                                    </div>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $kp->status_kp === 'Berjalan' ? 'bg-emerald-100 text-emerald-800' : ($kp->status_kp === 'Selesai' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $kp->status_kp }}
                                    </span>
                                </td>

                                <!-- Instansi & Periode -->
                                <td class="py-3.5 px-4">
                                    @if($kp->instansi)
                                        <div class="font-semibold text-slate-800">{{ $kp->instansi }}</div>
                                        <span class="text-slate-500 text-[11px] block">
                                            {{ $kp->tanggal_mulai ? $kp->tanggal_mulai->format('d/m/Y') : '-' }} s/d {{ $kp->tanggal_selesai ? $kp->tanggal_selesai->format('d/m/Y') : '-' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <i class="fa-solid fa-clock text-[9px] mr-1"></i> Menunggu Penetapan Admin
                                        </span>
                                        <span class="text-slate-400 text-[10px] block mt-0.5">Jadwal belum ditentukan</span>
                                    @endif
                                </td>

                                <!-- 2. Progress Logbook -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-1.5 font-bold text-slate-800">
                                        <span>{{ $kp->logbook_count }} / 30 Hari</span>
                                        <span class="text-[10px] text-slate-400">({{ $kp->rasio_logbook }}%)</span>
                                    </div>
                                    <div class="w-24 bg-slate-100 rounded-full h-1.5 mx-auto mt-1 overflow-hidden">
                                        <div class="bg-emerald-500 h-full" style="width: {{ min(100, $kp->rasio_logbook) }}%"></div>
                                    </div>
                                    @if($pendingReview > 0)
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                            {{ $pendingReview }} Perlu Validasi
                                        </span>
                                    @endif
                                </td>

                                <!-- 3. Nilai Akhir -->
                                <td class="py-3.5 px-4 text-center">
                                    @if($kp->penilaian)
                                        <span class="inline-block w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 font-extrabold text-sm leading-7">
                                            {{ $kp->penilaian->grade }}
                                        </span>
                                        <span class="block text-[10px] text-slate-500 font-mono">{{ number_format($kp->penilaian->nilai_akhir, 1) }}</span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum Dinilai</span>
                                    @endif
                                </td>

                                <!-- 4. Aksi & Cetak -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <!-- Review Logbook -->
                                        <a href="{{ route('dosen.logbook.detail', $kp->id_kp) }}"
                                           title="Review & Validasi E-Logbook 30 Hari"
                                           class="px-2.5 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 rounded-xl font-bold transition flex items-center space-x-1 text-[11px]">
                                            <i class="fa-solid fa-calendar-check text-xs"></i>
                                            <span>Logbook</span>
                                        </a>

                                        <!-- Form Penilaian -->
                                        <a href="{{ route('dosen.penilaian.form', $kp->id_kp) }}"
                                           title="Input Nilai KP"
                                           class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition flex items-center space-x-1 text-[11px] shadow-xs">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            <span>Nilai</span>
                                        </a>

                                        <!-- Cetak Sertifikat Resmi -->
                                        <a href="{{ route('dosen.sertifikat.cetak', $kp->id_kp) }}" target="_blank"
                                           title="Cetak Sertifikat KP Resmi A4"
                                           class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl font-bold transition flex items-center space-x-1 text-[11px]">
                                            <i class="fa-solid fa-certificate text-xs"></i>
                                            <span>Sertifikat</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- MODAL TAMBAH DATA MAHASISWA KP (FITUR TAMBAHKAN DATA) -->
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

            <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 space-y-4"
                 x-show="showModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-800">Tambah Data Mahasiswa KP Bimbingan</h3>
                    </div>
                    <button type="button" @click="showModal = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('dosen.pendaftaran.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                NIM Mahasiswa <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nim" required placeholder="Contoh: 21063110001"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nama Lengkap Mahasiswa <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_lengkap" required placeholder="Nama lengkap sesuai KTM"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Program Studi <span class="text-rose-500">*</span>
                            </label>
                            <select name="prodi" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                                <option value="Teknik Informatika" selected>Teknik Informatika</option>
                                <option value="Sistem Informasi">Sistem Informasi</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Angkatan <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="angkatan" value="2022" required min="2018" max="2030"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                IPK Terakhir <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="ipk_terakhir" value="3.50" required min="0" max="4.00"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                SKS Lulus <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="sks_lulus" value="95" required min="75" max="160"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Status KP Awal <span class="text-rose-500">*</span>
                            </label>
                            <select name="status_kp" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                                <option value="Berjalan" selected>Berjalan (Siap Isi E-Logbook)</option>
                                <option value="Pengajuan">Pengajuan</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Instansi / Perusahaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="instansi" required placeholder="Contoh: Dinas Kominfo Pamekasan"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Alamat Instansi
                            </label>
                            <input type="text" name="alamat_instansi" placeholder="Contoh: Jl. Jokotole No. 12, Pamekasan"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Mulai Pelaksanaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Selesai (30 Hari)
                            </label>
                            <input type="date" name="tanggal_selesai" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow transition flex items-center space-x-1.5">
                            <i class="fa-solid fa-save"></i>
                            <span>Simpan Mahasiswa Bimbingan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
