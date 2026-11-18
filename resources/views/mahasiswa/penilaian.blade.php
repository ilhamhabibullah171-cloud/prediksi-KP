@extends('layouts.app')

@section('title', 'Rincian Penilaian KP')
@section('page_heading', 'Hasil Penilaian Kerja Praktik')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(!$penilaian)
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-200 shadow-sm space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mx-auto">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Nilai Belum Diterbitkan</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Dosen Pembimbing atau Koordinator KP belum menginput rubrik evaluasi nilai akhir untuk pelaksanaan Kerja Praktik Anda. Silakan selesaikan pengisian logbook 30 hari dan upload sertifikat instansi.
            </p>
        </div>
    @else
        <!-- Final Grade Banner -->
        <div class="bg-gradient-to-r from-emerald-800 to-teal-700 rounded-3xl p-6 sm:p-8 text-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest font-semibold text-emerald-300">Hasil Evaluasi Yudisium KP</span>
                <h2 class="text-2xl font-extrabold mt-1">Selamat, Penilaian KP Telah Divalidasi!</h2>
                <p class="text-xs text-emerald-100 mt-1 max-w-lg">
                    Fakultas Teknik Universitas Islam Madura menyatakan mahasiswa telah menyelesaikan mata kuliah Kerja Praktik.
                </p>
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <a href="{{ route('mahasiswa.sertifikat.cetak') }}" target="_blank"
                       class="px-4 py-2 bg-white text-emerald-900 hover:bg-emerald-50 rounded-xl font-bold text-xs shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-certificate text-emerald-600"></i>
                        <span>Cetak Sertifikat KP</span>
                    </a>
                    <a href="{{ route('mahasiswa.penilaian.cetak') }}" target="_blank"
                       class="px-4 py-2 bg-emerald-900/60 hover:bg-emerald-900/80 text-white rounded-xl font-bold text-xs border border-white/20 transition flex items-center space-x-2">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>Cetak Lembar Nilai</span>
                    </a>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20 text-center flex-shrink-0">
                <span class="text-xs text-emerald-200 uppercase font-bold block">Nilai Akhir (Grade)</span>
                <div class="text-5xl font-black text-white my-1">{{ $penilaian->grade }}</div>
                <span class="text-xs text-emerald-100 font-mono">Skor: {{ number_format($penilaian->nilai_akhir, 2) }} / 100</span>
            </div>
        </div>

        <!-- Rubric Breakdown -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-extrabold text-slate-800 pb-3 border-b border-slate-100 flex items-center space-x-2">
                <i class="fa-solid fa-list-ol text-emerald-600"></i>
                <span>Rincian Rubrik Penilaian Akademik</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Nilai Instansi -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">1. Pembimbing Lapangan / Instansi</span>
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-semibold">Bobot 40%</span>
                    </div>
                    <div class="text-2xl font-black text-slate-800">
                        {{ number_format($penilaian->nilai_pembimbing_instansi, 2) }}
                    </div>
                    <p class="text-slate-500 text-[11px]">Aspek etika kerja, kontribusi, dan kedisiplinan di instansi/perusahaan.</p>
                </div>

                <!-- Nilai Dosen Rata-rata -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">2. Dosen Pembimbing KP</span>
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-semibold">Bobot 60%</span>
                    </div>
                    <div class="text-2xl font-black text-slate-800">
                        {{ number_format($penilaian->nilai_dosen_pembimbing, 2) }}
                    </div>
                    <p class="text-slate-500 text-[11px]">Rata-rata aspek kedisiplinan logbook, laporan teknis, dan presentasi.</p>
                </div>
            </div>

            <!-- Detailed Sub-aspects -->
            <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100 space-y-3 text-xs">
                <h4 class="font-bold text-emerald-950">Aspek Evaluasi Oleh Dosen:</h4>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="p-3 rounded-xl bg-white border border-emerald-200">
                        <span class="text-slate-400 block text-[11px]">Kedisiplinan & Logbook</span>
                        <strong class="text-base text-slate-800 font-extrabold">{{ number_format($penilaian->nilai_disiplin ?? 0, 1) }}</strong>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-emerald-200">
                        <span class="text-slate-400 block text-[11px]">Laporan KP</span>
                        <strong class="text-base text-slate-800 font-extrabold">{{ number_format($penilaian->nilai_laporan ?? 0, 1) }}</strong>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-emerald-200">
                        <span class="text-slate-400 block text-[11px]">Presentasi / Sidang</span>
                        <strong class="text-base text-slate-800 font-extrabold">{{ number_format($penilaian->nilai_presentasi ?? 0, 1) }}</strong>
                    </div>
                </div>
            </div>

            @if($penilaian->catatan_evaluasi)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                    <span class="font-bold text-slate-700 block mb-1">Catatan Evaluasi Pembimbing:</span>
                    <p class="text-slate-600 italic leading-relaxed">{{ $penilaian->catatan_evaluasi }}</p>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection

