@extends('layouts.app')

@section('title', 'Upload Sertifikat Selesai KP')
@section('page_heading', 'Upload Sertifikat Selesai KP')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Information Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-start space-x-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fa-solid fa-file-shield"></i>
        </div>
        <div class="text-xs leading-relaxed text-slate-600">
            <h3 class="text-sm font-bold text-slate-800">Petunjuk Upload Sertifikat Selesai KP</h3>
            <p class="mt-1">
                Unggah hasil pindaian (scan) sertifikat resmi atau surat keterangan selesai pelaksanaan Kerja Praktik dari instansi/perusahaan tempat Anda melaksanakan KP.
            </p>
            <p class="mt-1 text-slate-500">
                Format yang diizinkan: <strong>PDF, JPG, JPEG, PNG</strong> (Ukuran maksimal 5MB). File ini akan diverifikasi keabsahannya oleh Koordinator KP sebagai syarat pendaftaran sidang & yudisium.
            </p>
        </div>
    </div>

    @if($kp)
        <!-- Current Certificate Status & Upload Form -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800">Status Dokumen Sertifikat</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Instansi: {{ $kp->instansi }} &bull; Target Selesai: {{ $kp->tanggal_selesai->format('d M Y') }}</p>
                </div>

                @if($sertifikat)
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $sertifikat->status_verifikasi === 'Valid' ? 'bg-emerald-100 text-emerald-800' : ($sertifikat->status_verifikasi === 'Menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                        Status: {{ $sertifikat->status_verifikasi }}
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                        Belum Diunggah
                    </span>
                @endif
            </div>

            @if($sertifikat)
                <!-- Existing Certificate Details -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <span class="text-slate-400 block font-medium">Tanggal Upload:</span>
                            <span class="text-slate-800 font-bold">{{ $sertifikat->tanggal_upload->format('d F Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Jarak Hari Dari Target Selesai:</span>
                            <span class="text-slate-800 font-bold">{{ $kp->jarak_hari_sertifikat }} Hari</span>
                        </div>
                    </div>

                    @if($sertifikat->catatan_admin)
                        <div class="pt-2 border-t border-slate-200 text-slate-700">
                            <span class="font-bold text-slate-800">Catatan Koordinator KP:</span>
                            <p class="italic mt-0.5">{{ $sertifikat->catatan_admin }}</p>
                        </div>
                    @endif

                    <div class="pt-2 flex items-center space-x-3">
                        <a href="{{ route('storage.local', $sertifikat->file_path) }}" target="_blank"
                           class="inline-flex items-center space-x-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            <span>Buka / Preview Sertifikat</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Form Upload / Re-upload -->
            <form method="POST" action="{{ route('mahasiswa.sertifikat.store') }}" enctype="multipart/form-data" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ $sertifikat ? 'Unggah File Sertifikat Pengganti (Re-upload)' : 'Pilih Berkas Sertifikat (PDF / JPG / PNG)' }}
                    </label>
                    <input type="file" name="file_sertifikat" required accept=".pdf,image/*"
                           class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl p-1.5 transition">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>{{ $sertifikat ? 'Perbarui File Sertifikat' : 'Upload Sertifikat Sekarang' }}</span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm text-xs text-slate-500">
            Anda belum memiliki Kerja Praktik aktif. Silakan ajukan terlebih dahulu di menu Pendaftaran KP.
        </div>
    @endif

</div>
@endsection

