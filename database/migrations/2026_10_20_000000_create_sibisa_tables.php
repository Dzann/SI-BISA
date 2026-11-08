<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabel users bawaan Laravel: tambahkan kolom role
        Schema::table('users', fn (Blueprint $t) => $t->string('role')->default('user'));

        Schema::create('kategori_isyarat', function (Blueprint $t) {
            $t->id();
            $t->string('nama_kategori');
            $t->string('deskripsi')->nullable();
        });

        Schema::create('kamus_isyarat', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kategori_id')->nullable()->constrained('kategori_isyarat')->nullOnDelete();
            $t->string('kata_dasar')->unique();
            // Gunakan string agar kompatibel dengan SQLite (saat testing) maupun MySQL (produksi)
            // Validasi tipe dilakukan di layer Controller
            $t->string('tipe_media', 10);
            $t->string('url_media');
            $t->unsignedInteger('durasi_ms')->default(2000);
            $t->boolean('is_active')->default(true);
        });

        Schema::create('sesi_terjemah', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('browser')->nullable();
            $t->timestamp('mulai')->useCurrent();
            $t->timestamp('selesai')->nullable();
        });

        Schema::create('terjemahan', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sesi_id')->constrained('sesi_terjemah')->cascadeOnDelete();
            $t->text('teks_asli');
            $t->text('teks_stemming');
            $t->float('confidence')->nullable();
            $t->unsignedInteger('waktu_proses_ms')->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('detail_kata', function (Blueprint $t) {
            $t->id();
            $t->foreignId('terjemahan_id')->constrained('terjemahan')->cascadeOnDelete();
            $t->foreignId('isyarat_id')->nullable()->constrained('kamus_isyarat')->nullOnDelete();
            $t->unsignedInteger('urutan');
            $t->string('kata_asli');
            $t->string('kata_dasar');
            $t->boolean('status_match')->default(false);
            $t->index(['terjemahan_id', 'urutan']);
        });

        Schema::create('hasil_pengujian', function (Blueprint $t) {
            $t->id();
            $t->foreignId('terjemahan_id')->unique()->constrained('terjemahan')->cascadeOnDelete();
            $t->text('teks_referensi');
            $t->float('akurasi_suara')->nullable();
            $t->float('akurasi_stemming')->nullable();
            $t->unsignedInteger('waktu_respon_ms')->nullable();
        });
    }

    public function down(): void {
        foreach (['hasil_pengujian','detail_kata','terjemahan','sesi_terjemah','kamus_isyarat','kategori_isyarat'] as $tb) {
            Schema::dropIfExists($tb);
        }
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
    }
};
