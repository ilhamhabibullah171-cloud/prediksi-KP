@extends('layouts.app')

@section('title', 'Pendaftaran Mahasiswa KP - Dosen Pembimbing')
@section('page_heading', 'Pendaftaran Mahasiswa Kerja Praktik')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">

    <!-- Hero Card -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-emerald-500/10 blur-xl"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-widest font-semibold text-emerald-300 block">Modul Dosen Pembimbing</span>
                <h1 class="text-xl sm:text-2xl font-extrabold mt-1">Daftar Pendaftaran Mahasiswa KP</h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 mt-1 max-w-2xl">
                    Kelola data mahasiswa bimbingan Anda. Tambahkan mahasiswa baru secara langsung atau setujui pengajuan pendaftaran mandiri mahasiswa.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Tombol Tambah Data -->
                <button type="button" @click="showModal = true"
                        class="px-5 py-3 rounded-2xl bg-white text-emerald-950 font-extrabold text-xs shadow-lg shadow-black/10 hover:bg-emerald-50 transition transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                    <i class="fa-solid fa-user-plus text-emerald-700 text-sm"></i>
                    <span>+ Tambah Data Mahasiswa</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Table of Students -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-users text-emerald-600"></i>
                <span>Data Mahasiswa Bimbingan Anda ({{ $pendaftaranList->count() }} Orang)</span>
            </h3>

            <button type="button" @click="showModal = true"
                    class="px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition flex items-center space-x-1.5">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Data</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Mahasiswa</th>
                        <th class="py-3.5 px-4">Instansi Tempat KP</th>
                        <th class="py-3.5 px-4">Periode (30 Hari)</th>
                        <th class="py-3.5 px-4 text-center">Akademik</th>
                        <th class="py-3.5 px-4 text-center">Status KP</th>
                        <th class="py-3.5 px-4 text-center">Aksi / Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendaftaranList as $idx => $kp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4 text-center font-medium text-slate-400">{{ $idx + 1 }}</td>
                            
                            <!-- Mahasiswa info -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800 text-sm">
                                    {{ $kp->mahasiswa->nama_lengkap ?? 'Tanpa Nama' }}
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    <span class="text-[11px] font-mono text-slate-400">
                                        NIM: {{ $kp->nim }} &bull; Angkatan {{ $kp->mahasiswa->angkatan ?? '-' }}
                                    </span>
                                    @if(($kp->mahasiswa->prodi ?? '') === 'Sistem Informasi')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                            Sistem Informasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-700 border border-teal-200">
                                            Teknik Informatika
                                        </span>
                                    @endif
                                </div>
                                @if($kp->judul_kp)
                                    <div class="text-[11px] text-slate-600 mt-1 line-clamp-1 italic max-w-xs" title="{{ $kp->judul_kp }}">
                                        <i class="fa-solid fa-bookmark text-[9px] text-emerald-600 mr-1"></i>
                                        {{ $kp->judul_kp }}
                                    </div>
                                @endif
                            </td>

                            <!-- Instansi -->
                            <td class="py-4 px-4">
                                @if($kp->instansi)
                                    <div class="font-semibold text-slate-800">
                                        {{ $kp->instansi }}
                                    </div>
                                    @if($kp->alamat_instansi)
                                        <div class="text-[11px] text-slate-400 truncate max-w-xs mt-0.5">
                                            {{ $kp->alamat_instansi }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-clock text-[9px] mr-1"></i> Menunggu Penetapan Admin
                                    </span>
                                @endif
                            </td>

                            <!-- Periode -->
                            <td class="py-4 px-4">
                                @if($kp->tanggal_mulai && $kp->tanggal_selesai)
                                    <div class="text-slate-700 font-medium">
                                        {{ \Carbon\Carbon::parse($kp->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($kp->tanggal_selesai)->format('d M Y') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        30 Hari Kalender
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum dijadwalkan</span>
                                @endif
                            </td>

                            <!-- Akademik -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono font-semibold text-[11px]">
                                    IPK: {{ number_format($kp->mahasiswa->ipk_terakhir ?? 0, 2) }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $kp->mahasiswa->sks_lulus ?? 0 }} SKS
                                </div>
                            </td>

                            <!-- Status KP -->
                            <td class="py-4 px-4 text-center">
                                @if($kp->status_kp === 'Pengajuan')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-[11px] border border-amber-200">
                                        <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                                        <span>Pengajuan</span>
                                    </span>
                                @elseif($kp->status_kp === 'Berjalan')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px] border border-emerald-200">
                                        <i class="fa-solid fa-circle-play text-[10px]"></i>
                                        <span>Berjalan</span>
                                    </span>
                                @elseif($kp->status_kp === 'Selesai')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-bold text-[11px] border border-blue-200">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>Selesai</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-bold text-[11px] border border-rose-200">
                                        <i class="fa-solid fa-ban text-[10px]"></i>
                                        <span>Batal</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    @if($kp->status_kp === 'Pengajuan')
                                        <form method="POST" action="{{ route('dosen.pendaftaran.status', $kp->id_kp) }}">
                                            @csrf
                                            <input type="hidden" name="status_kp" value="Berjalan">
                                            <button type="submit" onclick="return confirm('Setujui pendaftaran mahasiswa ini agar dapat mulai mengisi E-Logbook?')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs transition flex items-center space-x-1" title="Setujui Pengajuan">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                                <span class="hidden sm:inline">Setujui</span>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('dosen.logbook.detail', $kp->id_kp) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition" title="Lihat E-Logbook">
                                        <i class="fa-solid fa-book-open"></i>
                                    </a>

                                    <a href="{{ route('dosen.penilaian.form', $kp->id_kp) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition" title="Beri Penilaian">
                                        <i class="fa-solid fa-award"></i>
                                    </a>

                                    <form method="POST" action="{{ route('dosen.pendaftaran.delete', $kp->id_kp) }}" onsubmit="return confirm('Yakin ingin menghapus data mahasiswa KP ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-[11px] transition" title="Hapus Data">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                <span>Belum ada mahasiswa yang mendaftar pada bimbingan Anda.</span>
                                <div class="mt-3">
                                    <button type="button" @click="showModal = true"
                                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-2">
                                        <i class="fa-solid fa-user-plus text-xs"></i>
                                        <span>+ Tambah Data Mahasiswa Sekarang</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah Data Mahasiswa KP -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto"
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             @click.away="showModal = false">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Form Input Dosen</span>
                    <h3 class="text-lg font-extrabold text-slate-800">Tambah Data Mahasiswa KP</h3>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('dosen.pendaftaran.store') }}" class="space-y-4">
                @csrf

                <!-- Section: Data Mahasiswa -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            NIM (Nomor Induk Mahasiswa) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nim" required placeholder="Contoh: 21063110015"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Lengkap Mahasiswa <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_lengkap" required placeholder="Nama lengkap mahasiswa"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Program Studi <span class="text-rose-500">*</span>
                        </label>
                        <select name="prodi" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="Teknik Informatika" selected>Teknik Informatika</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Angkatan <span class="text-rose-500">*</span>
                        </label>
                        <select name="angkatan" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach([2020, 2021, 2022, 2023, 2024, 2025] as $thn)
                                <option value="{{ $thn }}" {{ $thn == 2021 ? 'selected' : '' }}>Angkatan {{ $thn }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                IPK <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" max="4.00" name="ipk_terakhir" value="3.50" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                SKS <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" min="0" max="160" name="sks_lulus" value="110" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 my-2"></div>

                <!-- Section: Data KP -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Instansi / Perusahaan Tempat KP <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="instansi" required placeholder="Contoh: PT Telkom Indonesia / Diskominfo Pamekasan"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alamat Instansi
                        </label>
                        <input type="text" name="alamat_instansi" placeholder="Jl. Jokotole No. ..., Pamekasan"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Mulai KP <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Selesai (Otomatis 30 Hari jika kosong)
                            </label>
                            <input type="date" name="tanggal_selesai" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Status Pelaksanaan KP
                        </label>
                        <select name="status_kp"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="Berjalan" selected>Berjalan (Langsung aktif mengisi E-Logbook)</option>
                            <option value="Pengajuan">Pengajuan (Menunggu konfirmasi)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-save text-xs"></i>
                        <span>Simpan Data Mahasiswa</span>
                    </button>
                </div>
            </form>

        </div>

    </div>

</div>
@endsection
