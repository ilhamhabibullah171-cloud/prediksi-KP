@extends('layouts.app')

@section('title', 'Template Formulir Pendaftaran KP')
@section('page_heading', 'Formulir Pendaftaran Kerja Praktik (KP)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ isEditing: {{ (!$kp || empty($kp->instansi)) ? 'true' : 'false' }} }">

    <!-- Status Prasyarat Akademik Mahasiswa -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider">Verifikasi Akademik</span>
                <h3 class="text-sm font-bold text-slate-800">{{ $mahasiswa->nama_lengkap }} (NIM: {{ $mahasiswa->nim }})</h3>
                <p class="text-xs text-slate-500 mt-0.5">Program Studi: <strong class="text-slate-700">{{ $mahasiswa->prodi }}</strong> &bull; Angkatan {{ $mahasiswa->angkatan ?? '-' }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3 text-xs">
            <div class="px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-center">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">IPK Terakhir</span>
                <strong class="text-emerald-700 font-extrabold text-sm">{{ number_format($mahasiswa->ipk_terakhir, 2) }}</strong>
            </div>
            <div class="px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-center">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">SKS Lulus</span>
                <strong class="text-emerald-700 font-extrabold text-sm">{{ $mahasiswa->sks_lulus }} SKS</strong>
            </div>
            <div class="px-3 py-2 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center space-x-1">
                <i class="fa-solid fa-circle-check"></i>
                <span>Memenuhi Syarat</span>
            </div>
        </div>
    </div>

    <!-- TAMPILAN 1: STATUS JIKA SUDAH PERNAH MENDAFTAR (DAN TIDAK SEDANG EDIT) -->
    @if($kp && !empty($kp->instansi))
        <div x-show="!isEditing" class="space-y-6">
            
            @if(empty($kp->nidn) || $kp->status_kp === 'Pengajuan')
                <!-- Banner: Sedang Masuk Antrean Admin Fakultas -->
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-6 sm:p-8 space-y-4 shadow-sm">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl flex-shrink-0">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div class="space-y-1">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900 uppercase">
                                Status: Pengajuan Masuk ke Admin Fakultas
                            </span>
                            <h3 class="text-lg font-extrabold text-slate-800">Menunggu Penetapan Dosen Pembimbing oleh Admin Fakultas</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Formulir pendaftaran Kerja Praktik Anda telah berhasil dikirim dan tersimpan di sistem. 
                                <strong>Admin Fakultas Teknik</strong> akan meninjau data pengajuan Anda dan menentukan <strong>Dosen Pembimbing KP</strong> yang sesuai.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Banner: Sudah Disetujui & Dosen Pembimbing Ditetapkan -->
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-4 shadow-sm">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl flex-shrink-0">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="space-y-1">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-900 uppercase">
                                Status: Disetujui & Dosen Pembimbing Ditetapkan
                            </span>
                            <h3 class="text-lg font-extrabold text-slate-800">Pendaftaran Kerja Praktik Aktif</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Dosen Pembimbing Anda telah ditetapkan secara resmi oleh Admin Fakultas Teknik. Anda sekarang dapat mulai melaksanakan kegiatan KP dan mengisi laporan E-Logbook mingguan.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Rincian Formulir Pendaftaran Terdaftar -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Data Pendaftaran Terdaftar</span>
                        <h2 class="text-xl font-black text-slate-800 mt-0.5">{{ $kp->instansi }}</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($kp->status_kp === 'Pengajuan')
                            <button type="button" @click="isEditing = true"
                                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center space-x-1.5">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit Pengajuan</span>
                            </button>
                        @endif
                        <a href="{{ route('mahasiswa.pendaftaran.cetak') }}" target="_blank"
                           class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition flex items-center space-x-1.5">
                            <i class="fa-solid fa-print text-xs"></i>
                            <span>Cetak Formulir (A4)</span>
                        </a>
                    </div>
                </div>

                <!-- Grid Data Pengajuan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Judul / Topik Kerja Praktik:</span>
                        <p class="font-bold text-slate-800 text-xs leading-snug">{{ $kp->judul_kp ?? 'Belum ada judul/topik.' }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Alamat Instansi / Lokasi:</span>
                        <p class="font-medium text-slate-800">{{ $kp->alamat_instansi ?? '-' }}</p>
                        @if($kp->kontak_instansi)
                            <span class="text-slate-500 text-[11px] block mt-1">Kontak PIC: {{ $kp->kontak_instansi }}</span>
                        @endif
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Periode Pelaksanaan (30 Hari):</span>
                        <p class="font-bold text-slate-800">
                            {{ $kp->tanggal_mulai ? $kp->tanggal_mulai->format('d F Y') : '-' }} s/d {{ $kp->tanggal_selesai ? $kp->tanggal_selesai->format('d F Y') : '-' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Dosen Pembimbing KP:</span>
                        @if($kp->dosen)
                            <p class="font-extrabold text-emerald-800 text-sm">{{ $kp->dosen->nama_dosen }}</p>
                            <span class="text-slate-500 text-[11px] font-mono">NIDN: {{ $kp->nidn }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                <i class="fa-solid fa-clock mr-1"></i> Belum Ditentukan (Menunggu Admin Fakultas)
                            </span>
                        @endif
                    </div>

                    @if($kp->usulan_dosen)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Usulan Rekomendasi Dosen (Dari Mahasiswa):</span>
                            <p class="text-slate-700 italic">{{ $kp->usulan_dosen }}</p>
                        </div>
                    @endif

                    @if($kp->berkas_kp)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Berkas Lampiran / Proposal:</span>
                            <a href="{{ route('storage.local', $kp->berkas_kp) }}" target="_blank"
                               class="inline-flex items-center space-x-1.5 text-teal-700 hover:text-teal-900 font-bold underline text-xs pt-0.5">
                                <i class="fa-solid fa-paperclip"></i>
                                <span>Lihat Berkas Lampiran Proposal</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 text-xs text-slate-600">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Catatan Koordinator / Admin Fakultas:</span>
                    <p class="italic text-slate-700">{{ $kp->catatan_koordinator ?? 'Belum ada catatan.' }}</p>
                </div>

                <!-- Footer Nav -->
                <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                        &larr; Kembali ke Dashboard
                    </a>
                    @if($kp->status_kp === 'Berjalan' && !empty($kp->instansi))
                        <a href="{{ route('mahasiswa.logbook') }}"
                           class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                            <span>Buka E-Logbook Mingguan</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- TAMPILAN 2: FORMULIR TEMPLATE PENDAFTARAN KP (BARU / MODE EDIT) -->
    <div x-show="isEditing" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <span class="text-xs uppercase tracking-widest font-bold text-teal-600">Template Pendaftaran KP Online</span>
                <h2 class="text-xl font-extrabold text-slate-800 mt-0.5">Formulir Pendaftaran Kerja Praktik (KP)</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Isi formulir pendaftaran di bawah ini secara lengkap. Data akan diteruskan ke <strong>Admin Fakultas Teknik</strong> untuk penentuan <strong>Dosen Pembimbing</strong>.
                </p>
            </div>
            @if($kp && !empty($kp->instansi))
                <button type="button" @click="isEditing = false"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition self-start sm:self-center">
                    Batal Edit
                </button>
            @endif
        </div>

        <form method="POST" action="{{ route('mahasiswa.pendaftaran.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Bagian 1: Data Tempat Instansi KP -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center text-xs">1</span>
                    <span>Data Instansi / Tempat Pelaksanaan Kerja Praktik</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Instansi / Perusahaan / Lembaga Tempat KP <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="instansi" value="{{ old('instansi', $kp->instansi ?? '') }}" required
                               placeholder="Contoh: PT Telkom Indonesia / Dinas Kominfo Kabupaten Pamekasan"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Lengkap & Kota Instansi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="alamat_instansi" value="{{ old('alamat_instansi', $kp->alamat_instansi ?? '') }}" required
                               placeholder="Contoh: Jl. Jokotole No. 45, Barurambat Kota, Pamekasan, Madura"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kontak PIC / Pembimbing Lapangan di Instansi (Opsional)
                        </label>
                        <input type="text" name="kontak_instansi" value="{{ old('kontak_instansi', $kp->kontak_instansi ?? '') }}"
                               placeholder="Contoh: Bpk. Bambang (081234567890)"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Usulan Rekomendasi Dosen Pembimbing (Opsional)
                        </label>
                        <input type="text" name="usulan_dosen" value="{{ old('usulan_dosen', $kp->usulan_dosen ?? '') }}"
                               placeholder="Tulis usulan dosen pembimbing jika ada"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <span class="text-[10px] text-slate-400 block mt-1">Penetapan akhir tetap ditentukan mutlak oleh Admin Fakultas Teknik.</span>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Topik & Jadwal Pelaksanaan -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center text-xs">2</span>
                    <span>Rencana Judul & Jadwal Pelaksanaan (30 Hari)</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Judul / Topik Rencana Kerja Praktik <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul_kp" value="{{ old('judul_kp', $kp->judul_kp ?? '') }}" required
                               placeholder="Contoh: Rancang Bangun Sistem Informasi Manajemen Persediaan Berbasis Web"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Mulai Pelaksanaan KP <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai"
                                   value="{{ old('tanggal_mulai', $kp && $kp->tanggal_mulai ? $kp->tanggal_mulai->format('Y-m-d') : date('Y-m-d')) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Selesai (Durasi 30 Hari)
                            </label>
                            <input type="date" name="tanggal_selesai"
                                   value="{{ old('tanggal_selesai', $kp && $kp->tanggal_selesai ? $kp->tanggal_selesai->format('Y-m-d') : date('Y-m-d', strtotime('+30 days'))) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Upload Berkas Proposal / Surat Penerimaan Instansi (Opsional, PDF/JPG, Max: 3MB)
                        </label>
                        <input type="file" name="berkas_kp" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition border border-slate-200 rounded-xl p-1">
                        @if($kp && $kp->berkas_kp)
                            <span class="text-[11px] text-slate-500 block mt-1">
                                Berkas saat ini: <a href="{{ route('storage.local', $kp->berkas_kp) }}" target="_blank" class="text-teal-600 underline font-bold">Lihat Berkas</a> (unggah baru jika ingin memperbarui)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ketentuan & Alur Penetapan -->
            <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 space-y-1">
                <span class="font-bold block flex items-center space-x-1.5">
                    <i class="fa-solid fa-circle-info text-amber-600"></i>
                    <span>Ketentuan Penetapan Dosen Pembimbing:</span>
                </span>
                <p class="leading-relaxed text-amber-800">
                    Setelah Anda mengirimkan formulir ini, berkas pendaftaran akan langsung masuk ke antrean <strong>Admin Fakultas Teknik</strong>. 
                    Admin Fakultas yang berwenang menentukan dan menetapkan <strong>Dosen Pembimbing KP</strong> resmi untuk Anda.
                </p>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                <a href="{{ route('mahasiswa.dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                    &larr; Kembali ke Dashboard
                </a>
                <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center space-x-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Formulir Pendaftaran KP ke Admin Fakultas</span>
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
