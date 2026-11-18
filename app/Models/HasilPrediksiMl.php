<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPrediksiMl extends Model
{
    use HasFactory;

    protected $table = 'hasil_prediksi_ml';
    protected $primaryKey = 'id_prediksi';

    protected $fillable = [
        'id_kp',
        'rasio_kepatuhan_logbook',
        'jarak_hari_sertifikat',
        'jumlah_hari_terlewat',
        'label_prediksi',
        'status_code',
        'probabilitas_sukses',
        'detail_probabilitas',
        'rekomendasi_ews',
        'tanggal_update_prediksi',
    ];

    protected $casts = [
        'tanggal_update_prediksi' => 'datetime',
        'detail_probabilitas' => 'array',
    ];

    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'id_kp', 'id_kp');
    }
}

