<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';
    protected $primaryKey = 'id_nilai';

    protected $fillable = [
        'id_kp',
        'nilai_pembimbing_instansi',
        'nilai_dosen_pembimbing',
        'nilai_disiplin',
        'nilai_laporan',
        'nilai_presentasi',
        'nilai_akhir',
        'grade',
        'catatan_evaluasi',
    ];

    public function kerjaPraktik()
    {
        return $this->belongsTo(KerjaPraktik::class, 'id_kp', 'id_kp');
    }

    public static function determineGrade($score): string
    {
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'A-';
        if ($score >= 75) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 65) return 'B-';
        if ($score >= 60) return 'C+';
        if ($score >= 55) return 'C';
        if ($score >= 40) return 'D';
        return 'E';
    }
}

