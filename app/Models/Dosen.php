<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';
    protected $primaryKey = 'nidn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nidn',
        'user_id',
        'nama_dosen',
        'kuota_bimbingan',
        'jabatan_akademik',
        'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kerjaPraktik()
    {
        return $this->hasMany(KerjaPraktik::class, 'nidn', 'nidn');
    }

    public function bimbinganAktif()
    {
        return $this->hasMany(KerjaPraktik::class, 'nidn', 'nidn')->whereIn('status_kp', ['Berjalan', 'Pengajuan']);
    }

    public function getSisaKuotaAttribute()
    {
        $used = $this->bimbinganAktif()->where('status_kp', 'Berjalan')->count();
        return max(0, $this->kuota_bimbingan - $used);
    }
}

