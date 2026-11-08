<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test autentikasi: login, logout, sesi, dan rate limiting.
 * Menggunakan SQLite in-memory agar tidak mencemari database produksi.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Helper: buat user biasa dan user admin
    // -------------------------------------------------------

    private function buatUser(array $override = []): User
    {
        return User::factory()->create(array_merge(['password' => bcrypt('password123')], $override));
    }

    private function buatAdmin(): User
    {
        return User::factory()->admin()->create(['password' => bcrypt('password123')]);
    }

    // -------------------------------------------------------
    // Test: POST /api/login
    // -------------------------------------------------------

    /** @test */
    public function login_berhasil_dengan_kredensial_valid(): void
    {
        $user = $this->buatUser(['email' => 'user@test.com']);

        $response = $this->postJson('/api/login', [
            'email'    => 'user@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'role'])
                 ->assertJson(['message' => 'Login berhasil', 'role' => 'user']);
    }

    /** @test */
    public function login_admin_mengembalikan_role_admin(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->postJson('/api/login', [
            'email'    => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['role' => 'admin']);
    }

    /** @test */
    public function login_gagal_dengan_kata_sandi_salah(): void
    {
        $this->buatUser(['email' => 'user@test.com']);

        $response = $this->postJson('/api/login', [
            'email'    => 'user@test.com',
            'password' => 'salah_password',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['message' => 'Email atau kata sandi salah']);
    }

    /** @test */
    public function login_gagal_tanpa_email(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function login_gagal_tanpa_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'user@test.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function login_gagal_dengan_email_format_tidak_valid(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'bukan-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function login_gagal_email_tidak_terdaftar(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'tidakada@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['message' => 'Email atau kata sandi salah']);
    }

    // -------------------------------------------------------
    // Test: GET /api/me
    // -------------------------------------------------------

    /** @test */
    public function me_mengembalikan_data_user_saat_sudah_login(): void
    {
        $user = $this->buatUser();

        $response = $this->actingAs($user)->getJson('/api/me');

        $response->assertStatus(200)
                 ->assertJsonStructure(['name', 'email', 'role'])
                 ->assertJson([
                     'name'  => $user->name,
                     'email' => $user->email,
                 ]);
    }

    /** @test */
    public function me_mengembalikan_null_untuk_tamu(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
                 ->assertExactJson([]);
    }

    // -------------------------------------------------------
    // Test: POST /api/logout
    // -------------------------------------------------------

    /** @test */
    public function logout_berhasil_ketika_sudah_login(): void
    {
        $user = $this->buatUser();

        $response = $this->actingAs($user)->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Keluar']);
    }

    /** @test */
    public function logout_ditolak_untuk_tamu(): void
    {
        // Route /api/logout dilindungi middleware auth
        $response = $this->postJson('/api/logout');

        // Laravel redirect ke login (302) atau JSON 401 tergantung konfigurasi
        $this->assertContains($response->status(), [302, 401, 403]);
    }

    // -------------------------------------------------------
    // Test: Rate Limiting POST /api/login
    // -------------------------------------------------------

    /** @test */
    public function login_rate_limited_setelah_6_percobaan_gagal(): void
    {
        $payload = ['email' => 'brute@test.com', 'password' => 'salah'];

        // Lakukan 6 percobaan gagal (sesuai throttle:6,1 di routes)
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/login', $payload);
        }

        // Percobaan ke-7 harus mendapat 429 Too Many Requests
        $response = $this->postJson('/api/login', $payload);
        $response->assertStatus(429);
    }
}
