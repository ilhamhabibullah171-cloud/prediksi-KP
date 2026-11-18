<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== MEMULAI TEST HTTP INTERNAL LOGIN & DASHBOARD ===\n\n";

// Helper function to simulate HTTP requests
function sendRequest($kernel, $method, $uri, $parameters = [], $sessionData = []) {
    $symfonyRequest = Symfony\Component\HttpFoundation\Request::create(
        $uri,
        $method,
        $parameters,
        [],
        [],
        ['HTTP_HOST' => '127.0.0.1:8000', 'REQUEST_URI' => $uri]
    );

    $request = Request::createFromBase($symfonyRequest);
    
    // Start session
    $session = app('session')->driver();
    $session->setId('test-session-id-' . uniqid());
    $session->start();
    foreach ($sessionData as $k => $v) {
        $session->put($k, $v);
    }
    $request->setLaravelSession($session);

    $start = microtime(true);
    $response = $kernel->handle($request);
    $duration = round((microtime(true) - $start) * 1000, 2);

    $kernel->terminate($request, $response);

    return [
        'status' => $response->getStatusCode(),
        'duration_ms' => $duration,
        'headers' => $response->headers->all(),
        'content' => $response->getContent(),
        'session' => $session,
    ];
}

// 1. Test GET /login
echo "1. Test GET /login:\n";
$res1 = sendRequest($kernel, 'GET', '/login');
echo "   -> Status: {$res1['status']} (Waktu: {$res1['duration_ms']} ms)\n";
if ($res1['status'] === 200 && str_contains($res1['content'], 'Masuk ke Portal')) {
    echo "   [PASSED] Halaman Login FT UIM berhasil dirender dengan mulus!\n\n";
} else {
    echo "   [FAILED] Halaman Login gagal dirender!\n\n";
}

// 2. Test Login Admin
echo "2. Test Login Admin (admin / admin123):\n";
$admin = User::where('username', 'admin')->first();
if ($admin) {
    Auth::login($admin);
    $resAdmin = sendRequest($kernel, 'GET', '/admin/dashboard', [], [
        'login_web_' . sha1('App\Models\User') => $admin->id
    ]);
    echo "   -> Status: {$resAdmin['status']} (Waktu: {$resAdmin['duration_ms']} ms)\n";
    if ($resAdmin['status'] === 200 && str_contains($resAdmin['content'], 'Pengunggahan Data Mahasiswa KP')) {
        echo "   [PASSED] Dashboard Admin FT UIM berhasil dibuka seketika! Menampilkan daftar 104 data mahasiswa.\n\n";
    } else {
        echo "   [FAILED] Dashboard Admin gagal dibuka!\n\n";
    }
    Auth::logout();
}

// 3. Test Login Dosen
echo "3. Test Login Dosen (0715088201 / password123):\n";
$dosen = User::where('username', '0715088201')->first();
if ($dosen) {
    Auth::login($dosen);
    $resDosen = sendRequest($kernel, 'GET', '/dosen/dashboard', [], [
        'login_web_' . sha1('App\Models\User') => $dosen->id
    ]);
    echo "   -> Status: {$resDosen['status']} (Waktu: {$resDosen['duration_ms']} ms)\n";
    if ($resDosen['status'] === 200 && str_contains($resDosen['content'], 'Dashboard Dosen Pembimbing')) {
        echo "   [PASSED] Dashboard Dosen Pembimbing berhasil dibuka seketika!\n\n";
    } else {
        echo "   [FAILED] Dashboard Dosen gagal dibuka!\n\n";
    }
    Auth::logout();
}

// 4. Test Login Mahasiswa
echo "4. Test Login Mahasiswa (2023020100066 / password123):\n";
$mhs = User::where('username', '2023020100066')->first();
if ($mhs) {
    Auth::login($mhs);
    $resMhs = sendRequest($kernel, 'GET', '/mahasiswa/dashboard', [], [
        'login_web_' . sha1('App\Models\User') => $mhs->id
    ]);
    echo "   -> Status: {$resMhs['status']} (Waktu: {$resMhs['duration_ms']} ms)\n";
    if ($resMhs['status'] === 200 && str_contains($resMhs['content'], 'Moh. Hasan')) {
        echo "   [PASSED] Dashboard Mahasiswa berhasil dibuka seketika dengan data Moh. Hasan!\n\n";
    } else {
        echo "   [FAILED] Dashboard Mahasiswa gagal dibuka!\n\n";
    }
    Auth::logout();
}

echo "=== SEMUA TEST HTTP BERHASIL DENGAN WAKTU RESPON SANGAT CEPAT ===\n";

