@extends('layouts.app')

@section('title', 'Plotting Dosen Pembimbing KP')
@section('page_heading', 'Plotting & Penetapan Dosen Pembimbing')

@section('content')
<div class="space-y-6" x-data="{ plotModal: false, selectedKp: {} }">

    <!-- Header Summary -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Pengajuan Menunggu Penetapan Dosen</h3>
            <p class="text-xs text-slate-500 mt-0.5">Tinjau usulan mahasiswa dan tetapkan dosen pembimbing dengan memperhatikan kuota masing-masing dosen.</p>
        </div>
        <div class="flex items-center space-x-2 text-xs">
            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 font-bold border border-amber-200">
                {{ $pengajuanList->count() }} Pengajuan Perlu Diproses
            </span>
        </div>
    </div>

    <!-- Pending Plotting Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <h4 class="text-sm font-extrabold text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-hourglass-start text-amber-500"></i>
            <span>Antrean Pengajuan Mahasiswa</span>
        </h4>

        @if($pengajuanList->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                Tidak ada pengajuan KP yang menunggu plotting. Seluruh mahasiswa telah memiliki Dosen Pembimbing.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Mahasiswa & NIM</th>
                            <th class="py-3 px-4">Instansi & Alamat</th>
                            <th class="py-3 px-4">Jadwal KP</th>
                            <th class="py-3 px-4">Usulan Mahasiswa</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pengajuanList as $kp)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-slate-800 text-sm">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</div>
                                    <span class="text-slate-400 font-mono text-[11px] block">NIM: {{ $kp->nim }} &bull; IPK: {{ number_format($kp->mahasiswa->ipk_terakhir ?? 0, 2) }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <div class="font-semibold">{{ $kp->instansi }}</div>
                                    <p class="text-slate-500 text-[11px] truncate max-w-xs">{{ $kp->alamat_instansi ?? '-' }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 whitespace-nowrap">
                                    {{ $kp->tanggal_mulai->format('d/m/Y') }} - {{ $kp->tanggal_selesai->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-medium">
                                        {{ $kp->usulan_dosen ?? 'Tidak mengusulkan' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button type="button"
                                            @click="selectedKp = {{ json_encode($kp) }}; plotModal = true"
                                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center space-x-1.5">
                                        <i class="fa-solid fa-user-check text-xs"></i>
                                        <span>Plotting Dosen</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Active Plotting List -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <h4 class="text-sm font-extrabold text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-list-check text-emerald-600"></i>
            <span>Daftar Bimbingan Aktif (Telah Ditetapkan)</span>
        </h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">Mahasiswa</th>
                        <th class="py-3 px-4">Instansi</th>
                        <th class="py-3 px-4">Dosen Pembimbing Ditetapkan</th>
                        <th class="py-3 px-4 text-center">Status KP</th>
                        <th class="py-3 px-4 text-right">Tanggal Disetujui</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($allKp->where('status_kp', '!=', 'Pengajuan') as $k)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800">{{ $k->mahasiswa->nama_lengkap ?? 'N/A' }}</span>
                                <span class="text-slate-400 font-mono text-[11px] block">{{ $k->nim }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                {{ $k->instansi }}
                            </td>
                            <td class="py-3 px-4 font-bold text-emerald-900">
                                {{ $k->dosen->nama_dosen ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $k->status_kp === 'Berjalan' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $k->status_kp }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-500 font-mono text-[11px]">
                                {{ $k->updated_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Plotting -->
    <div x-show="plotModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="plotModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800">Tetapkan Dosen Pembimbing</h3>
                <button @click="plotModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form :action="'{{ url('admin/plotting') }}/' + selectedKp.id_kp" method="POST" class="space-y-4">
                @csrf
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <span class="text-slate-400 block font-medium">Mahasiswa:</span>
                    <strong class="text-slate-800 font-bold block" x-text="selectedKp.mahasiswa ? selectedKp.mahasiswa.nama_lengkap : ''"></strong>
                    <span class="text-slate-500 block" x-text="'Instansi: ' + selectedKp.instansi"></span>
                    <span class="text-emerald-700 block font-semibold" x-text="'Usulan Mahasiswa: ' + (selectedKp.usulan_dosen || 'Tidak ada')"></span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pilih Dosen Pembimbing <span class="text-rose-500">*</span>
                    </label>
                    <select name="nidn" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">-- Pilih Dosen (Kuota Tersedia) --</option>
                        @foreach($dosenList as $d)
                            <option value="{{ $d->nidn }}">
                                {{ $d->nama_dosen }} (Sisa: {{ $d->sisa_kuota }} Mhs)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Penetapan Koordinator (Opsional)
                    </label>
                    <textarea name="catatan_koordinator" rows="2"
                              placeholder="Contoh: Disetujui. Silakan segera melakukan bimbingan perdana..."
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="plotModal = false" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md">Tetapkan & Aktifkan KP</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

