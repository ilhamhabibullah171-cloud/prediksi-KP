@extends('layouts.app')

@section('title', 'Logbook Tracker 30 Hari')
@section('page_heading', 'Logbook Tracker 30 Hari Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Student Header & Summary -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-slate-400 font-mono">{{ $kp->nim }}</span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs text-slate-500 font-semibold">{{ $kp->instansi }}</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</h2>
            <p class="text-xs text-slate-500 mt-1">
                Jadwal KP: {{ $kp->tanggal_mulai->format('d M Y') }} s/d {{ $kp->tanggal_selesai->format('d M Y') }} &bull; Target: 30 Hari Pelaksanaan
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.logbook.detail', $kp->id_kp) }}"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                Buka Validasi &rarr;
            </a>
        </div>
    </div>

    <!-- Tracker Matrix Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div>
            <h3 class="text-base font-extrabold text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                <span>Matriks Visual Pemantauan 30 Hari</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Dosen dapat langsung menganalisis pola kedisiplinan: apakah mahasiswa mengisi harian secara konsisten atau terdapat hari-hari yang terlewat (bolong).
            </p>
        </div>

        <!-- Progress Summary -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
            <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200">
                <span class="text-slate-500 block text-[11px]">Approved (Valid)</span>
                <strong class="text-lg text-emerald-800 font-black">{{ $kp->logbook_approved_count }} Hari</strong>
            </div>
            <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200">
                <span class="text-slate-500 block text-[11px]">Pending Review</span>
                <strong class="text-lg text-amber-800 font-black">{{ $kp->logbooks->where('status_validasi', 'Pending')->count() }} Hari</strong>
            </div>
            <div class="p-3 rounded-2xl bg-orange-50 border border-orange-200">
                <span class="text-slate-500 block text-[11px]">Revisi</span>
                <strong class="text-lg text-orange-800 font-black">{{ $kp->logbooks->where('status_validasi', 'Revisi')->count() }} Hari</strong>
            </div>
            <div class="p-3 rounded-2xl bg-rose-50 border border-rose-200">
                <span class="text-slate-500 block text-[11px]">Terlewat (Bolong)</span>
                <strong class="text-lg text-rose-800 font-black">{{ $kp->hari_terlewat_count }} Hari</strong>
            </div>
        </div>

        <!-- 30-Day Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-10 gap-3 pt-2">
            @foreach($days as $day)
                @php
                    $status = $day['status'];
                    $color = match($status) {
                        'Approved' => 'bg-emerald-500 text-white border-emerald-600 shadow-sm shadow-emerald-600/20',
                        'Pending' => 'bg-amber-400 text-amber-950 border-amber-500 shadow-sm shadow-amber-500/20',
                        'Revisi' => 'bg-orange-500 text-white border-orange-600 shadow-sm shadow-orange-600/20',
                        'Terlewat' => 'bg-rose-500 text-white border-rose-600 shadow-sm shadow-rose-600/20',
                        'Hari Ini' => 'bg-blue-600 text-white border-blue-700 animate-pulse',
                        default => 'bg-slate-100 text-slate-400 border-slate-200 opacity-60',
                    };
                @endphp
                <div class="p-3 rounded-2xl border text-center flex flex-col justify-between h-24 {{ $color }}">
                    <div class="text-[11px] font-black tracking-tight">H-{{ $day['hari_ke'] }}</div>
                    <div class="my-auto">
                        <span class="text-[10px] block opacity-90">{{ $day['tanggal']->format('d/m') }}</span>
                        <span class="text-[9px] uppercase font-bold tracking-tighter block mt-0.5">{{ $status }}</span>
                    </div>
                    <div class="text-[9px] opacity-80">
                        @if($day['entry'])
                            <i class="fa-solid fa-check"></i>
                        @else
                            <i class="fa-solid fa-minus"></i>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap items-center justify-center gap-4 pt-4 border-t border-slate-100 text-xs text-slate-600">
            <span class="flex items-center space-x-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-emerald-500"></span>
                <span>Approved</span>
            </span>
            <span class="flex items-center space-x-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-amber-400"></span>
                <span>Pending Review</span>
            </span>
            <span class="flex items-center space-x-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-orange-500"></span>
                <span>Revisi</span>
            </span>
            <span class="flex items-center space-x-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-rose-500"></span>
                <span>Terlewat (Bolong)</span>
            </span>
            <span class="flex items-center space-x-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-slate-200"></span>
                <span>Belum Tiba</span>
            </span>
        </div>
    </div>

</div>
@endsection

