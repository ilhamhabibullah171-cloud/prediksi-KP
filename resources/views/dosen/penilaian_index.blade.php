@extends('layouts.app')

@section('title', 'Penilaian Kerja Praktik')
@section('page_heading', 'Input & Kelola Penilaian Kerja Praktik')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Daftar Penilaian Mahasiswa Bimbingan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Input nilai evaluasi akhir berdasarkan rubrik instansi, kedisiplinan logbook, laporan, dan presentasi.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        @if($bimbinganList->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                Belum ada data mahasiswa bimbingan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3.5 px-4">Mahasiswa</th>
                            <th class="py-3.5 px-4">Instansi</th>
                            <th class="py-3.5 px-4 text-center">Status Sertifikat</th>
                            <th class="py-3.5 px-4 text-center">Logbook</th>
                            <th class="py-3.5 px-4 text-center">Nilai Akhir</th>
                            <th class="py-3.5 px-4 text-center">Grade</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($bimbinganList as $kp)
                            @php $pen = $kp->penilaian; @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-slate-800 text-sm">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</div>
                                    <div class="flex items-center space-x-1.5 mt-0.5">
                                        <span class="text-slate-400 font-mono text-[11px]">{{ $kp->nim }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold {{ $kp->mahasiswa && $kp->mahasiswa->prodi === 'Sistem Informasi' ? 'bg-purple-100 text-purple-800' : 'bg-teal-100 text-teal-800' }}">
                                            {{ $kp->mahasiswa->prodi ?? 'Teknik Informatika' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-700">
                                    {{ $kp->instansi }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($kp->sertifikat)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $kp->sertifikat->status_verifikasi === 'Valid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $kp->sertifikat->status_verifikasi }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum upload</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold">
                                    {{ $kp->logbook_approved_count }} / 30 Hari
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($pen && $pen->nilai_akhir)
                                        <span class="font-mono font-bold text-slate-800 text-sm">{{ number_format($pen->nilai_akhir, 2) }}</span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($pen && $pen->grade)
                                        <span class="inline-block w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 font-black text-sm leading-7">
                                            {{ $pen->grade }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <a href="{{ route('dosen.penilaian.form', $kp->id_kp) }}"
                                           class="px-3 py-1.5 {{ $pen ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs' }} font-bold text-xs rounded-xl transition inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-pen-ruler text-xs"></i>
                                            <span>{{ $pen ? 'Edit Nilai' : 'Input Nilai' }}</span>
                                        </a>

                                        @if($pen && $pen->nilai_akhir)
                                            <a href="{{ route('dosen.penilaian.cetak', $kp->id_kp) }}" target="_blank"
                                               class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl transition inline-flex items-center space-x-1 border border-blue-200" title="Cetak Berita Acara & Lembar Nilai">
                                                <i class="fa-solid fa-file-invoice text-xs"></i>
                                                <span class="hidden sm:inline">Cetak Nilai</span>
                                            </a>
                                            <a href="{{ route('dosen.sertifikat.cetak', $kp->id_kp) }}" target="_blank"
                                               class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl transition inline-flex items-center space-x-1 border border-emerald-200" title="Cetak Sertifikat">
                                                <i class="fa-solid fa-certificate text-xs"></i>
                                                <span class="hidden sm:inline">Sertifikat</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

