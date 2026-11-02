<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terjemahan extends Model
{
    use HasFactory;

    protected $table = 'terjemahan';
    public $timestamps = false; // Memiliki created_at saja

    protected $fillable = [
        'sesi_id',
        'teks_asli',
        'teks_stemming',
        'confidence',
        'waktu_proses_ms',
        'created_at',
    ];

    protected $casts = [
        'confidence' => 'float',
        'waktu_proses_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    public function sesi()
    {
        return $this->belongsTo(SesiTerjemah::class, 'sesi_id');
    }

    public function detailKata()
    {
        return $this->hasMany(DetailKata::class, 'terjemahan_id')->orderBy('urutan');
    }

    public function hasilPengujian()
    {
        return $this->hasOne(HasilPengujian::class, 'terjemahan_id');
    }
}
