<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// Authentication (Login Mahasiswa, Dosen Pembimbing, dan Admin FT)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Buat Akun Mahasiswa KP (Registrasi Mahasiswa)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// -------------------------------------------------------------
// PORTAL ADMIN FAKULTAS TEKNIK (UPLOAD DATA MELALUI TEMPLATE)
// -------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Fitur Download Template & Import File XLSX / CSV (Nama, NIM, Prodi, Nilai Dosen Pembimbing)
    Route::get('/mahasiswa/template-xlsx', [AdminController::class, 'downloadTemplateXlsx'])->name('mahasiswa.template-xlsx');
    Route::get('/mahasiswa/data104-xlsx', [AdminController::class, 'downloadData104Xlsx'])->name('mahasiswa.data104-xlsx');
    Route::post('/mahasiswa/import-xlsx', [AdminController::class, 'importXlsx'])->name('mahasiswa.import-xlsx');
    Route::post('/mahasiswa/{id_kp}/instansi', [AdminController::class, 'updateInstansi'])->name('mahasiswa.update-instansi');
    Route::delete('/mahasiswa/{id_kp}', [AdminController::class, 'deleteMahasiswaKp'])->name('mahasiswa.delete');
});

// -------------------------------------------------------------
// PORTAL MAHASISWA (4 FITUR INTI KP)
// -------------------------------------------------------------
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
    
    // Fitur 1: Form Pendaftaran KP
    Route::get('/pendaftaran', [MahasiswaController::class, 'pendaftaran'])->name('pendaftaran');
    Route::post('/pendaftaran', [MahasiswaController::class, 'storePendaftaran'])->name('pendaftaran.store');
    Route::get('/pendaftaran/cetak', [MahasiswaController::class, 'cetakPendaftaran'])->name('pendaftaran.cetak');

    // Fitur 2: E-Logbook Mingguan (7 Hari per Minggu selama 30 Hari)
    Route::get('/logbook', [MahasiswaController::class, 'logbook'])->name('logbook');
    Route::post('/logbook', [MahasiswaController::class, 'storeLogbook'])->name('logbook.store');

    // Fitur 3: Penilaian KP
    Route::get('/penilaian', [MahasiswaController::class, 'penilaian'])->name('penilaian');
    Route::get('/penilaian/cetak', [MahasiswaController::class, 'cetakNilai'])->name('penilaian.cetak');

    // Fitur 4: Cetak Sertifikat KP
    Route::get('/sertifikat/cetak', [MahasiswaController::class, 'cetakSertifikat'])->name('sertifikat.cetak');
});

// -------------------------------------------------------------
// PORTAL DOSEN PEMBIMBING (4 FITUR INTI KP)
// -------------------------------------------------------------
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
    
    // Fitur 1: Data & Form Pendaftaran Mahasiswa KP
    Route::get('/pendaftaran', [DosenController::class, 'pendaftaranIndex'])->name('pendaftaran.index');
    Route::post('/pendaftaran/tambah', [DosenController::class, 'storeMahasiswaKp'])->name('pendaftaran.store');
    Route::post('/pendaftaran/{id_kp}/status', [DosenController::class, 'updateStatusKp'])->name('pendaftaran.status');
    Route::delete('/pendaftaran/{id_kp}', [DosenController::class, 'deleteMahasiswaKp'])->name('pendaftaran.delete');

    // Fitur 2: E-Logbook Mingguan Mahasiswa (Validasi & Monitoring)
    Route::get('/logbook', [DosenController::class, 'logbookIndex'])->name('logbook.index');
    Route::get('/logbook/{id_kp}', [DosenController::class, 'logbookDetail'])->name('logbook.detail');
    Route::post('/logbook/{id_logbook}/validate', [DosenController::class, 'validateLogbook'])->name('logbook.validate');

    // Fitur 3: Penilaian KP Mahasiswa
    Route::get('/penilaian', [DosenController::class, 'penilaianIndex'])->name('penilaian.index');
    Route::get('/penilaian/{id_kp}', [DosenController::class, 'penilaianForm'])->name('penilaian.form');
    Route::post('/penilaian/{id_kp}', [DosenController::class, 'storePenilaian'])->name('penilaian.store');
    Route::get('/penilaian/{id_kp}/cetak', [DosenController::class, 'cetakNilai'])->name('penilaian.cetak');

    // Fitur 4: Cetak Sertifikat KP Mahasiswa
    Route::get('/sertifikat', [DosenController::class, 'sertifikatIndex'])->name('sertifikat.index');
    Route::get('/sertifikat/{id_kp}/cetak', [DosenController::class, 'cetakSertifikat'])->name('sertifikat.cetak');
});

// Storage fallback route
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*')->name('storage.local');
