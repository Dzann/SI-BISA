<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KamusIsyarat extends Model
{
    use HasFactory;

    protected $table = 'kamus_isyarat';
    public $timestamps = false;

    protected $fillable = [
        'kategori_id',
        'kata_dasar',
        'tipe_media',
        'url_media',
        'durasi_ms',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'durasi_ms' => 'integer',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriIsyarat::class, 'kategori_id');
    }

    public function detailKata()
    {
        return $this->hasMany(DetailKata::class, 'isyarat_id');
    }
}
