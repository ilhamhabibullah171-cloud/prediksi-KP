@extends('layouts.app')

@section('title', 'Validasi E-Logbook Mingguan')
@section('page_heading', 'Validasi E-Logbook Mingguan Mahasiswa (30 Hari)')

@section('content')
<div class="space-y-6" x-data="{ reviewModal: false, selectedLog: {} }">

    <!-- Header Mahasiswa Info -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-800 flex items-center justify-center font-extrabold text-lg flex-shrink-0">
                {{ strtoupper(substr($kp->mahasiswa->nama_lengkap ?? 'M', 0, 1)) }}
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-800">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</h3>
                <p class="text-xs text-slate-500">
                    NIM: <strong class="text-slate-700">{{ $kp->nim }}</strong> &bull; Prodi: <strong class="text-slate-700">{{ $kp->mahasiswa->prodi ?? '-' }}</strong> &bull; Instansi: <strong>{{ $kp->instansi ?? 'Menunggu Penetapan Admin' }}</strong> &bull; Periode: {{ $kp->tanggal_mulai ? $kp->tanggal_mulai->format('d/m/Y') : '-' }} s/d {{ $kp->tanggal_selesai ? $kp->tanggal_selesai->format('d/m/Y') : '-' }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.logbook.index') }}"
               class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                &larr; Kembali ke Daftar Logbook
            </a>
        </div>
    </div>

    <!-- Summary Counts -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-center">
            <span class="text-slate-400 block text-[11px] font-bold">Total Terisi</span>
            <strong class="text-xl font-black text-slate-800 mt-1 block">{{ $kp->logbook_count }} / 30 Hari</strong>
            <span class="text-[10px] text-teal-600 font-bold">{{ $kp->rasio_logbook }}% Kepatuhan</span>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-center">
            <span class="text-slate-400 block text-[11px] font-bold">Approved</span>
            <strong class="text-xl font-black text-emerald-600 mt-1 block">{{ $kp->logbook_approved_count }}</strong>
            <span class="text-[10px] text-slate-400">Tervalidasi</span>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-center">
            <span class="text-slate-400 block text-[11px] font-bold">Menunggu Validasi</span>
            <strong class="text-xl font-black text-amber-600 mt-1 block">{{ $kp->logbooks()->where('status_validasi', 'Pending')->count() }}</strong>
            <span class="text-[10px] text-amber-600 font-bold">Perlu Review</span>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-center">
            <span class="text-slate-400 block text-[11px] font-bold">Perlu Revisi</span>
            <strong class="text-xl font-black text-orange-600 mt-1 block">{{ $kp->logbooks()->where('status_validasi', 'Revisi')->count() }}</strong>
            <span class="text-[10px] text-orange-600 font-bold">Catatan Dosen</span>
        </div>
    </div>

    @if(empty($weeks))
        <div class="bg-white rounded-3xl p-8 sm:p-12 text-center text-slate-500 border border-slate-200 space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mx-auto shadow-xs">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h4 class="font-extrabold text-slate-800 text-base">Tempat & Jadwal KP Belum Ditetapkan</h4>
            <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                Mahasiswa ini belum dapat mengisi E-Logbook mingguan karena tempat instansi dan jadwal pelaksanaan Kerja Praktik belum ditetapkan oleh <strong>Admin Fakultas Teknik</strong>.
            </p>
        </div>
    @else
        <!-- Weekly Grouped Logbook (Minggu 1 s/d Minggu 5) -->
        <div class="space-y-6">
        @foreach($weeks as $wIndex => $w)
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xs">
                            W{{ $wIndex }}
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-slate-800">{{ $w['label'] }} ({{ $w['hari_range'] }})</h4>
                            <span class="text-[11px] text-slate-400">7 Hari Kerja Praktik</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">
                            Approved: {{ $w['approved_count'] }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold">
                            Pending: {{ $w['pending_count'] }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold">
                            Terisi: {{ $w['filled_count'] }} / {{ $w['total_days'] }}
                        </span>
                    </div>
                </div>

                <!-- Days in this Week -->
                <div class="space-y-3">
                    @foreach($w['days'] as $day)
                        @php
                            $entry = $day['entry'];
                            $status = $day['status'];
                        @endphp
                        <div class="p-4 rounded-2xl border transition
                            {{ $status === 'Approved' ? 'bg-emerald-50/30 border-emerald-200' : ($status === 'Pending' ? 'bg-amber-50/50 border-amber-300' : ($status === 'Revisi' ? 'bg-orange-50/50 border-orange-300' : ($status === 'Terlewat' ? 'bg-rose-50/30 border-rose-200' : 'bg-slate-50 border-slate-200 opacity-60'))) }}">
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center space-x-3">
                                    <span class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center font-bold text-xs text-slate-800">
                                        H-{{ $day['hari_ke'] }}
                                    </span>
                                    <div>
                                        <span class="font-extrabold text-slate-800 text-xs">Hari ke-{{ $day['hari_ke'] }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $day['tanggal']->format('l, d F Y') }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold
                                        {{ $status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($status === 'Pending' ? 'bg-amber-100 text-amber-800' : ($status === 'Revisi' ? 'bg-orange-100 text-orange-800' : ($status === 'Terlewat' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-600'))) }}">
                                        {{ $status }}
                                    </span>

                                    @if($entry)
                                        <button type="button"
                                                @click="selectedLog = {{ json_encode($entry) }}; selectedLog.hari_ke = {{ $day['hari_ke'] }}; reviewModal = true;"
                                                class="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                            Validasi
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($entry)
                                <div class="mt-3 pt-3 border-t border-slate-200/60 text-xs text-slate-700 space-y-2">
                                    <p class="leading-relaxed">{{ $entry->deskripsi_pekerjaan }}</p>

                                    @if($entry->dokumentasi)
                                        <div class="pt-1">
                                            <a href="{{ route('storage.local', $entry->dokumentasi) }}" target="_blank"
                                               class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-emerald-700 font-bold hover:bg-slate-50 transition text-[11px]">
                                                <i class="fa-solid fa-paperclip"></i>
                                                <span>Lihat File Dokumentasi Mahasiswa</span>
                                            </a>
                                        </div>
                                    @endif

                                    @if($entry->catatan_dosen)
                                        <p class="text-[11px] text-orange-800 bg-orange-100/60 p-2 rounded-xl italic">
                                            Catatan Anda Sebelumnya: {{ $entry->catatan_dosen }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <p class="mt-2 text-[11px] text-slate-400 italic">Belum ada catatan kegiatan yang diunggah.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Modal Review & Validasi Dosen -->
    <div x-show="reviewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 x-show="reviewModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="reviewModal = false"></div>

            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 space-y-4"
                 x-show="reviewModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-xs uppercase font-bold text-teal-600 tracking-wider">Validasi E-Logbook</span>
                        <h3 class="text-base font-extrabold text-slate-800">
                            Validasi Hari ke-<span x-text="selectedLog.hari_ke"></span>
                        </h3>
                    </div>
                    <button type="button" @click="reviewModal = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form :action="'{{ url('/dosen/logbook') }}/' + selectedLog.id_logbook + '/validate'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Keputusan Validasi <span class="text-rose-500">*</span>
                        </label>
                        <select name="status_validasi" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none bg-white">
                            <option value="Approved">Approved (Disetujui)</option>
                            <option value="Revisi">Revisi (Perlu Diperbaiki Mahasiswa)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan / Saran Pembimbing
                        </label>
                        <textarea name="catatan_dosen" rows="3" x-model="selectedLog.catatan_dosen"
                                  placeholder="Berikan komentar, arahan, atau perbaikan untuk mahasiswa..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="reviewModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white shadow transition">
                            Simpan Validasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
