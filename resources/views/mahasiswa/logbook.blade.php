@extends('layouts.app')

@section('title', 'E-Logbook Mingguan (30 Hari)')
@section('page_heading', 'E-Logbook Mingguan Mahasiswa (30 Hari)')

@section('content')
<div class="space-y-6" x-data="logbookApp()">

    @if(!$kp || empty($kp->instansi) || !$kp->tanggal_mulai)
        <!-- Kondisi Belum Ditetapkan Tempat & Jadwal KP -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 text-center space-y-4 max-w-2xl mx-auto shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mx-auto shadow-xs">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h3 class="text-xl font-extrabold text-slate-800">Tempat & Jadwal KP Belum Ditetapkan</h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-lg mx-auto">
                Laporan E-Logbook mingguan hanya dapat diisi ketika Anda sudah resmi melaksanakan Kerja Praktik di instansi yang ditetapkan oleh <strong>Admin Fakultas Teknik</strong>.
            </p>
            <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-800 max-w-md mx-auto">
                <span class="font-bold block">Status Pendaftaran: Menunggu Penetapan</span>
                <span class="text-[11px] text-amber-700">Admin Fakultas sedang memproses penetapan lokasi instansi dan tanggal pelaksanaan KP Anda.</span>
            </div>
            <div class="pt-2">
                <a href="{{ route('mahasiswa.dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs inline-flex items-center space-x-2 shadow-xs transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    @else
        <!-- Header Stats Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs uppercase tracking-widest font-bold text-teal-600">E-Logbook Mingguan (7 Hari x 30 Hari)</span>
                    <h2 class="text-xl font-extrabold text-slate-800">{{ $kp->instansi }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Periode: {{ $kp->tanggal_mulai ? $kp->tanggal_mulai->format('d M Y') : '-' }} s/d {{ $kp->tanggal_selesai ? $kp->tanggal_selesai->format('d M Y') : '-' }} &bull; Pembimbing: {{ $kp->dosen->nama_dosen ?? 'Belum ditentukan' }}
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        &larr; Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Mode Pengisian Logbook Mahasiswa Badge -->
            <div class="p-3.5 bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-teal-900">
                <div class="flex items-center space-x-2.5">
                    <span class="w-7 h-7 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs">
                        <i class="fa-solid fa-pen-nib"></i>
                    </span>
                    <div>
                        <span class="font-extrabold text-slate-800">Status Pelaksanaan Kerja Praktik:</span>
                        <span class="text-slate-600 ml-1">Silakan isi laporan kegiatan harian Anda (Hari 1 s/d 30). Dosen Pembimbing akan meninjau dan memvalidasi setiap laporan.</span>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl bg-teal-100 text-teal-900 font-extrabold text-[11px] self-start sm:self-center border border-teal-300">
                    {{ $kp->logbook_approved_count }} / 30 Hari Approved
                </span>
            </div>

            <!-- Progress bar & Counters -->
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold text-slate-700">
                    <span>Total Terisi: {{ $kp->logbook_count }} dari 30 Hari</span>
                    <span class="text-emerald-600 font-extrabold">{{ $kp->rasio_logbook }}% Kepatuhan</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-500"
                         style="width: {{ min(100, $kp->rasio_logbook) }}%"></div>
                </div>
            </div>

            <!-- Legend / Indicators -->
            <div class="flex flex-wrap items-center gap-3 pt-2 text-xs text-slate-600 border-t border-slate-100">
                <span class="font-bold text-slate-700">Status Validasi:</span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-md bg-emerald-500"></span>
                    <span>Approved</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-md bg-amber-400"></span>
                    <span>Pending Review</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-md bg-orange-500"></span>
                    <span>Perlu Revisi</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-md bg-slate-200"></span>
                    <span>Belum Diisi</span>
                </span>
            </div>
        </div>

        <!-- Weekly View: 5 Minggu (7 Hari per Minggu) -->
        <div class="space-y-6">
            @foreach($weeks as $wIndex => $w)
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-black text-sm">
                                W{{ $wIndex }}
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800">{{ $w['label'] }} ({{ $w['hari_range'] }})</h3>
                                <span class="text-[11px] text-slate-400">Kegiatan Kerja Praktik</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">
                                Approved: {{ $w['approved_count'] }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold">
                                Terisi: {{ $w['filled_count'] }} / {{ $w['total_days'] }}
                            </span>
                        </div>
                    </div>

                    <!-- 7 Days Grid per Week -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
                        @foreach($w['days'] as $day)
                            @php
                                $entry = $day['entry'];
                                $status = $day['status'];
                            @endphp
                            <div class="p-3.5 rounded-2xl border flex flex-col justify-between text-xs space-y-2 transition hover:shadow-xs
                                {{ $status === 'Approved' ? 'bg-emerald-50/60 border-emerald-300' : ($status === 'Pending' ? 'bg-amber-50/60 border-amber-300' : ($status === 'Revisi' ? 'bg-orange-50/60 border-orange-300 ring-2 ring-orange-300' : 'bg-slate-50 border-slate-200')) }}">
                                
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-extrabold text-slate-900 text-sm">Hari {{ $day['hari_ke'] }}</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                            {{ $status === 'Approved' ? 'bg-emerald-200 text-emerald-900' : ($status === 'Pending' ? 'bg-amber-200 text-amber-900' : ($status === 'Revisi' ? 'bg-orange-200 text-orange-900' : 'bg-slate-200 text-slate-600')) }}">
                                            {{ $status }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $day['tanggal']->format('D, d M Y') }}</span>
                                </div>

                                <div class="py-1">
                                    @if($entry)
                                        <p class="text-[11px] text-slate-700 line-clamp-3 leading-tight">
                                            {{ $entry->deskripsi_pekerjaan }}
                                        </p>
                                        @if($entry->catatan_dosen)
                                            <p class="mt-1 text-[10px] text-orange-800 bg-orange-100/70 p-1.5 rounded-lg italic">
                                                Catatan Dosen: {{ $entry->catatan_dosen }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-[10px] text-slate-400 italic">Belum ada catatan.</p>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                                    @if($entry && $entry->dokumentasi)
                                        <a href="{{ route('storage.local', $entry->dokumentasi) }}" target="_blank"
                                           class="text-emerald-700 hover:text-emerald-900 text-[10px] font-bold flex items-center space-x-1">
                                            <i class="fa-solid fa-paperclip"></i>
                                            <span>Foto</span>
                                        </a>
                                    @else
                                        <span></span>
                                    @endif

                                    @if($entry && $status === 'Approved')
                                        <button type="button" @click="openModal({{ json_encode($day) }}, 'view')"
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] transition inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-eye text-[9px]"></i>
                                            <span>Lihat Detail</span>
                                        </button>
                                    @elseif($entry)
                                        <button type="button" @click="openModal({{ json_encode($day) }}, 'edit')"
                                                class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] shadow-xs transition inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-pen-to-square text-[9px]"></i>
                                            <span>Edit Laporan</span>
                                        </button>
                                    @else
                                        <button type="button" @click="openModal({{ json_encode($day) }}, 'create')"
                                                class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[10px] shadow-xs transition inline-flex items-center space-x-1">
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

        <!-- Modal Form Input / Edit / View Logbook -->
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
    @endif

</div>

<script>
function logbookApp() {
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
        openModal(day, mode) {
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
