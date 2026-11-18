@extends('layouts.app')

@section('title', 'Kelola Data Mahasiswa')
@section('page_heading', 'Master Data Mahasiswa')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, uploadModal: false, editModal: false, editData: {} }">

    <!-- Header Actions -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-extrabold text-slate-800">Data Master Mahasiswa FT UIM</h3>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data mahasiswa, program studi, dan akun resmi mahasiswa Fakultas Teknik UIM.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" @click="uploadModal = true"
                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                <i class="fa-solid fa-file-csv text-xs"></i>
                <span>Upload Data (CSV)</span>
            </button>
            <button type="button" @click="createModal = true"
                    class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>+ Tambah Manual</span>
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">NIM & Mahasiswa</th>
                        <th class="py-3 px-4">Prodi & Angkatan</th>
                        <th class="py-3 px-4 text-center">IPK Terakhir</th>
                        <th class="py-3 px-4 text-center">SKS Lulus</th>
                        <th class="py-3 px-4">No. HP</th>
                        <th class="py-3 px-4 text-center">Status KP</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($mahasiswaList as $mhs)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-800 text-sm">{{ $mhs->nama_lengkap }}</div>
                                <span class="text-slate-400 font-mono text-[11px] block">{{ $mhs->nim }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <div>{{ $mhs->prodi }}</div>
                                <span class="text-slate-400 text-[11px]">Angkatan {{ $mhs->angkatan }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800 font-mono">
                                {{ number_format($mhs->ipk_terakhir, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                {{ $mhs->sks_lulus }} SKS
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $mhs->no_hp ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($mhs->activeKp)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $mhs->activeKp->status_kp === 'Berjalan' ? 'bg-emerald-100 text-emerald-800' : ($mhs->activeKp->status_kp === 'Selesai' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $mhs->activeKp->status_kp }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum KP</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <button type="button"
                                            @click="editData = {{ json_encode($mhs) }}; editModal = true"
                                            class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.mahasiswa.delete', $mhs->nim) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini?')"
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

    <!-- Modal Create -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="createModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800">Tambah Mahasiswa Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.mahasiswa.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIM *</label>
                        <input type="text" name="nim" required placeholder="Contoh: 21063110011"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Angkatan *</label>
                        <input type="number" name="angkatan" value="2022" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" required placeholder="Nama lengkap mahasiswa"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">IPK Terakhir (ML Input) *</label>
                        <input type="number" step="0.01" min="0" max="4.0" name="ipk_terakhir" value="3.25" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SKS Lulus (ML Input) *</label>
                        <input type="number" min="0" max="160" name="sks_lulus" value="105" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Program Studi *</label>
                        <input type="text" name="prodi" value="Teknik Informatika" required
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
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800" x-text="'Edit Mahasiswa NIM: ' + editData.nim"></h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form :action="'{{ url('admin/mahasiswa') }}/' + editData.nim" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" x-model="editData.nama_lengkap" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Angkatan</label>
                        <input type="number" name="angkatan" x-model="editData.angkatan" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Prodi</label>
                        <input type="text" name="prodi" x-model="editData.prodi" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">IPK Terakhir</label>
                        <input type="number" step="0.01" min="0" max="4.0" name="ipk_terakhir" x-model="editData.ipk_terakhir" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">SKS Lulus</label>
                        <input type="number" name="sks_lulus" x-model="editData.sks_lulus" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono">
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

    <!-- Modal Upload File Mahasiswa CSV -->
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="uploadModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block">Admin Fakultas Teknik</span>
                    <h3 class="text-base font-extrabold text-slate-800">Upload Data Mahasiswa (CSV)</h3>
                </div>
                <button @click="uploadModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-xs text-blue-900 space-y-1">
                <p class="font-bold flex items-center space-x-1.5">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Format Kolom Berkas CSV:</span>
                </p>
                <code class="block bg-white p-2 rounded-lg border border-blue-200 font-mono text-[11px] text-blue-800">
                    NIM,Nama Lengkap,Angkatan,IPK,SKS,Prodi,NoHP
                </code>
                <p class="text-[11px] text-blue-700 mt-1">
                    Password default mahasiswa yang diunggah akan disetel otomatis: <strong class="font-mono text-blue-900">password123</strong>
                </p>
            </div>

            <form method="POST" action="{{ route('admin.mahasiswa.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pilih File CSV Mahasiswa <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="file_csv" required accept=".csv,.txt"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" @click="uploadModal = false" class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                        <span>Unggah & Proses Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

