# SIMKP & EWS - Sistem Informasi Kerja Praktik & Prediksi Kelulusan (Early Warning System)
### Studi Kasus: Fakultas Teknik Universitas Islam Madura (UIM)

Sistem Informasi Kerja Praktik (SIMKP) terintegrasi **Machine Learning Early Warning System (EWS)** untuk memantau aktivitas pelaksanaan Kerja Praktik 30 hari, kedisiplinan logbook harian, kelengkapan berkas sertifikat instansi, dan memprediksi risiko kelulusan mahasiswa secara dini.

---

## 1. Arsitektur & Tech Stack

- **Backend (Web & Business Logic)**: PHP 8.2 + Laravel 11 (MVC, Eloquent ORM, Middleware Role Auth)
- **Frontend**: HTML5, Tailwind CSS, FontAwesome 6, Alpine.js, ApexCharts (Interaktif Donut & Bar Charts)
- **Machine Learning Service**: Python 3.12, scikit-learn, joblib, numpy, Flask Microservice (`ml_service/app.py` & `predict.py`)
  - **Model**: `RandomForestClassifier` (`model.joblib`)
  - **Kelas Prediksi**: `Tepat Waktu`, `Terlambat`, `Gagal`
  - **Fitur Input**:
    1. `ipk_terakhir` (Indeks Prestasi Kumulatif)
    2. `sks_lulus` (Jumlah SKS Lulus)
    3. `rasio_kepatuhan_logbook` (Persentase kepatuhan logbook 30 hari: `[terisi / 30] * 100%`)
    4. `jarak_hari_sertifikat` (Selisih hari antara tanggal selesai KP dengan upload sertifikat)
    5. `jumlah_hari_terlewat` (Jumlah hari kalender KP yang tidak diisi / bolong)
- **Database**: SQLite (Ready-to-use out of the box) & support MySQL / PostgreSQL via `.env`

---

## 2. Struktur Tabel & Database

1. `users`: Autentikasi terpusat (`id`, `username`, `password`, `role`: `'mahasiswa'`, `'dosen'`, `'admin'`).
2. `mahasiswa`: Data akademik mahasiswa (`nim` PK, `user_id`, `nama_lengkap`, `angkatan`, `ipk_terakhir`, `sks_lulus`, `prodi`, `no_hp`).
3. `dosen`: Data dosen pembimbing (`nidn` PK, `user_id`, `nama_dosen`, `kuota_bimbingan`, `jabatan_akademik`, `no_hp`).
4. `kerja_praktik`: Transaksi utama KP (`id_kp` PK, `nim`, `nidn`, `instansi`, `alamat_instansi`, `tanggal_mulai`, `tanggal_selesai`, `status_kp`: `'Pengajuan'`, `'Berjalan'`, `'Selesai'`, `'Batal'`).
5. `logbook`: Entri harian 30 hari (`id_logbook` PK, `id_kp`, `hari_ke` 1-30, `tanggal_kegiatan`, `deskripsi_pekerjaan`, `dokumentasi`, `status_validasi`: `'Pending'`, `'Approved'`, `'Revisi'`, `catatan_dosen`, `waktu_submit`).
6. `sertifikat`: Berkas selesai KP (`id_sertifikat` PK, `id_kp`, `file_path`, `tanggal_upload`, `status_verifikasi`: `'Menunggu'`, `'Valid'`, `'Ditolak'`, `catatan_admin`).
7. `penilaian`: Evaluasi rubrik yudisium (`id_nilai` PK, `id_kp`, `nilai_pembimbing_instansi` [40%], `nilai_dosen_pembimbing` [60%]: `nilai_disiplin`, `nilai_laporan`, `nilai_presentasi`, `nilai_akhir`, `grade`: A/B/C/D/E).
8. `hasil_prediksi_ml`: Riwayat prediksi AI EWS (`id_prediksi` PK, `id_kp`, 5 fitur input, `label_prediksi`, `status_code`: AMAN/RENTAN/BAHAYA, `probabilitas_sukses`, `detail_probabilitas`, `rekomendasi_ews`, `tanggal_update_prediksi`).

---

## 3. Fitur Utama Berdasarkan Role

### A. Role: Mahasiswa
- **Dashboard EWS**: Menampilkan kartu status prediksi AI (Aman, Rentan, Bahaya), peluang kelulusan %, progress bar e-logbook 30 hari, dan **Notifikasi Peringatan Harian** jika belum mengisi logbook hari ini.
- **Pendaftaran & Pengajuan KP**: Validasi prasyarat akademik (IPK & SKS), form lokasi instansi, durasi 30 hari, dan usulan dosen pembimbing.
- **E-Logbook 30 Hari**: Matriks visual 30 hari kalender, formulir input kegiatan harian + lampiran dokumentasi foto, sistem otomatis penanda *Terlewat (Locked)*, catatan revisi dosen, dan auto-trigger prediksi real-time saat submit.
- **Upload Sertifikat**: Form upload pindaian sertifikat/surat selesai KP (PDF/JPG/PNG), hitung otomatis jarak hari keterlambatan dari tanggal target selesai.
- **Lihat Penilaian**: Rincian nilai evaluasi (Instansi 40%, Dosen 60%), Nilai Akhir, Grade Huruf (A, B+, dll.), dan catatan evaluasi yudisium.

### B. Role: Dosen Pembimbing
- **Dashboard Dosen**: Statistik bimbingan, pemantauan kapasitas kuota bimbingan, jumlah logbook pending review, dan notifikasi mahasiswa berisiko (EWS Warning).
- **Monitoring & Validasi E-Logbook**: Membaca kegiatan harian mahasiswa, melihat berkas dokumentasi, tombol Approve atau Revisi dengan masukan komentar teknis.
- **Logbook Tracker 30 Hari**: Matriks visual kalender 30 hari per mahasiswa (Hijau = Approved, Kuning = Pending, Orange = Revisi, Merah = Terlewat/Bolong) untuk memantau kedisiplinan dan deteksi mahasiswa yang submit borongan di akhir.
- **Input Penilaian KP**: Form rubrik penilaian (Nilai Instansi, Aspek Kedisiplinan & Logbook, Aspek Kualitas Laporan, Aspek Ujian/Presentasi), kalkulasi otomatis nilai akhir dan konversi grade huruf resmi.

### C. Role: Admin Prodi / Koordinator KP
- **Dashboard EWS**: Donut chart distribusi prediksi mahasiswa (% Aman, % Rentan, % Bahaya), tabel peringatan mahasiswa berisiko tinggi, tombol sinkronisasi prediksi massal.
- **Kelola Data Master**: CRUD Data Mahasiswa & Data Dosen (otomatis mengelola akun login pengguna).
- **Plotting Bimbingan**: Menetapkan dosen pembimbing bagi pengajuan mahasiswa dengan visualisasi sisa kuota bimbingan dosen.
- **Verifikasi Sertifikat**: Memeriksa file sertifikat instansi yang diunggah mahasiswa (Setujui / Tolak dengan catatan).
- **Rekapitulasi Nilai & Yudisium**:
  - Cetak **Berita Acara Nilai Akhir KP** resmi (lengkap dengan Kop Surat FT UIM, rubrik, dan tanda tangan Dekan & Pembimbing).
  - Cetak **Rekapitulasi Nilai Yudisium** format landscape untuk sidang yudisium fakultas.
- **Monitoring Prediksi AI Lengkap**: Matriks lengkap 5 fitur input dan probabilitas sukses Machine Learning untuk seluruh mahasiswa.

---

## 4. Cara Menjalankan Aplikasi

### Menjalankan Web Server (Laravel):
```bash
php artisan serve
```
Buka browser di: `http://127.0.0.1:8000`

### (Opsional) Menjalankan Microservice Machine Learning (Flask):
```bash
python ml_service/app.py
```
*Catatan: Sistem telah dilengkapi fitur **Dual-Layer Auto Fallback**. Jika service Flask HTTP tidak berjalan, Laravel secara otomatis mengeksekusi script CLI `predict.py` di latar belakang sehingga prediksi EWS tetap 100% berfungsi tanpa kendala!*

### Menjalankan Prediksi EWS via Artisan CLI:
```bash
# Prediksi seluruh mahasiswa aktif
php artisan ml:predict

# Prediksi mahasiswa tertentu berdasarkan ID KP
php artisan ml:predict 1
```

---

## 5. Akun Demo Untuk Pengujian (1-Click Quick Login Tersedia)

Halaman login (`/login`) telah dilengkapi dengan tombol **1-Click Quick Demo Login**:

| Role | Username | Password | Deskripsi Akun Demo |
|---|---|---|---|
| **Admin Prodi** | `admin` | `admin123` | Koordinator KP & Dekanat FT UIM |
| **Dosen Pembimbing** | `0715088201` | `password123` | Dr. H. Akhmad Fauzi, M.Kom. |
| **Mahasiswa (Aman)** | `21063110001` | `password123` | Ahmad Muzakki (Prediksi: Tepat Waktu, Logbook 100%) |
| **Mahasiswa (Rentan)** | `21063110005` | `password123` | Syarif Hidayatullah (Prediksi: Terlambat, Logbook bolong) |
| **Mahasiswa (Bahaya)** | `21063110008` | `password123` | Dani Firmansyah (Prediksi: Gagal, Resiko Kritis) |
