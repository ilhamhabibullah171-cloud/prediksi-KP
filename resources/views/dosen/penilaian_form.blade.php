@extends('layouts.app')

@section('title', 'Rubrik Penilaian KP')
@section('page_heading', 'Form Rubrik Penilaian Kerja Praktik')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="penilaianForm()">

    <!-- Student & Institution Header -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-mono">
                <span>{{ $kp->nim }}</span>
                <span>&bull;</span>
                <span class="font-semibold text-emerald-700">Prodi {{ $kp->mahasiswa->prodi ?? 'Teknik Informatika' }}</span>
            </div>
            <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $kp->mahasiswa->nama_lengkap ?? 'N/A' }}</h3>
            <p class="text-xs text-slate-500 mt-1">
                Lokasi: <strong class="text-slate-700">{{ $kp->instansi }}</strong> &bull; Total Logbook Approved: {{ $kp->logbook_approved_count }} / 30 Hari
            </p>
        </div>

        <a href="{{ route('dosen.penilaian.index') }}"
           class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
            &larr; Kembali
        </a>
    </div>

    <!-- Live Grade Calculator Card -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-700 rounded-3xl p-6 sm:p-8 text-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <span class="text-xs uppercase tracking-widest font-semibold text-emerald-300">Kalkulasi Otomatis Rubrik</span>
            <h2 class="text-xl font-extrabold mt-1">Nilai Akhir & Konversi Grade Akademik</h2>
            <p class="text-xs text-emerald-100 mt-1 max-w-md">
                Bobot Nilai: <strong>40%</strong> Pembimbing Instansi + <strong>60%</strong> Dosen Pembimbing (Rata-rata Disiplin, Laporan, & Presentasi).
            </p>
        </div>
        <div class="bg-white/10 backdrop-blur-md px-8 py-4 rounded-2xl border border-white/20 text-center flex-shrink-0">
            <span class="text-[11px] text-emerald-200 uppercase font-bold block">Grade Kelulusan</span>
            <div class="text-5xl font-black text-white my-1" x-text="grade">A</div>
            <span class="text-xs text-emerald-100 font-mono">Skor Akhir: <strong x-text="nilaiAkhir.toFixed(2)">0.00</strong></span>
        </div>
    </div>

    <!-- Rubric Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('dosen.penilaian.store', $kp->id_kp) }}" class="space-y-6">
            @csrf

            <!-- Preset & Quick Fill Bar -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <span class="font-bold text-slate-700 flex items-center space-x-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-emerald-600"></i>
                    <span>Isi Cepat Nilai Komponen:</span>
                </span>
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" @click="setPreset(95)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 font-bold text-xs transition">
                        95 (A)
                    </button>
                    <button type="button" @click="setPreset(90)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 font-bold text-xs transition">
                        90 (A)
                    </button>
                    <button type="button" @click="setPreset(85)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 font-bold text-xs transition">
                        85 (A)
                    </button>
                    <button type="button" @click="setPreset(80)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 font-bold text-xs transition">
                        80 (A-)
                    </button>
                    <button type="button" @click="setPreset(75)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 font-bold text-xs transition">
                        75 (B+)
                    </button>
                    <button type="button" @click="setPreset(0)" class="px-2.5 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                        Reset (0)
                    </button>
                </div>
            </div>

            <!-- Section 1: Nilai Pembimbing Instansi -->
            <div class="space-y-4 pb-6 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-800">1. Nilai Pembimbing Lapangan / Instansi</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Nilai yang tercantum pada surat/evaluasi instansi tempat mahasiswa KP (Bobot 40%).</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">Bobot 40%</span>
                </div>

                <div class="max-w-xs">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Skor Instansi (0 - 100) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.1" min="0" max="100" name="nilai_pembimbing_instansi"
                           x-model.number="nilaiInstansi" @input="calculate()" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <!-- Section 2: Rubrik Nilai Dosen Pembimbing -->
            <div class="space-y-4 pb-6 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-800">2. Rubrik Evaluasi Dosen Pembimbing</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Rata-rata 3 aspek berikut berkontribusi 60% terhadap nilai akhir.</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">Bobot 60%</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Aspek 1: Disiplin & Logbook -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kedisiplinan & Logbook <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai_disiplin"
                               x-model.number="nilaiDisiplin" @input="calculate()" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <span class="text-[11px] text-slate-400 mt-1 block">Kepatuhan 30 hari logbook.</span>
                    </div>

                    <!-- Aspek 2: Laporan KP -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kualitas Laporan KP <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai_laporan"
                               x-model.number="nilaiLaporan" @input="calculate()" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <span class="text-[11px] text-slate-400 mt-1 block">Kelengkapan format & teknis.</span>
                    </div>

                    <!-- Aspek 3: Presentasi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Presentasi / Sidang <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai_presentasi"
                               x-model.number="nilaiPresentasi" @input="calculate()" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <span class="text-[11px] text-slate-400 mt-1 block">Penguasaan materi & demo.</span>
                    </div>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex justify-between items-center text-slate-700">
                    <span>Rata-rata Skor Dosen Pembimbing:</span>
                    <strong class="font-mono text-sm text-slate-900" x-text="nilaiDosen.toFixed(2)">0.00</strong>
                </div>
            </div>

            <!-- Section 3: Catatan Evaluasi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Catatan Evaluasi / Rekomendasi Pembimbing (Opsional)
                </label>
                <textarea name="catatan_evaluasi" rows="3"
                          placeholder="Tuliskan catatan apresiasi, kritik membangun, atau catatan kelulusan..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none leading-relaxed transition">{{ old('catatan_evaluasi', $penilaian->catatan_evaluasi ?? '') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('dosen.penilaian.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan & Terbitkan Nilai Akhir</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function penilaianForm() {
        return {
            nilaiInstansi: {{ $penilaian ? $penilaian->nilai_pembimbing_instansi : 0 }},
            nilaiDisiplin: {{ $penilaian ? $penilaian->nilai_disiplin : 0 }},
            nilaiLaporan: {{ $penilaian ? $penilaian->nilai_laporan : 0 }},
            nilaiPresentasi: {{ $penilaian ? $penilaian->nilai_presentasi : 0 }},
            nilaiDosen: {{ $penilaian ? $penilaian->nilai_dosen_pembimbing : 0 }},
            nilaiAkhir: {{ $penilaian ? $penilaian->nilai_akhir : 0 }},
            grade: '{{ $penilaian ? $penilaian->grade : '-' }}',
            setPreset(val) {
                this.nilaiInstansi = val;
                this.nilaiDisiplin = val;
                this.nilaiLaporan = val;
                this.nilaiPresentasi = val;
                this.calculate();
            },
            calculate() {
                const i = parseFloat(this.nilaiInstansi) || 0;
                const d1 = parseFloat(this.nilaiDisiplin) || 0;
                const d2 = parseFloat(this.nilaiLaporan) || 0;
                const d3 = parseFloat(this.nilaiPresentasi) || 0;

                this.nilaiDosen = (d1 + d2 + d3) / 3;
                this.nilaiAkhir = (0.4 * i) + (0.6 * this.nilaiDosen);

                const score = this.nilaiAkhir;
                if (score <= 0) this.grade = '-';
                else if (score >= 85) this.grade = 'A';
                else if (score >= 80) this.grade = 'A-';
                else if (score >= 75) this.grade = 'B+';
                else if (score >= 70) this.grade = 'B';
                else if (score >= 65) this.grade = 'B-';
                else if (score >= 60) this.grade = 'C+';
                else if (score >= 55) this.grade = 'C';
                else if (score >= 40) this.grade = 'D';
                else this.grade = 'E';
            },
            init() {
                if ({{ $penilaian ? 'true' : 'false' }}) {
                    this.calculate();
                }
            }
        }
    }
</script>
@endsection

