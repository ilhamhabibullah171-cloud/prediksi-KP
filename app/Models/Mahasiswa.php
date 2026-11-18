<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'user_id',
        'nama_lengkap',
        'angkatan',
        'ipk_terakhir',
        'sks_lulus',
        'prodi',
        'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kerjaPraktik()
    {
        return $this->hasMany(KerjaPraktik::class, 'nim', 'nim');
    }

    public function activeKp()
    {
        return $this->hasOne(KerjaPraktik::class, 'nim', 'nim')->latestOfMany('id_kp');
    }
}

