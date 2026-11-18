@extends('layouts.app')

@section('title', 'Verifikasi Sertifikat Selesai KP')
@section('page_heading', 'Verifikasi Berkas Sertifikat Selesai KP')

@section('content')
<div class="space-y-6" x-data="{ verifModal: false, selectedCert: {} }">

    <!-- Header Summary -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Verifikasi Sertifikat Instansi Mahasiswa</h3>
            <p class="text-xs text-slate-500 mt-0.5">Periksa keabsahan surat keterangan/sertifikat selesai KP sebagai prasyarat sidang dan yudisium kelulusan.</p>
        </div>
        <div class="flex items-center space-x-2 text-xs">
            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 font-bold border border-amber-200">
                {{ $sertifikatList->where('status_verifikasi', 'Menunggu')->count() }} Menunggu Verifikasi
            </span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        @if($sertifikatList->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                Belum ada mahasiswa yang mengunggah sertifikat selesai KP.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Mahasiswa</th>
                            <th class="py-3 px-4">Instansi Tempat KP</th>
                            <th class="py-3 px-4">Tanggal Upload & Gap</th>
                            <th class="py-3 px-4">Berkas Scan</th>
                            <th class="py-3 px-4 text-center">Status Verifikasi</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sertifikatList as $cert)
                            @php $kp = $cert->kerjaPraktik; @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-slate-800 text-sm">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</div>
                                    <span class="text-slate-400 font-mono text-[11px] block">NIM: {{ $kp->nim }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-700">
                                    {{ $kp->instansi }}
                                    <span class="text-slate-400 text-[10px] block">Dosen: {{ $kp->dosen->nama_dosen ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <div>{{ $cert->tanggal_upload->format('d/m/Y') }}</div>
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        Selisih: {{ $kp->jarak_hari_sertifikat }} hari dari target
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('storage.local', $cert->file_path) }}" target="_blank"
                                       class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                        <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                        <span>Buka Dokumen</span>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px] {{ $cert->status_verifikasi === 'Valid' ? 'bg-emerald-100 text-emerald-800' : ($cert->status_verifikasi === 'Menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $cert->status_verifikasi }}
                                    </span>
                                    @if($cert->catatan_admin)
                                        <p class="text-[10px] text-slate-400 italic mt-0.5 truncate max-w-xs mx-auto">
                                            "{{ $cert->catatan_admin }}"
                                        </p>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <button type="button"
                                            @click="selectedCert = {{ json_encode($cert) }}; verifModal = true"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-check-double text-xs"></i>
                                        <span>Verifikasi</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Modal Verifikasi -->
    <div x-show="verifModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="verifModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800">Verifikasi Dokumen Sertifikat</h3>
                <button @click="verifModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form :action="'{{ url('admin/sertifikat') }}/' + selectedCert.id_sertifikat + '/verify'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Verifikasi Berkas <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center space-x-2 p-3 rounded-xl border border-emerald-300 bg-emerald-50 cursor-pointer">
                            <input type="radio" name="status_verifikasi" value="Valid" checked class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold text-emerald-900">Setujui (Valid)</span>
                        </label>
                        <label class="flex items-center space-x-2 p-3 rounded-xl border border-rose-300 bg-rose-50 cursor-pointer">
                            <input type="radio" name="status_verifikasi" value="Ditolak" class="text-rose-600 focus:ring-rose-500">
                            <span class="text-xs font-bold text-rose-900">Tolak Berkas</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Verifikator Koordinator (Opsional)
                    </label>
                    <textarea name="catatan_admin" rows="2"
                              placeholder="Contoh: Berkas sah, cap basah dan tanda tangan pimpinan instansi lengkap..."
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                              x-model="selectedCert.catatan_admin"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="verifModal = false" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

