<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;

    protected $table = 'logbook';
    protected $primaryKey = 'id_logbook';

    protected $fillable = [
        'id_kp',
        'hari_ke',
        'tanggal_kegiatan',
        'deskripsi_pekerjaan',
        'dokumentasi',
        'status_validasi',
        'catatan_dosen',
        'waktu_submit',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'waktu_submit' => 'datetime',
    ];

    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'id_kp', 'id_kp');
    }
}

