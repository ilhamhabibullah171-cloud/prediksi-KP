@extends('layouts.app')

@section('title', 'Cetak Sertifikat Mahasiswa KP - Dosen Pembimbing')
@section('page_heading', 'Cetak Sertifikat Mahasiswa KP')

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-emerald-500/10 blur-xl"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-widest font-semibold text-emerald-300 block">Modul Cetak Sertifikat</span>
                <h1 class="text-xl sm:text-2xl font-extrabold mt-1">Penerbitan & Cetak Sertifikat KP</h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 mt-1 max-w-2xl">
                    Cetak sertifikat resmi selesai Kerja Praktik berformat standar Fakultas Teknik Universitas Islam Madura (UIM) yang dilengkapi nomor registrasi dan tanda tangan resmi.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 text-emerald-200 text-xs font-semibold border border-emerald-400/30 flex items-center space-x-2">
                    <i class="fa-solid fa-certificate"></i>
                    <span>Format A4 Landscape Siap Cetak</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Table of Students -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-graduation-cap text-emerald-600"></i>
                <span>Mahasiswa Bimbingan yang Memenuhi Syarat Sertifikat</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Mahasiswa</th>
                        <th class="py-3.5 px-4">Tempat Instansi KP</th>
                        <th class="py-3.5 px-4 text-center">Status KP</th>
                        <th class="py-3.5 px-4 text-center">Nilai Akhir & Grade</th>
                        <th class="py-3.5 px-4 text-center">Kelayakan Sertifikat</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bimbinganList as $idx => $kp)
                        @php
                            $hasNilai = (bool) $kp->penilaian;
                            $nilai = $kp->penilaian;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4 text-center font-medium text-slate-400">{{ $idx + 1 }}</td>
                            
                            <!-- Mahasiswa info -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800 text-sm">
                                    {{ $kp->mahasiswa->nama_lengkap ?? 'Tanpa Nama' }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[11px] font-mono text-slate-400">
                                        NIM: {{ $kp->nim }}
                                    </span>
                                    @if(($kp->mahasiswa->prodi ?? '') === 'Sistem Informasi')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">SI</span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-700">TI</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Instansi -->
                            <td class="py-4 px-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $kp->instansi }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($kp->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($kp->tanggal_selesai)->format('d M Y') }}
                                </div>
                            </td>

                            <!-- Status KP -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full font-bold text-[11px] {{ $kp->status_kp === 'Selesai' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    {{ $kp->status_kp }}
                                </span>
                            </td>

                            <!-- Nilai -->
                            <td class="py-4 px-4 text-center">
                                @if($hasNilai)
                                    <div class="font-extrabold text-slate-800 text-sm">
                                        {{ number_format($nilai->nilai_akhir, 2) }}
                                    </div>
                                    <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px] mt-0.5">
                                        Grade: {{ $nilai->grade }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum Dinilai</span>
                                @endif
                            </td>

                            <!-- Kelayakan -->
                            <td class="py-4 px-4 text-center">
                                @if($hasNilai || $kp->status_kp === 'Selesai')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px] border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>Siap Cetak</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-medium text-[11px]">
                                        <i class="fa-solid fa-hourglass text-[10px]"></i>
                                        <span>Menunggu Nilai</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('dosen.sertifikat.cetak', $kp->id_kp) }}" target="_blank"
                                       class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center space-x-1.5">
                                        <i class="fa-solid fa-print text-xs"></i>
                                        <span>Cetak Sertifikat</span>
                                    </a>

                                    @if($hasNilai)
                                        <a href="{{ route('dosen.penilaian.cetak', $kp->id_kp) }}" target="_blank"
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition" title="Cetak Berita Acara Nilai">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-certificate text-3xl mb-2 block"></i>
                                <span>Belum ada mahasiswa bimbingan yang terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

