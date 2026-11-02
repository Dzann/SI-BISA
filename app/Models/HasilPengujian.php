<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPengujian extends Model
{
    use HasFactory;

    protected $table = 'hasil_pengujian';
    public $timestamps = false;

    protected $fillable = [
        'terjemahan_id',
        'teks_referensi',
        'akurasi_suara',
        'akurasi_stemming',
        'waktu_respon_ms',
    ];

    protected $casts = [
        'akurasi_suara' => 'float',
        'akurasi_stemming' => 'float',
        'waktu_respon_ms' => 'integer',
    ];

    public function terjemahan()
    {
        return $this->belongsTo(Terjemahan::class, 'terjemahan_id');
    }
}
