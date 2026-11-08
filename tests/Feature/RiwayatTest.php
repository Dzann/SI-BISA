<?php

namespace Tests\Feature;

use App\Models\SesiTerjemah;
use App\Models\Terjemahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test proteksi IDOR pada endpoint GET /api/riwayat/{id}.
 * Memastikan pengguna tidak bisa mengakses data milik orang lain.
 */
class RiwayatTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Helper: buat sesi + terjemahan untuk user tertentu
    // -------------------------------------------------------

    private function buatTerjemahan(?User $user, string $teks = 'halo'): Terjemahan
    {
        $sesi = SesiTerjemah::create([
            'user_id' => $user?->id,
            'browser' => 'Test Browser',
            'mulai'   => now(),
        ]);

        return Terjemahan::create([
            'sesi_id'         => $sesi->id,
            'teks_asli'       => $teks,
            'teks_stemming'   => $teks,
            'confidence'      => null,
            'waktu_proses_ms' => 10,
            'created_at'      => now(),
        ]);
    }

    // -------------------------------------------------------
    // Test: GET /api/riwayat/{id}
    // -------------------------------------------------------

    /** @test */
    public function detail_riwayat_dapat_diakses_oleh_user_pemilik(): void
    {
        $user = User::factory()->create();
        $terjemahan = $this->buatTerjemahan($user);

        $response = $this->actingAs($user)->getJson('/api/riwayat/' . $terjemahan->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['terjemahan', 'detail']);
    }

    /** @test */
    public function detail_riwayat_ditolak_untuk_user_lain_idor(): void
    {
        $pemilik = User::factory()->create();
        $orang_lain = User::factory()->create();

        $terjemahan = $this->buatTerjemahan($pemilik);

        // Orang lain mencoba akses data milik pemilik → harus 403
        $response = $this->actingAs($orang_lain)->getJson('/api/riwayat/' . $terjemahan->id);

        $response->assertStatus(403);
    }

    /** @test */
    public function detail_riwayat_admin_dapat_akses_semua_data(): void
    {
        $pemilik = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $terjemahan = $this->buatTerjemahan($pemilik);

        // Admin harus bisa akses data siapapun
        $response = $this->actingAs($admin)->getJson('/api/riwayat/' . $terjemahan->id);

        $response->assertStatus(200);
    }

    /** @test */
    public function detail_riwayat_404_untuk_id_yang_tidak_ada(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/riwayat/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function detail_riwayat_anonim_dapat_akses_dengan_session_id_benar(): void
    {
        $terjemahan = $this->buatTerjemahan(null);

        $response = $this->getJson('/api/riwayat/' . $terjemahan->id . '?session_id=' . $terjemahan->sesi_id);

        $response->assertStatus(200);
    }

    /** @test */
    public function detail_riwayat_anonim_ditolak_dengan_session_id_salah(): void
    {
        $terjemahan = $this->buatTerjemahan(null);

        // Session ID yang salah harus ditolak
        $response = $this->getJson('/api/riwayat/' . $terjemahan->id . '?session_id=99999');

        $response->assertStatus(403);
    }

    // -------------------------------------------------------
    // Test: GET /api/riwayat (daftar)
    // -------------------------------------------------------

    /** @test */
    public function riwayat_list_tamu_tanpa_session_id_mengembalikan_kosong(): void
    {
        // Buat data agar ada di DB, namun tamu tanpa session_id tidak boleh melihat
        $this->buatTerjemahan(null);

        $response = $this->getJson('/api/riwayat');

        $response->assertStatus(200)
                 ->assertExactJson([]);
    }

    /** @test */
    public function riwayat_list_dapat_dicari_dengan_query_q(): void
    {
        $user = User::factory()->create();
        $this->buatTerjemahan($user, 'selamat pagi');
        $this->buatTerjemahan($user, 'selamat malam');

        $response = $this->actingAs($user)->getJson('/api/riwayat?q=pagi');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertStringContainsString('pagi', $data[0]['teks_asli']);
    }
}
