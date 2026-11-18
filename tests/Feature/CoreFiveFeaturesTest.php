<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\KerjaPraktik;

class CoreFiveFeaturesTest extends TestCase
{
    public function test_login_page_renders_cleanly()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login Mahasiswa');
        $response->assertSee('Login Dosen');
        $response->assertDontSee('Administrator');
        $response->assertDontSee('Admin');
    }

    public function test_mahasiswa_dashboard_renders_all_5_features()
    {
        $user = User::where('role', 'mahasiswa')->first();
        if (!$user) {
            $this->markTestSkipped('No mahasiswa user found.');
        }

        $response = $this->actingAs($user)->get('/mahasiswa/dashboard');
        $response->assertStatus(200);
        // 5 Core Features
        $response->assertSee('Form & Status Pendaftaran KP');
        $response->assertSee('E-Logbook Mingguan');
        $response->assertSee('Penilaian Kerja Praktik');
        $response->assertSee('Cetak Sertifikat Kerja Praktik');
        $response->assertSee('Prediksi Kelulusan Mahasiswa KP (EWS)');
    }

    public function test_dosen_dashboard_renders_all_5_features()
    {
        $user = User::where('role', 'dosen')->first();
        if (!$user) {
            $this->markTestSkipped('No dosen user found.');
        }

        $response = $this->actingAs($user)->get('/dosen/dashboard');
        $response->assertStatus(200);
        // 5 Core Features
        $response->assertSee('Pendaftaran KP');
        $response->assertSee('E-Logbook 30 Hari');
        $response->assertSee('Penilaian KP');
        $response->assertSee('Cetak Sertifikat');
        $response->assertSee('Prediksi EWS');
        $response->assertSee('+ Tambah Data Mahasiswa KP');
    }

    public function test_weekly_logbook_for_mahasiswa()
    {
        $user = User::where('role', 'mahasiswa')->first();
        if (!$user) {
            $this->markTestSkipped('No mahasiswa user found.');
        }

        $response = $this->actingAs($user)->get('/mahasiswa/logbook');
        $response->assertStatus(200);
        $response->assertSee('Minggu 1');
        $response->assertSee('Hari 1 - 7');
    }

    public function test_cetak_sertifikat_view_renders()
    {
        $kp = KerjaPraktik::first();
        if (!$kp) {
            $this->markTestSkipped('No KP found.');
        }

        $dosenUser = User::where('role', 'dosen')->first();
        $response = $this->actingAs($dosenUser)->get("/dosen/sertifikat/{$kp->id_kp}/cetak");
        $response->assertStatus(200);
        $response->assertSee('SERTIFIKAT KERJA PRAKTIK');
    }

    public function test_cetak_penilaian_view_renders()
    {
        $kp = KerjaPraktik::whereHas('penilaian')->first();
        if (!$kp) {
            $this->markTestSkipped('No KP with penilaian found.');
        }

        $dosenUser = User::where('role', 'dosen')->first();
        $response = $this->actingAs($dosenUser)->get("/dosen/penilaian/{$kp->id_kp}/cetak");
        $response->assertStatus(200);
        $response->assertSee('BERITA ACARA & LEMBAR PENILAIAN');
    }
}

