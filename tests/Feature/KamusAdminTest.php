<?php

namespace Tests\Feature;

use App\Models\KamusIsyarat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Test CRUD endpoint /api/kamus (khusus admin).
 * Memverifikasi akses kontrol, validasi, upload file, dan operasi database.
 */
class KamusAdminTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Helper: buat user biasa dan admin
    // -------------------------------------------------------

    private function buatUser(): User
    {
        return User::factory()->create();
    }

    private function buatAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    private function buatKamus(array $override = []): KamusIsyarat
    {
        return KamusIsyarat::create(array_merge([
            'kata_dasar'  => 'halo',
            'tipe_media'  => 'gambar',
            'url_media'   => 'bisindo/halo.png',
            'durasi_ms'   => 2000,
            'is_active'   => true,
            'kategori_id' => null,
        ], $override));
    }

    // -------------------------------------------------------
    // Test: GET /api/kamus — Akses Kontrol
    // -------------------------------------------------------

    /** @test */
    public function kamus_index_ditolak_untuk_tamu(): void
    {
        $response = $this->getJson('/api/kamus');

        // Middleware auth mengarahkan ke login (302) atau 401/403
        $this->assertContains($response->status(), [302, 401, 403]);
    }

    /** @test */
    public function kamus_index_ditolak_untuk_user_biasa(): void
    {
        $user = $this->buatUser();

        $response = $this->actingAs($user)->getJson('/api/kamus');

        $response->assertStatus(403);
    }

    /** @test */
    public function kamus_index_berhasil_untuk_admin(): void
    {
        $admin = $this->buatAdmin();
        $this->buatKamus(['kata_dasar' => 'makan']);
        $this->buatKamus(['kata_dasar' => 'minum']);

        $response = $this->actingAs($admin)->getJson('/api/kamus');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    // -------------------------------------------------------
    // Test: POST /api/kamus — Tambah Isyarat
    // -------------------------------------------------------

    /** @test */
    public function kamus_store_berhasil_dengan_data_valid(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();

        $file = UploadedFile::fake()->image('makan.png');

        $response = $this->actingAs($admin)->postJson('/api/kamus', [
            'kata_dasar' => 'makan',
            'media'      => $file,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'id'])
                 ->assertJson(['message' => 'Berhasil disimpan']);

        $this->assertDatabaseHas('kamus_isyarat', ['kata_dasar' => 'makan']);
    }

    /** @test */
    public function kamus_store_menyimpan_file_ke_storage(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();

        $file = UploadedFile::fake()->image('tes.png', 100, 100);

        $this->actingAs($admin)->postJson('/api/kamus', [
            'kata_dasar' => 'tes',
            'media'      => $file,
        ]);

        $kamus = KamusIsyarat::where('kata_dasar', 'tes')->first();
        $this->assertNotNull($kamus);
        Storage::disk('public')->assertExists($kamus->url_media);
    }

    /** @test */
    public function kamus_store_gagal_tanpa_kata_dasar(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)->postJson('/api/kamus', [
            'media' => UploadedFile::fake()->image('test.png'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['kata_dasar']);
    }

    /** @test */
    public function kamus_store_gagal_tanpa_file_media(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)->postJson('/api/kamus', [
            'kata_dasar' => 'makan',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['media']);
    }

    /** @test */
    public function kamus_store_gagal_kata_dasar_duplikat(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();
        $this->buatKamus(['kata_dasar' => 'makan']);

        $response = $this->actingAs($admin)->postJson('/api/kamus', [
            'kata_dasar' => 'makan',
            'media'      => UploadedFile::fake()->image('makan2.png'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['kata_dasar']);
    }

    /** @test */
    public function kamus_store_gagal_tipe_file_tidak_valid(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)->postJson('/api/kamus', [
            'kata_dasar' => 'dokumen',
            'media'      => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['media']);
    }

    // -------------------------------------------------------
    // Test: PUT /api/kamus/{id} — Ubah Isyarat
    // -------------------------------------------------------

    /** @test */
    public function kamus_update_berhasil_ubah_kata_dasar(): void
    {
        $admin = $this->buatAdmin();
        $kamus = $this->buatKamus(['kata_dasar' => 'lama']);

        $response = $this->actingAs($admin)->putJson('/api/kamus/' . $kamus->id, [
            'kata_dasar' => 'baru',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Berhasil diperbarui']);

        $this->assertDatabaseHas('kamus_isyarat', ['id' => $kamus->id, 'kata_dasar' => 'baru']);
    }

    /** @test */
    public function kamus_update_berhasil_unique_ignore_diri_sendiri(): void
    {
        $admin = $this->buatAdmin();
        $kamus = $this->buatKamus(['kata_dasar' => 'sama']);

        // Update dengan kata_dasar yang sama (milik record sendiri) harus berhasil
        $response = $this->actingAs($admin)->putJson('/api/kamus/' . $kamus->id, [
            'kata_dasar' => 'sama',
            'durasi_ms'  => 3000,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function kamus_update_gagal_kata_dasar_sudah_dipakai_record_lain(): void
    {
        $admin = $this->buatAdmin();
        $this->buatKamus(['kata_dasar' => 'sudah_ada']);
        $kamus2 = $this->buatKamus(['kata_dasar' => 'record_kedua']);

        $response = $this->actingAs($admin)->putJson('/api/kamus/' . $kamus2->id, [
            'kata_dasar' => 'sudah_ada',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['kata_dasar']);
    }

    /** @test */
    public function kamus_update_404_untuk_id_tidak_ada(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)->putJson('/api/kamus/99999', [
            'kata_dasar' => 'apapun',
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function kamus_update_berhasil_mengganti_file_media(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();
        $kamus = $this->buatKamus(['kata_dasar' => 'ganti', 'url_media' => 'bisindo/lama.png']);

        $fileBaru = UploadedFile::fake()->image('baru.png');

        $response = $this->actingAs($admin)->putJson('/api/kamus/' . $kamus->id, [
            'media' => $fileBaru,
        ]);

        $response->assertStatus(200);
        $kamus->refresh();
        // URL media harus berubah
        $this->assertNotEquals('bisindo/lama.png', $kamus->url_media);
    }

    // -------------------------------------------------------
    // Test: DELETE /api/kamus/{id} — Hapus Isyarat
    // -------------------------------------------------------

    /** @test */
    public function kamus_destroy_berhasil_hapus_record(): void
    {
        $admin = $this->buatAdmin();
        $kamus = $this->buatKamus(['kata_dasar' => 'hapus_ini']);

        $response = $this->actingAs($admin)->deleteJson('/api/kamus/' . $kamus->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Berhasil dihapus']);

        $this->assertDatabaseMissing('kamus_isyarat', ['id' => $kamus->id]);
    }

    /** @test */
    public function kamus_destroy_juga_menghapus_file_dari_storage(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();

        // Upload file nyata ke fake storage
        Storage::disk('public')->put('bisindo/hapus.png', 'isi_file_palsu');

        $kamus = $this->buatKamus([
            'kata_dasar' => 'hapus_file',
            'url_media'  => 'bisindo/hapus.png',
        ]);

        $this->actingAs($admin)->deleteJson('/api/kamus/' . $kamus->id);

        // File harus sudah terhapus dari storage
        Storage::disk('public')->assertMissing('bisindo/hapus.png');
    }

    /** @test */
    public function kamus_destroy_404_untuk_id_tidak_ada(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)->deleteJson('/api/kamus/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function kamus_destroy_ditolak_untuk_user_biasa(): void
    {
        $user = $this->buatUser();
        $kamus = $this->buatKamus();

        $response = $this->actingAs($user)->deleteJson('/api/kamus/' . $kamus->id);

        $response->assertStatus(403);
        // Record masih ada di DB
        $this->assertDatabaseHas('kamus_isyarat', ['id' => $kamus->id]);
    }
}
