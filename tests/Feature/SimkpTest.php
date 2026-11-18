<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\KerjaPraktik;

class SimkpTest extends TestCase
{
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIMKP & Prediksi Kelulusan (EWS)');
    }

    public function test_admin_dashboard_renders_successfully(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Early Warning System');
    }

    public function test_dosen_dashboard_and_tracker_render_successfully(): void
    {
        $dosen = User::where('role', 'dosen')->first();
        $response = $this->actingAs($dosen)->get('/dosen/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal Dosen Pembimbing');

        $kp = KerjaPraktik::where('nidn', $dosen->dosen->nidn)->first();
        if ($kp) {
            $respTracker = $this->actingAs($dosen)->get("/dosen/tracker/{$kp->id_kp}");
            $respTracker->assertStatus(200);
            $respTracker->assertSee('Matriks Visual Pemantauan 30 Hari');
        }
    }

    public function test_mahasiswa_dashboard_and_logbook_render_successfully(): void
    {
        $mhs = User::where('role', 'mahasiswa')->first();
        $response = $this->actingAs($mhs)->get('/mahasiswa/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Early Warning System');

        $respLogbook = $this->actingAs($mhs)->get('/mahasiswa/logbook');
        $respLogbook->assertStatus(200);
        $respLogbook->assertSee('Matriks E-Logbook 30 Hari');
    }

    public function test_berita_acara_renders_successfully(): void
    {
        $admin = User::where('role', 'admin')->first();
        $kpWithGrade = KerjaPraktik::whereHas('penilaian')->first();
        if ($kpWithGrade) {
            $response = $this->actingAs($admin)->get("/admin/rekapitulasi/{$kpWithGrade->id_kp}/berita-acara");
            $response->assertStatus(200);
            $response->assertSee('BERITA ACARA NILAI AKHIR KERJA PRAKTIK');
        }
    }
}

