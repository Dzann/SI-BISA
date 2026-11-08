<?php

namespace Tests\Unit;

use App\Models\DetailKata;
use App\Models\KamusIsyarat;
use App\Models\KategoriIsyarat;
use App\Models\SesiTerjemah;
use App\Models\Terjemahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test unit untuk model-model Eloquent.
 * Memverifikasi cast, fillable, relasi, dan factory state.
 */
class ModelTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------
    // Test: Model KamusIsyarat
    // -------------------------------------------------------

    /** @test */
    public function kamus_isyarat_is_active_dicast_ke_boolean(): void
    {
        $kamus = KamusIsyarat::create([
            'kata_dasar'  => 'tes_cast',
            'tipe_media'  => 'gambar',
            'url_media'   => 'bisindo/tes.png',
            'durasi_ms'   => 2000,
            'is_active'   => 1, // disimpan sebagai integer
            'kategori_id' => null,
        ]);

        $this->assertIsBool($kamus->is_active);
        $this->assertTrue($kamus->is_active);
    }

    /** @test */
    public function kamus_isyarat_durasi_ms_dicast_ke_integer(): void
    {
        $kamus = KamusIsyarat::create([
            'kata_dasar'  => 'tes_durasi',
            'tipe_media'  => 'gambar',
            'url_media'   => 'bisindo/tes.png',
            'durasi_ms'   => '3000', // disimpan sebagai string
            'is_active'   => true,
            'kategori_id' => null,
        ]);

        $this->assertIsInt($kamus->durasi_ms);
        $this->assertEquals(3000, $kamus->durasi_ms);
    }

    /** @test */
    public function kamus_isyarat_memiliki_relasi_kategori(): void
    {
        $kamus = new KamusIsyarat();

        $relasi = $kamus->kategori();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relasi);
    }

    /** @test */
    public function kamus_isyarat_memiliki_relasi_detail_kata(): void
    {
        $kamus = new KamusIsyarat();

        $relasi = $kamus->detailKata();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relasi);
    }

    // -------------------------------------------------------
    // Test: Model User
    // -------------------------------------------------------

    /** @test */
    public function user_factory_default_memiliki_role_user(): void
    {
        $user = User::factory()->create();

        $this->assertEquals('user', $user->role);
    }

    /** @test */
    public function user_factory_admin_state_memiliki_role_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertEquals('admin', $admin->role);
    }

    /** @test */
    public function user_memiliki_relasi_sesi_terjemah(): void
    {
        $user = new User();

        $relasi = $user->sesiTerjemah();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relasi);
    }

    /** @test */
    public function user_password_tidak_terekspos_saat_serialisasi(): void
    {
        $user = User::factory()->create();
        $json = $user->toArray();

        $this->assertArrayNotHasKey('password', $json);
        $this->assertArrayNotHasKey('remember_token', $json);
    }

    // -------------------------------------------------------
    // Test: Model SesiTerjemah & Terjemahan
    // -------------------------------------------------------

    /** @test */
    public function sesi_terjemah_dapat_dibuat_tanpa_user(): void
    {
        $sesi = SesiTerjemah::create([
            'user_id' => null,
            'browser' => 'Test Browser',
            'mulai'   => now(),
        ]);

        $this->assertNotNull($sesi->id);
        $this->assertNull($sesi->user_id);
    }

    /** @test */
    public function terjemahan_dapat_dibuat_dan_disimpan(): void
    {
        $sesi = SesiTerjemah::create([
            'user_id' => null,
            'browser' => 'Test',
            'mulai'   => now(),
        ]);

        $terjemahan = Terjemahan::create([
            'sesi_id'         => $sesi->id,
            'teks_asli'       => 'halo dunia',
            'teks_stemming'   => 'halo dunia',
            'confidence'      => 0.9,
            'waktu_proses_ms' => 50,
            'created_at'      => now(),
        ]);

        $this->assertNotNull($terjemahan->id);
        $this->assertDatabaseHas('terjemahan', ['teks_asli' => 'halo dunia']);
    }

    // -------------------------------------------------------
    // Test: Fillable Protection
    // -------------------------------------------------------

    /** @test */
    public function kamus_isyarat_hanya_mengizinkan_fillable_fields(): void
    {
        $kamus = new KamusIsyarat();
        $fillable = $kamus->getFillable();

        // Field yang seharusnya bisa diisi massal
        foreach (['kata_dasar', 'tipe_media', 'url_media', 'durasi_ms', 'is_active', 'kategori_id'] as $field) {
            $this->assertContains($field, $fillable, "Field '$field' seharusnya ada dalam fillable");
        }
    }

    /** @test */
    public function user_hanya_mengizinkan_fillable_fields(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        foreach (['name', 'email', 'password', 'role'] as $field) {
            $this->assertContains($field, $fillable, "Field '$field' seharusnya ada dalam fillable User");
        }
    }
}
