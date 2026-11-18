@extends('layouts.app')

@section('title', 'Profil Belum Lengkap')
@section('content')
<div class="max-w-md mx-auto my-12 bg-white rounded-3xl p-8 border border-slate-200 shadow-sm text-center space-y-4">
    <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mx-auto">
        <i class="fa-solid fa-user-gear"></i>
    </div>
    <h3 class="text-base font-bold text-slate-800">Data Mahasiswa Belum Ditemukan</h3>
    <p class="text-xs text-slate-500">
        Akun ini belum terhubung dengan data master mahasiswa (NIM). Silakan hubungi Admin Prodi / Koordinator KP untuk melengkapi biodata akademik Anda.
    </p>
</div>
@endsection

