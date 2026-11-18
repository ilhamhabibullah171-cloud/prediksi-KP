@extends('layouts.app')

@section('title', 'Validasi E-Logbook Mahasiswa')
@section('page_heading', 'Monitoring & Validasi E-Logbook Mingguan')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Daftar Mahasiswa Bimbingan KP</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pilih mahasiswa untuk memeriksa dan memvalidasi logbook kegiatan harian 7 hari selama 30 hari.</p>
        </div>
        <a href="{{ route('dosen.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($bimbinganList as $kp)
            @php
                $pendingCount = $kp->logbooks->where('status_validasi', 'Pending')->count();
            @endphp
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-1.5">
                            <span class="text-[11px] font-mono text-slate-400 font-bold">{{ $kp->nim }}</span>
                            @if(($kp->mahasiswa->prodi ?? '') === 'Sistem Informasi')
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">SI</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-700 border border-teal-200">TI</span>
                            @endif
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $kp->status_kp === 'Selesai' ? 'bg-blue-100 text-blue-800' : ($kp->status_kp === 'Berjalan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $kp->status_kp }}
                        </span>
                    </div>

                    <div>
                        <h4 class="text-base font-bold text-slate-800">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $kp->instansi ?? 'Menunggu Penetapan Admin' }}</p>
                    </div>

                    <!-- Progress bar -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1">
                            <span>Logbook Terisi</span>
                            <span>{{ $kp->logbook_count }} / 30 Hari</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-teal-500 h-full" style="width: {{ min(100, $kp->rasio_logbook) }}%"></div>
                        </div>
                    </div>

                    @if($pendingCount > 0)
                        <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-center space-x-2">
                            <i class="fa-solid fa-bell text-amber-600 animate-bounce"></i>
                            <span>{{ $pendingCount }} Logbook Menunggu Validasi!</span>
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-end">
                    <a href="{{ route('dosen.logbook.detail', $kp->id_kp) }}"
                       class="w-full text-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center space-x-2">
                        <span>Periksa E-Logbook Mingguan</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-3xl p-8 text-center text-slate-400 text-xs border border-slate-200">
                Belum ada mahasiswa bimbingan yang terdaftar.
            </div>
        @endforelse
    </div>

</div>
@endsection
