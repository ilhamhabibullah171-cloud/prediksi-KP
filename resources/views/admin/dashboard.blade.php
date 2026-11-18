@extends('layouts.app')

@section('title', 'Dashboard Admin Fakultas Teknik')
@section('page_heading', 'Dashboard Admin Fakultas Teknik')

@section('content')
<div class="space-y-6" x-data="{ 
    importModal: false, 
    editModal: false, 
    searchQuery: '', 
    filterStatus: 'all',
    selectedKp: { 
        id_kp: null, 
        nim: '', 
        nama: '', 
        prodi: '', 
        judul_kp: '',
        instansi: '', 
        alamat_instansi: '', 
        kontak_instansi: '',
        tanggal_mulai: '', 
        tanggal_selesai: '', 
        nidn: '', 
        usulan_dosen: '',
        berkas_kp: '',
        berkas_url: '',
        status_kp: 'Berjalan',
        catatan: ''
    },
    openEdit(kp) {
        this.selectedKp = {
            id_kp: kp.id_kp,
            nim: kp.nim,
            nama: kp.mahasiswa ? kp.mahasiswa.nama_lengkap : 'N/A',
            prodi: kp.mahasiswa ? kp.mahasiswa.prodi : '-',
            judul_kp: kp.judul_kp || '',
            instansi: kp.instansi || '',
            alamat_instansi: kp.alamat_instansi || '',
            kontak_instansi: kp.kontak_instansi || '',
            tanggal_mulai: kp.tanggal_mulai ? (typeof kp.tanggal_mulai === 'string' ? kp.tanggal_mulai.substring(0, 10) : '') : '{{ date('Y-m-d') }}',
            tanggal_selesai: kp.tanggal_selesai ? (typeof kp.tanggal_selesai === 'string' ? kp.tanggal_selesai.substring(0, 10) : '') : '{{ date('Y-m-d', strtotime('+30 days')) }}',
            nidn: kp.nidn || '{{ $dosenList->first()->nidn ?? '' }}',
            usulan_dosen: kp.usulan_dosen || '',
            berkas_kp: kp.berkas_kp || '',
            berkas_url: kp.berkas_kp ? '{{ url('/storage') }}/' + kp.berkas_kp.replace(/^public\//, '').replace(/^storage\//, '') : '',
            status_kp: kp.status_kp === 'Pengajuan' ? 'Berjalan' : (kp.status_kp || 'Berjalan'),
            catatan: kp.catatan_koordinator || ''
        };
        this.editModal = true;
    }
}">

    <!-- Hero Card Admin FT -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-52 h-52 rounded-full bg-emerald-500/10 blur-2xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
            <div>
                <div class="flex items-center space-x-2 text-xs uppercase tracking-widest text-emerald-400 font-bold">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Portal Administrator Fakultas Teknik UIM</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">Pengelolaan & Penetapan Pembimbing KP</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Admin Fakultas Teknik mengelola pendaftaran mahasiswa KP, mengunggah data terpusat, memvalidasi tempat & jadwal instansi, serta <strong>menentukan dan menetapkan Dosen Pembimbing resmi</strong> bagi setiap mahasiswa.
                </p>
            </div>

            <!-- Action Buttons: Unduh Template & Unggah File -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.mahasiswa.template-xlsx') }}"
                   class="px-4 py-2.5 rounded-2xl bg-white text-emerald-950 hover:bg-emerald-50 font-extrabold text-xs shadow-lg transition flex items-center space-x-2 transform active:scale-95">
                    <i class="fa-solid fa-file-excel text-emerald-700 text-base"></i>
                    <span>Template Kosong (XLSX)</span>
                </a>

                <a href="{{ route('admin.mahasiswa.data104-xlsx') }}"
                   class="px-4 py-2.5 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-extrabold text-xs shadow-lg border border-emerald-400/30 transition flex items-center space-x-2 transform active:scale-95">
                    <i class="fa-solid fa-file-csv text-emerald-300 text-base"></i>
                    <span>Unduh Data 104 Mhs (XLSX)</span>
                </a>

                <button type="button" @click="importModal = true"
                        class="px-4 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-900/30 transition flex items-center space-x-2 transform active:scale-95">
                    <i class="fa-solid fa-file-arrow-up text-base"></i>
                    <span>Unggah Data XLSX / CSV</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Pengajuan Menunggu Penetapan Admin -->
    @if($totalBelumDitetapkan > 0)
        <div class="p-4 rounded-3xl bg-amber-500/10 border-2 border-amber-400/40 text-amber-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-md">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-amber-950 text-sm">Pendaftaran KP Menunggu Penetapan Dosen Pembimbing</h4>
                    <p class="text-xs text-amber-800">
                        Terdapat <strong>{{ $totalBelumDitetapkan }} mahasiswa</strong> yang telah mengajukan / belum ditetapkan Dosen Pembimbing dan tempat KP oleh Admin Fakultas Teknik.
                    </p>
                </div>
            </div>
            <button type="button" @click="filterStatus = 'pending'"
                    class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-1.5 flex-shrink-0 transform active:scale-95">
                <i class="fa-solid fa-filter"></i>
                <span>Tampilkan Pengajuan ({{ $totalBelumDitetapkan }})</span>
            </button>
        </div>
    @endif

    <!-- 4 Key Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Mahasiswa KP Terdata</span>
                <strong class="text-2xl font-black text-slate-800 mt-1 block">{{ $totalMahasiswa }}</strong>
                <span class="text-[11px] text-slate-500 font-medium">TI: <strong class="text-emerald-700">{{ $totalTI }}</strong> &bull; SI: <strong class="text-purple-700">{{ $totalSI }}</strong></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Pembimbing & Tempat KP Ditetapkan</span>
                <strong class="text-2xl font-black text-emerald-700 mt-1 block">{{ $totalDitetapkan }}</strong>
                <span class="text-[11px] text-slate-500 font-medium">
                    Status Aktif Berjalan
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Perlu Penetapan Admin</span>
                <strong class="text-2xl font-black {{ $totalBelumDitetapkan > 0 ? 'text-amber-600' : 'text-slate-700' }} mt-1 block">
                    {{ $totalBelumDitetapkan }}
                </strong>
                <span class="text-[11px] text-slate-500 font-medium">
                    Menunggu Verifikasi FT
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Dinilai Dosen</span>
                <strong class="text-2xl font-black text-blue-600 mt-1 block">{{ $totalDinilai }}</strong>
                <span class="text-[11px] text-slate-500">Rata-rata: <strong>{{ number_format($rataRataNilai, 1) }}</strong></span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi Data & Pendaftaran KP Mahasiswa -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-800 flex items-center space-x-2">
                    <i class="fa-solid fa-list-check text-emerald-600"></i>
                    <span>Daftar Pendaftaran & Rekapitulasi Mahasiswa KP</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Admin Fakultas menetapkan Dosen Pembimbing resmi dan memvalidasi lokasi tempat KP</p>
            </div>

            <!-- Filter Tabs & Search Bar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="flex items-center space-x-1.5 p-1 bg-slate-100 rounded-2xl">
                    <button type="button" @click="filterStatus = 'all'"
                            :class="filterStatus === 'all' ? 'bg-white text-slate-900 shadow-xs font-extrabold' : 'text-slate-500 hover:text-slate-800 font-semibold'"
                            class="px-3 py-1.5 rounded-xl text-xs transition transform active:scale-95 flex items-center space-x-1">
                        <span>Semua</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 text-slate-700">{{ $kpList->count() }}</span>
                    </button>

                    <button type="button" @click="filterStatus = 'pending'"
                            :class="filterStatus === 'pending' ? 'bg-amber-600 text-white shadow-xs font-extrabold' : 'text-slate-500 hover:text-slate-800 font-semibold'"
                            class="px-3 py-1.5 rounded-xl text-xs transition transform active:scale-95 flex items-center space-x-1">
                        <span>Perlu Pembimbing</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="filterStatus === 'pending' ? 'bg-amber-700 text-white' : 'bg-amber-100 text-amber-800'">{{ $totalBelumDitetapkan }}</span>
                    </button>

                    <button type="button" @click="filterStatus = 'assigned'"
                            :class="filterStatus === 'assigned' ? 'bg-emerald-600 text-white shadow-xs font-extrabold' : 'text-slate-500 hover:text-slate-800 font-semibold'"
                            class="px-3 py-1.5 rounded-xl text-xs transition transform active:scale-95 flex items-center space-x-1">
                        <span>Ditetapkan</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="filterStatus === 'assigned' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800'">{{ $totalDitetapkan }}</span>
                    </button>
                </div>

                <!-- Search input -->
                <div class="relative w-full sm:w-60">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama / NIM / prodi..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">NIM & Mahasiswa</th>
                        <th class="py-3.5 px-4">Program Studi</th>
                        <th class="py-3.5 px-4">Tempat Instansi & Jadwal KP</th>
                        <th class="py-3.5 px-4">Dosen Pembimbing (Fakultas)</th>
                        <th class="py-3.5 px-4 text-center">Nilai Akhir</th>
                        <th class="py-3.5 px-4 text-center">Grade</th>
                        <th class="py-3.5 px-4 text-right">Aksi Penetapan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kpList as $idx => $kp)
                        @php
                            $mhs = $kp->mahasiswa;
                            $pen = $kp->penilaian;
                            $nilaiDosen = $pen ? ($pen->nilai_akhir ?? $pen->nilai_dosen_pembimbing) : null;
                            $isPending = empty($kp->nidn) || $kp->status_kp === 'Pengajuan';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition"
                            x-show="(!searchQuery || '{{ strtolower($mhs->nama_lengkap ?? '') }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($kp->nim) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($mhs->prodi ?? '') }}'.includes(searchQuery.toLowerCase())) && (filterStatus === 'all' || (filterStatus === 'pending' && {{ $isPending ? 'true' : 'false' }}) || (filterStatus === 'assigned' && {{ !$isPending ? 'true' : 'false' }}))">
                            
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>

                            <!-- 1. NIM & NAMA -->
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 text-sm">
                                    {{ $mhs->nama_lengkap ?? 'N/A' }}
                                </div>
                                <div class="flex items-center space-x-2 mt-0.5">
                                    <span class="font-mono font-bold text-slate-700 text-xs">{{ $kp->nim }}</span>
                                    <span class="text-[10px] text-slate-400">&bull; Angkatan {{ $mhs->angkatan ?? '-' }}</span>
                                </div>
                                @if($kp->judul_kp)
                                    <div class="text-[11px] text-slate-500 mt-1 line-clamp-1 italic max-w-xs" title="{{ $kp->judul_kp }}">
                                        <i class="fa-solid fa-bookmark text-[9px] text-emerald-600 mr-1"></i>
                                        {{ $kp->judul_kp }}
                                    </div>
                                @endif
                            </td>

                            <!-- 2. PRODI -->
                            <td class="py-3.5 px-4 font-semibold text-slate-700">
                                @if($mhs && str_contains(strtoupper($mhs->prodi), 'SISTEM'))
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-800 border border-purple-200 font-semibold text-[11px]">
                                        <i class="fa-solid fa-network-wired text-[9px] text-purple-600"></i>
                                        <span>Sistem Informasi</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-teal-50 text-teal-800 border border-teal-200 font-semibold text-[11px]">
                                        <i class="fa-solid fa-code text-[9px] text-teal-600"></i>
                                        <span>Teknik Informatika</span>
                                    </span>
                                @endif
                            </td>

                            <!-- 3. TEMPAT INSTANSI & JADWAL KP -->
                            <td class="py-3.5 px-4">
                                @if($kp->instansi)
                                    <div class="font-bold text-slate-800 text-xs">
                                        {{ $kp->instansi }}
                                    </div>
                                    @if($kp->alamat_instansi)
                                        <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $kp->alamat_instansi }}</div>
                                    @endif
                                    <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">
                                        <i class="fa-regular fa-calendar text-[9px] mr-1"></i>
                                        {{ $kp->tanggal_mulai ? $kp->tanggal_mulai->format('d/m/Y') : '-' }} s/d {{ $kp->tanggal_selesai ? $kp->tanggal_selesai->format('d/m/Y') : '-' }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[10px]">
                                        <i class="fa-solid fa-clock-rotate-left text-[9px] text-amber-600"></i>
                                        <span>Belum Ditetapkan</span>
                                    </span>
                                @endif
                            </td>

                            <!-- 4. DOSEN PEMBIMBING (FAKULTAS) -->
                            <td class="py-3.5 px-4">
                                @if($kp->dosen && $kp->status_kp !== 'Pengajuan')
                                    <div class="flex items-center space-x-2">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            <i class="fa-solid fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <strong class="text-slate-900 font-bold text-xs block leading-tight">{{ $kp->dosen->nama_dosen }}</strong>
                                            <span class="text-[10px] text-slate-400 font-mono">NIDN: {{ $kp->dosen->nidn }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[10px]">
                                            <i class="fa-solid fa-clock-rotate-left text-[9px] text-amber-600"></i>
                                            <span>Menunggu Penetapan FT</span>
                                        </span>
                                        @if($kp->usulan_dosen)
                                            <div class="text-[10px] text-slate-500 italic">
                                                <span class="font-medium text-slate-400">Usulan mhs:</span> {{ $kp->usulan_dosen }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- 5. NILAI DOSEN PEMBIMBING -->
                            <td class="py-3.5 px-4 text-center font-mono font-black text-sm text-slate-800">
                                @if($nilaiDosen !== null)
                                    <span class="inline-block px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        {{ number_format($nilaiDosen, 1) }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- 6. Grade Kelulusan -->
                            <td class="py-3.5 px-4 text-center">
                                @if($pen && $pen->grade)
                                    <span class="inline-block w-8 h-8 rounded-xl bg-emerald-100 text-emerald-900 font-black text-sm leading-8 shadow-xs">
                                        {{ $pen->grade }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- 7. Aksi Penetapan -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <button type="button" @click="openEdit({{ json_encode($kp) }})"
                                            class="px-3 py-1.5 {{ $isPending ? 'bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold' }} rounded-xl transition text-[11px] flex items-center space-x-1.5 transform active:scale-95"
                                            title="Tetapkan Dosen Pembimbing & Tempat KP">
                                        <i class="fa-solid {{ $isPending ? 'fa-user-plus' : 'fa-user-pen' }} text-xs"></i>
                                        <span>{{ $isPending ? '+ Tentukan Pembimbing' : 'Ubah Pembimbing' }}</span>
                                    </button>

                                    <form method="POST" action="{{ route('admin.mahasiswa.delete', $kp->id_kp) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-xl transition text-[11px] transform active:scale-95" title="Hapus Data Mahasiswa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-file-excel text-3xl mb-2 text-slate-300 block"></i>
                                Belum ada data mahasiswa yang diunggah. Silakan klik tombol <strong>Unggah File Data XLSX</strong> di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL UNGGAH FILE TEMPLATE XLSX                                -->
    <!-- ============================================================== -->
    <div x-show="importModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 x-show="importModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="importModal = false"></div>

            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 space-y-5"
                 x-show="importModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-file-arrow-up"></i>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-800">Unggah File Template Mahasiswa KP</h3>
                    </div>
                    <button type="button" @click="importModal = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transform active:scale-95">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/80 text-xs text-slate-600 space-y-2">
                    <p class="font-bold text-emerald-950">Pastikan format kolom sesuai dengan template resmi:</p>
                    <div class="bg-white p-2.5 rounded-xl border border-emerald-200 font-mono text-[11px] text-slate-700">
                        NIM | NAMA | PRODI | NILAI_DOSEN_PEMBIMBING
                    </div>
                    <a href="{{ route('admin.mahasiswa.template-xlsx') }}"
                       class="inline-flex items-center space-x-1.5 text-xs font-bold text-emerald-700 hover:underline pt-1">
                        <i class="fa-solid fa-download"></i>
                        <span>Unduh file template resmi (.xlsx) di sini jika belum ada</span>
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.mahasiswa.import-xlsx') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Pilih File Excel / CSV (.xlsx, .xls, .csv) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="file_xlsx" required accept=".xlsx,.xls,.csv"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 border border-slate-200 rounded-xl p-1">
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="importModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition transform active:scale-95">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow transition flex items-center space-x-1.5 transform active:scale-95">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Unggah & Simpan Data</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL PENETAPAN DOSEN PEMBIMBING & TEMPAT KP (ADMIN FAKULTAS)  -->
    <!-- ============================================================== -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 x-show="editModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="editModal = false"></div>

            <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 space-y-4"
                 x-show="editModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-base shadow-xs">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Wewenang Fakultas Teknik</span>
                            <h3 class="text-base font-extrabold text-slate-800">Penetapan Pembimbing & Tempat KP</h3>
                        </div>
                    </div>
                    <button type="button" @click="editModal = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transform active:scale-95">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Info Mahasiswa Terpilih & Pengajuannya -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <strong class="text-slate-900 font-extrabold text-sm block" x-text="selectedKp.nama"></strong>
                            <span class="text-slate-500 font-mono" x-text="'NIM: ' + selectedKp.nim"></span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white border border-slate-200 text-emerald-800" x-text="selectedKp.prodi"></span>
                    </div>

                    <!-- Usulan Dosen / Berkas Mahasiswa jika ada -->
                    <template x-if="selectedKp.judul_kp">
                        <div class="pt-2 border-t border-slate-200 text-[11px] text-slate-600">
                            <span class="font-bold text-slate-700">Rencana Topik / Judul:</span>
                            <span class="italic ml-1" x-text="selectedKp.judul_kp"></span>
                        </div>
                    </template>

                    <template x-if="selectedKp.usulan_dosen">
                        <div class="text-[11px] text-amber-800 bg-amber-50 p-2 rounded-xl border border-amber-200">
                            <i class="fa-solid fa-lightbulb mr-1 text-amber-600"></i>
                            <strong>Usulan Pembimbing dari Mahasiswa:</strong> <span x-text="selectedKp.usulan_dosen"></span>
                            <span class="block text-[10px] text-amber-700 mt-0.5">(Sebagai bahan pertimbangan keputusan Admin Fakultas)</span>
                        </div>
                    </template>

                    <template x-if="selectedKp.berkas_url">
                        <div class="pt-1 flex items-center justify-between">
                            <span class="text-[11px] text-slate-500">Berkas Lampiran Mahasiswa:</span>
                            <a :href="selectedKp.berkas_url" target="_blank"
                               class="text-[11px] font-bold text-emerald-700 hover:underline inline-flex items-center space-x-1">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>Lihat File Berkas / Proposal</span>
                            </a>
                        </div>
                    </template>
                </div>

                <form method="POST" :action="'{{ url('/admin/mahasiswa') }}/' + selectedKp.id_kp + '/instansi'" class="space-y-3.5">
                    @csrf

                    <!-- 1. Tentukan Dosen Pembimbing (Wajib) -->
                    <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-1.5">
                        <label class="block text-xs font-extrabold text-emerald-950 uppercase tracking-wider flex items-center justify-between">
                            <span class="flex items-center space-x-1.5">
                                <i class="fa-solid fa-user-tie text-emerald-700"></i>
                                <span>Tentukan Dosen Pembimbing Resmi <span class="text-rose-500">*</span></span>
                            </span>
                            <span class="text-[10px] text-emerald-700 font-semibold">Wewenang Admin FT</span>
                        </label>
                        <select name="nidn" x-model="selectedKp.nidn" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-bold">
                            <option value="" disabled>-- Pilih Dosen Pembimbing Fakultas Teknik --</option>
                            @foreach($dosenList as $dsn)
                                <option value="{{ $dsn->nidn }}">
                                    {{ $dsn->nama_dosen }} (NIDN: {{ $dsn->nidn }}) — [{{ $dsn->kerja_praktik_count ?? 0 }} Mhs]
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500">
                            Mahasiswa otomatis akan tampil di portal bimbingan dosen yang dipilih.
                        </p>
                    </div>

                    <!-- 2. Instansi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Instansi / Perusahaan Tempat KP <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="instansi" x-model="selectedKp.instansi" required
                               placeholder="Contoh: PT Telkom Indonesia / Dinas Kominfo Pamekasan"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- 3. Alamat Instansi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alamat / Kota Instansi
                        </label>
                        <input type="text" name="alamat_instansi" x-model="selectedKp.alamat_instansi"
                               placeholder="Contoh: Jl. Jokotole No. 12, Pamekasan, Madura"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- 4. Topik / Judul KP -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Topik / Judul Kerja Praktik
                        </label>
                        <input type="text" name="judul_kp" x-model="selectedKp.judul_kp"
                               placeholder="Contoh: Rancang Bangun Sistem Informasi Pelayanan Publik"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- 5. Tanggal Pelaksanaan (30 Hari) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Mulai KP <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" x-model="selectedKp.tanggal_mulai" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Selesai (30 Hari)
                            </label>
                            <input type="date" name="tanggal_selesai" x-model="selectedKp.tanggal_selesai"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <!-- 6. Status KP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Status Pelaksanaan KP
                            </label>
                            <select name="status_kp" x-model="selectedKp.status_kp"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                                <option value="Berjalan">Berjalan (Disetujui & Aktif)</option>
                                <option value="Pengajuan">Pengajuan (Menunggu)</option>
                                <option value="Selesai">Selesai KP</option>
                                <option value="Batal">Batal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Catatan Admin Fakultas
                            </label>
                            <input type="text" name="catatan" x-model="selectedKp.catatan"
                                   placeholder="Contoh: Disetujui, harap segera menemui dosen pembimbing"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition transform active:scale-95">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-black bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-900/20 transition flex items-center space-x-2 transform active:scale-95">
                            <i class="fa-solid fa-user-check"></i>
                            <span>Tetapkan Pembimbing & Validasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
