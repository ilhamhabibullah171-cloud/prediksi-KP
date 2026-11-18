@extends('layouts.app')

@section('title', 'Rekapitulasi Nilai & Kelulusan KP')
@section('page_heading', 'Rekapitulasi Nilai & Yudisium KP')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Print Buttons -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Rekapitulasi Nilai Akhir & Kelulusan KP</h3>
            <p class="text-xs text-slate-500 mt-0.5">Laporan rekapitulasi nilai evaluasi Kerja Praktik untuk keperluan sidang yudisium Fakultas Teknik UIM.</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('admin.rekapitulasi.cetak-rekap') }}" target="_blank"
               class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Cetak Rekap Yudisium (PDF)</span>
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Mahasiswa Dievaluasi</span>
                <strong class="text-2xl font-black text-slate-800 mt-1 block">{{ $totalEvaluated }}</strong>
                <span class="text-[11px] text-slate-500">Telah memiliki nilai akhir</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Rata-rata Nilai Akhir</span>
                <strong class="text-2xl font-black text-blue-600 mt-1 block">{{ number_format($averageNilai, 2) }}</strong>
                <span class="text-[11px] text-slate-500">Skala 0 - 100</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Mahasiswa KP</span>
                <strong class="text-2xl font-black text-slate-800 mt-1 block">{{ $kpList->count() }}</strong>
                <span class="text-[11px] text-slate-500">Termasuk yang sedang berjalan</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">NIM & Mahasiswa</th>
                        <th class="py-3 px-3">Instansi Tempat KP</th>
                        <th class="py-3 px-3">Dosen Pembimbing</th>
                        <th class="py-3 px-3 text-center">Nilai Instansi</th>
                        <th class="py-3 px-3 text-center">Nilai Dosen</th>
                        <th class="py-3 px-3 text-center">Nilai Akhir</th>
                        <th class="py-3 px-3 text-center">Grade</th>
                        <th class="py-3 px-3 text-right">Berita Acara</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kpList as $index => $kp)
                        @php $pen = $kp->penilaian; @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3 font-mono text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-800 text-sm">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</div>
                                <span class="text-slate-400 font-mono text-[11px]">{{ $kp->nim }}</span>
                            </td>
                            <td class="py-3 px-3 text-slate-700 font-medium">
                                {{ $kp->instansi }}
                            </td>
                            <td class="py-3 px-3 text-slate-700">
                                {{ $kp->dosen->nama_dosen ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono">
                                {{ $pen ? number_format($pen->nilai_pembimbing_instansi, 1) : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono">
                                {{ $pen ? number_format($pen->nilai_dosen_pembimbing, 1) : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-extrabold text-sm text-slate-800">
                                {{ $pen ? number_format($pen->nilai_akhir, 2) : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($pen && $pen->grade)
                                    <span class="inline-block w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 font-black text-sm leading-7">
                                        {{ $pen->grade }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right whitespace-nowrap">
                                @if($pen)
                                    <a href="{{ route('admin.rekapitulasi.berita-acara', $kp->id_kp) }}" target="_blank"
                                       class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition text-xs">
                                        <i class="fa-solid fa-print text-xs"></i>
                                        <span>Cetak Berita Acara</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum ada nilai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada data rekapitulasi Kerja Praktik.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

