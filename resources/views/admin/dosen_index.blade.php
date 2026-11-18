@extends('layouts.app')

@section('title', 'Kelola Data Dosen')
@section('page_heading', 'Master Data Dosen Pembimbing')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, editData: {} }">

    <!-- Header Actions -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Dosen Pembimbing KP FT UIM</h3>
            <p class="text-xs text-slate-500 mt-0.5">Kelola NIDN, kuota kapasitas bimbingan mahasiswa, dan akun login pembimbing.</p>
        </div>
        <button type="button" @click="createModal = true"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Daftarkan Dosen Baru</span>
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">NIDN & Nama Dosen</th>
                        <th class="py-3 px-4">Jabatan Akademik</th>
                        <th class="py-3 px-4 text-center">Kuota Bimbingan</th>
                        <th class="py-3 px-4 text-center">Bimbingan Aktif</th>
                        <th class="py-3 px-4 text-center">Sisa Kuota</th>
                        <th class="py-3 px-4">Kontak / HP</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($dosenList as $d)
                        @php
                            $aktif = $d->kerjaPraktik->where('status_kp', 'Berjalan')->count();
                            $sisa = max(0, $d->kuota_bimbingan - $aktif);
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-800 text-sm">{{ $d->nama_dosen }}</div>
                                <span class="text-slate-400 font-mono text-[11px] block">{{ $d->nidn }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                {{ $d->jabatan_akademik ?? 'Tenaga Pengajar' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                {{ $d->kuota_bimbingan }} Mahasiswa
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-emerald-700">
                                {{ $aktif }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] {{ $sisa > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $sisa }} Tersedia
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $d->no_hp ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <button type="button"
                                            @click="editData = {{ json_encode($d) }}; editModal = true"
                                            class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.dosen.delete', $d->nidn) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data dosen ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create Dosen -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="createModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800">Daftarkan Dosen Pembimbing Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.dosen.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIDN *</label>
                        <input type="text" name="nidn" required placeholder="07xxxxxxxx"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kuota Bimbingan *</label>
                        <input type="number" name="kuota_bimbingan" value="10" min="1" max="50" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Dosen Lengkap & Gelar *</label>
                    <input type="text" name="nama_dosen" required placeholder="Contoh: Dr. H. Fulan, M.Kom."
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jabatan Akademik</label>
                        <input type="text" name="jabatan_akademik" placeholder="Lektor / Asisten Ahli"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp/HP</label>
                        <input type="text" name="no_hp" placeholder="08xxxxxxxxxx"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password Akun (Default: password123)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika default"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md">Simpan Data Dosen</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Dosen -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800" x-text="'Edit Dosen NIDN: ' + editData.nidn"></h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form :action="'{{ url('admin/dosen') }}/' + editData.nidn" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Dosen Lengkap & Gelar *</label>
                    <input type="text" name="nama_dosen" x-model="editData.nama_dosen" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kuota Bimbingan *</label>
                        <input type="number" name="kuota_bimbingan" x-model="editData.kuota_bimbingan" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jabatan Akademik</label>
                        <input type="text" name="jabatan_akademik" x-model="editData.jabatan_akademik"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp/HP</label>
                    <input type="text" name="no_hp" x-model="editData.no_hp"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

