<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikat';
    protected $primaryKey = 'id_sertifikat';

    protected $fillable = [
        'id_kp',
        'file_path',
        'tanggal_upload',
        'status_verifikasi',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_upload' => 'date',
    ];

    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'id_kp', 'id_kp');
    }
}

