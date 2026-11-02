<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKata extends Model
{
    use HasFactory;

    protected $table = 'detail_kata';
    public $timestamps = false;

    protected $fillable = [
        'terjemahan_id',
        'isyarat_id',
        'urutan',
        'kata_asli',
        'kata_dasar',
        'status_match',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'status_match' => 'boolean',
    ];

    public function terjemahan()
    {
        return $this->belongsTo(Terjemahan::class, 'terjemahan_id');
    }

    public function isyarat()
    {
        return $this->belongsTo(KamusIsyarat::class, 'isyarat_id');
    }
}
