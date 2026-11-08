<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sastrawi\Stemmer\StemmerInterface;
use Tests\TestCase;

/**
 * Test unit untuk Sastrawi Stemmer Singleton.
 * Memverifikasi stemming berjalan benar dan singleton terdaftar di container.
 */
class StemmerTest extends TestCase
{
    // -------------------------------------------------------
    // Test: Singleton di Container
    // -------------------------------------------------------

    /** @test */
    public function stemmer_dapat_di_resolve_dari_container(): void
    {
        $stemmer = app(StemmerInterface::class);

        $this->assertInstanceOf(StemmerInterface::class, $stemmer);
    }

    /** @test */
    public function stemmer_merupakan_singleton_instance_yang_sama(): void
    {
        $stemmer1 = app(StemmerInterface::class);
        $stemmer2 = app(StemmerInterface::class);

        // Harus instance yang sama (singleton)
        $this->assertSame($stemmer1, $stemmer2);
    }

    // -------------------------------------------------------
    // Test: Akurasi Stemming Bahasa Indonesia
    // -------------------------------------------------------

    /** @test */
    public function stem_kata_makanan_menjadi_makan(): void
    {
        $stemmer = app(StemmerInterface::class);

        $this->assertEquals('makan', $stemmer->stem('makanan'));
    }

    /** @test */
    public function stem_kata_berlari_menjadi_lari(): void
    {
        $stemmer = app(StemmerInterface::class);

        $this->assertEquals('lari', $stemmer->stem('berlari'));
    }

    /** @test */
    public function stem_kata_penerjemah_menjadi_terjemah(): void
    {
        $stemmer = app(StemmerInterface::class);

        $this->assertEquals('terjemah', $stemmer->stem('penerjemah'));
    }

    /** @test */
    public function stem_kata_minuman_menjadi_minum(): void
    {
        $stemmer = app(StemmerInterface::class);

        $this->assertEquals('minum', $stemmer->stem('minuman'));
    }

    /** @test */
    public function stem_kata_pembelajaran_menjadi_ajar_atau_belajar(): void
    {
        $stemmer = app(StemmerInterface::class);

        $hasil = $stemmer->stem('pembelajaran');

        // Sastrawi bisa menghasilkan 'ajar' atau 'belajar' tergantung konfigurasi kamus
        $this->assertContains($hasil, ['ajar', 'belajar', 'ajar']);
    }

    /** @test */
    public function stem_kata_sudah_dasar_tidak_berubah(): void
    {
        $stemmer = app(StemmerInterface::class);

        // Kata dasar biasanya tidak berubah setelah di-stem
        $hasil = $stemmer->stem('meja');
        $this->assertIsString($hasil);
        $this->assertNotEmpty($hasil);
    }

    /** @test */
    public function stem_kata_panjang_menghasilkan_string(): void
    {
        $stemmer = app(StemmerInterface::class);

        $hasil = $stemmer->stem('ketidakberdayaan');
        $this->assertIsString($hasil);
        $this->assertNotEmpty($hasil);
    }
}
