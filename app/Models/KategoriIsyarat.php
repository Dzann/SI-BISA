<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriIsyarat extends Model
{
    use HasFactory;

    protected $table = 'kategori_isyarat';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function kamusIsyarat()
    {
        return $this->hasMany(KamusIsyarat::class, 'kategori_id');
    }
}
