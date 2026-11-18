<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\KerjaPraktik;
use App\Models\Logbook;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($credentials['username']);
        $password = $credentials['password'];

        // 1. Percobaan login menggunakan username langsung
        if (Auth::attempt(['username' => $loginInput, 'password' => $password], $request->boolean('remember'))) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
            $user = Auth::user();
            return $this->redirectBasedOnRole($user);
        }

        // 2. Percobaan login menggunakan NIM (jika username berbeda atau mahasiswa memasukkan NIM)
        $userByNim = User::whereHas('mahasiswa', function ($q) use ($loginInput) {
            $q->where('nim', $loginInput);
        })->first();

        if ($userByNim && Hash::check($password, $userByNim->password)) {
            Auth::login($userByNim, $request->boolean('remember'));
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
            return $this->redirectBasedOnRole($userByNim);
        }

        return back()->withErrors([
            'username' => 'Username/NIM atau password yang dimasukkan salah.',
        ])->onlyInput('username');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:30',
            'nama_lengkap' => 'required|string|max:150',
            'prodi' => 'required|string|in:Teknik Informatika,Sistem Informasi',
            'angkatan' => 'required|integer|min:2018|max:2030',
            'username' => 'nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nim.required' => 'Nomor Induk Mahasiswa (NIM) wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap mahasiswa wajib diisi.',
            'prodi.required' => 'Program Studi wajib dipilih.',
            'angkatan.required' => 'Tahun angkatan masuk wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',
        ]);

        $nim = trim($validated['nim']);
        $username = !empty($validated['username']) ? trim($validated['username']) : $nim;

        // 1. Cari atau buat akun pengguna (User)
        $user = User::where('username', $username)
            ->orWhere('username', $nim)
            ->orWhereHas('mahasiswa', function ($q) use ($nim) {
                $q->where('nim', $nim);
            })
            ->first();

        if ($user) {
            // Perbarui password dan nama akun yang sudah ada (misal data hasil upload admin)
            $user->update([
                'username' => $username,
                'name' => $validated['nama_lengkap'],
                'password' => Hash::make($validated['password']),
                'role' => 'mahasiswa',
            ]);
        } else {
            // Buat akun baru jika belum pernah terdaftar
            $user = User::create([
                'username' => $username,
                'name' => $validated['nama_lengkap'],
                'password' => Hash::make($validated['password']),
                'role' => 'mahasiswa',
            ]);
        }

        // 2. Hubungkan atau buat profil Mahasiswa
        $mahasiswa = Mahasiswa::updateOrCreate(
            ['nim' => $nim],
            [
                'user_id' => $user->id,
                'nama_lengkap' => $validated['nama_lengkap'],
                'prodi' => $validated['prodi'],
                'angkatan' => $validated['angkatan'],
                'ipk_terakhir' => $request->input('ipk_terakhir', 3.50),
                'sks_lulus' => $request->input('sks_lulus', 100),
            ]
        );

        // 3. Pastikan Mahasiswa memiliki record Kerja Praktik (KP) awal dengan instansi & jadwal kosong
        $kp = KerjaPraktik::where('nim', $nim)->first();
        if (!$kp) {
            $dosen = Dosen::first();
            $nidn = $dosen ? $dosen->nidn : '0715088201';

            $kp = KerjaPraktik::create([
                'nim' => $nim,
                'nidn' => $nidn,
                'instansi' => null,
                'alamat_instansi' => null,
                'tanggal_mulai' => null,
                'tanggal_selesai' => null,
                'status_kp' => 'Pengajuan',
                'catatan_koordinator' => 'Akun mahasiswa baru terdaftar. Menunggu penetapan tempat dan jadwal KP oleh Admin Fakultas.',
            ]);
        }

        return redirect()->route('login')->with('success', "Akun mahasiswa Anda berhasil dibuat! Username: {$username}. Silakan masuk menggunakan username dan password yang baru Anda daftarkan.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            default => redirect()->route('login')->with('error', 'Peran akun pengguna tidak dikenali oleh sistem.'),
        };
    }
}
