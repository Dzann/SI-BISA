<?php

namespace Tests\Feature;

use App\Models\KamusIsyarat;
use App\Models\SesiTerjemah;
use App\Models\Terjemahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test endpoint POST /api/translate dan GET /api/riwayat.
 * Memverifikasi stemming, penyimpanan DB, dan proteksi privasi IDOR.
 */
class TranslateTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Helper: seed data kamus isyarat untuk pengujian
    // -------------------------------------------------------

    private function seedKamus(): void
    {
        KamusIsyarat::insert([
            [
                'kata_dasar' => 'makan',
                'tipe_media' => 'gambar',
                'url_media'  => 'bisindo/makan.png',
                'durasi_ms'  => 2000,
                'is_active'  => true,
                'kategori_id' => null,
            ],
            [
                'kata_dasar' => 'minum',
                'tipe_media' => 'gambar',
                'url_media'  => 'bisindo/minum.png',
                'durasi_ms'  => 2000,
                'is_active'  => true,
                'kategori_id' => null,
            ],
        ]);
    }

    // -------------------------------------------------------
    // Test: POST /api/translate
    // -------------------------------------------------------

    /** @test */
    public function translate_berhasil_dengan_teks_valid(): void
    {
        $this->seedKamus();

        $response = $this->postJson('/api/translate', [
            'text' => 'makan minum',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'session_id',
                     'terjemahan_id',
                     'waktu_proses_ms',
                     'items',
                     'tidak_ditemukan',
                 ]);

        $items = $response->json('items');
        $this->assertCount(2, $items);
        $this->assertEquals('makan', $items[0]['kata_dasar']);
        $this->assertTrue($items[0]['found']);
    }

    /** @test */
    public function translate_gagal_tanpa_teks(): void
    {
        $response = $this->postJson('/api/translate', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['text']);
    }

    /** @test */
    public function translate_gagal_teks_melebihi_1000_karakter(): void
    {
        $response = $this->postJson('/api/translate', [
            'text' => str_repeat('a ', 501), // 1002 karakter
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['text']);
    }

    /** @test */
    public function translate_tidak_menyimpan_ke_db_jika_simpan_false(): void
    {
        $this->seedKamus();

        $response = $this->postJson('/api/translate', [
            'text'   => 'makan',
            'simpan' => false,
        ]);

        $response->assertStatus(200);
        $this->assertNull($response->json('terjemahan_id'));
        $this->assertDatabaseCount('sesi_terjemah', 0);
        $this->assertDatabaseCount('terjemahan', 0);
    }

    /** @test */
    public function translate_menyimpan_ke_db_jika_simpan_true(): void
    {
        $this->seedKamus();

        $response = $this->postJson('/api/translate', [
            'text'   => 'makan minum',
            'simpan' => true,
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('terjemahan_id'));
        $this->assertDatabaseCount('sesi_terjemah', 1);
        $this->assertDatabaseCount('terjemahan', 1);
        $this->assertDatabaseCount('detail_kata', 2);
    }

    /** @test */
    public function translate_stemming_kata_turunan_ke_kata_dasar(): void
    {
        // Seed kata dasar "makan" — kata "makanan" harus di-stem menjadi "makan"
        $this->seedKamus();

        $response = $this->postJson('/api/translate', [
            'text'   => 'makanan',
            'simpan' => false,
        ]);

        $response->assertStatus(200);
        $items = $response->json('items');
        $this->assertCount(1, $items);
        $this->assertEquals('makan', $items[0]['kata_dasar']);
        $this->assertTrue($items[0]['found']);
    }

    /** @test */
    public function translate_kata_tidak_ditemukan_masuk_ke_array_tidak_ditemukan(): void
    {
        $response = $this->postJson('/api/translate', [
            'text'   => 'xyzqwerty',
            'simpan' => false,
        ]);

        $response->assertStatus(200);
        $tidakDitemukan = $response->json('tidak_ditemukan');
        $this->assertNotEmpty($tidakDitemukan);
    }

    /** @test */
    public function translate_confidence_disimpan_ke_database(): void
    {
        $this->seedKamus();

        $this->postJson('/api/translate', [
            'text'       => 'makan',
            'confidence' => 0.95,
            'simpan'     => true,
        ]);

        $this->assertDatabaseHas('terjemahan', [
            'teks_asli'  => 'makan',
            'confidence' => 0.95,
        ]);
    }

    /** @test */
    public function translate_melanjutkan_sesi_yang_sudah_ada(): void
    {
        $sesi = SesiTerjemah::create([
            'user_id' => null,
            'browser' => 'Chrome Test',
            'mulai'   => now(),
        ]);

        $response = $this->postJson('/api/translate', [
            'text'       => 'makan',
            'simpan'     => true,
            'session_id' => $sesi->id,
        ]);

        $response->assertStatus(200);
        $this->assertEquals($sesi->id, $response->json('session_id'));
        // Tidak membuat sesi baru
        $this->assertDatabaseCount('sesi_terjemah', 1);
    }

    // -------------------------------------------------------
    // Test: GET /api/riwayat
    // -------------------------------------------------------

    /** @test */
    public function riwayat_mengembalikan_array_kosong_untuk_tamu_tanpa_session_id(): void
    {
        $response = $this->getJson('/api/riwayat');

        $response->assertStatus(200)
                 ->assertExactJson([]);
    }

    /** @test */
    public function riwayat_mengembalikan_data_berdasarkan_session_id_anonim(): void
    {
        // Buat sesi dan terjemahan
        $sesi = SesiTerjemah::create(['user_id' => null, 'browser' => 'Test', 'mulai' => now()]);
        Terjemahan::create([
            'sesi_id'          => $sesi->id,
            'teks_asli'        => 'halo',
            'teks_stemming'    => 'halo',
            'confidence'       => null,
            'waktu_proses_ms'  => 10,
            'created_at'       => now(),
        ]);

        $response = $this->getJson('/api/riwayat?session_id=' . $sesi->id);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    /** @test */
    public function riwayat_user_hanya_melihat_data_milik_sendiri(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Buat sesi & terjemahan milik user1
        $sesi1 = SesiTerjemah::create(['user_id' => $user1->id, 'browser' => 'Test', 'mulai' => now()]);
        Terjemahan::create([
            'sesi_id' => $sesi1->id, 'teks_asli' => 'punya user1',
            'teks_stemming' => 'user1', 'confidence' => null, 'waktu_proses_ms' => 10, 'created_at' => now(),
        ]);

        // Buat sesi & terjemahan milik user2
        $sesi2 = SesiTerjemah::create(['user_id' => $user2->id, 'browser' => 'Test', 'mulai' => now()]);
        Terjemahan::create([
            'sesi_id' => $sesi2->id, 'teks_asli' => 'punya user2',
            'teks_stemming' => 'user2', 'confidence' => null, 'waktu_proses_ms' => 10, 'created_at' => now(),
        ]);

        // User1 hanya melihat milik sendiri
        $response = $this->actingAs($user1)->getJson('/api/riwayat');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
        $this->assertEquals('punya user1', $response->json()[0]['teks_asli']);
    }
}
