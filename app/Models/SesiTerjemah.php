<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiTerjemah extends Model
{
    use HasFactory;

    protected $table = 'sesi_terjemah';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'browser',
        'mulai',
        'selesai',
    ];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function terjemahan()
    {
        return $this->hasMany(Terjemahan::class, 'sesi_id');
    }
}
