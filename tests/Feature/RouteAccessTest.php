<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test aksesibilitas semua route/halaman dalam aplikasi.
 * Memverifikasi bahwa halaman publik dapat diakses, halaman admin terlindungi.
 */
class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Test: Halaman Publik
    // -------------------------------------------------------

    /** @test */
    public function landing_page_dapat_diakses(): void
    {
        $this->get('/')->assertStatus(200);
    }

    /** @test */
    public function halaman_beranda_dapat_diakses(): void
    {
        $this->get('/app')->assertStatus(200);
    }

    /** @test */
    public function halaman_riwayat_dapat_diakses(): void
    {
        $this->get('/riwayat')->assertStatus(200);
    }

    /** @test */
    public function halaman_detail_riwayat_dapat_diakses(): void
    {
        // Halaman ini hanya render blade — data diambil via JS/API
        $this->get('/riwayat/1')->assertStatus(200);
    }

    /** @test */
    public function halaman_pengaturan_dapat_diakses(): void
    {
        $this->get('/pengaturan')->assertStatus(200);
    }

    /** @test */
    public function halaman_akun_dapat_diakses(): void
    {
        $this->get('/akun')->assertStatus(200);
    }

    /** @test */
    public function halaman_login_dapat_diakses(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    // -------------------------------------------------------
    // Test: Halaman Admin — Akses Kontrol
    // -------------------------------------------------------

    /** @test */
    public function halaman_admin_redirect_ke_login_untuk_tamu(): void
    {
        $response = $this->get('/admin');

        // Middleware auth harus redirect ke halaman login
        $response->assertRedirect('/login');
    }

    /** @test */
    public function halaman_admin_ditolak_untuk_user_biasa(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    /** @test */
    public function halaman_admin_dapat_diakses_oleh_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    // -------------------------------------------------------
    // Test: API Route — Metode HTTP yang Salah
    // -------------------------------------------------------

    /** @test */
    public function api_translate_menolak_metode_get(): void
    {
        $response = $this->getJson('/api/translate');

        $response->assertStatus(405); // Method Not Allowed
    }

    /** @test */
    public function api_login_menolak_metode_get(): void
    {
        $response = $this->getJson('/api/login');

        $response->assertStatus(405);
    }

    /** @test */
    public function api_logout_menolak_metode_get(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/logout');

        $response->assertStatus(405);
    }

    // -------------------------------------------------------
    // Test: Route yang Tidak Ada
    // -------------------------------------------------------

    /** @test */
    public function route_tidak_ada_mengembalikan_404(): void
    {
        $this->get('/halaman-tidak-ada')->assertStatus(404);
    }
}
