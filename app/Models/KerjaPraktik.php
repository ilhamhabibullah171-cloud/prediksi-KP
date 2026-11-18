<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KerjaPraktik extends Model
{
    use HasFactory;

    protected $table = 'kerja_praktik';
    protected $primaryKey = 'id_kp';

    protected $fillable = [
        'nim',
        'nidn',
        'instansi',
        'alamat_instansi',
        'judul_kp',
        'kontak_instansi',
        'berkas_kp',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_kp',
        'usulan_dosen',
        'catatan_koordinator',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nidn', 'nidn');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'id_kp', 'id_kp')->orderBy('hari_ke');
    }

    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class, 'id_kp', 'id_kp');
    }

    public function penilaian()
    {
        return $this->hasOne(Penilaian::class, 'id_kp', 'id_kp');
    }

    // Helper statistics
    public function getLogbookCountAttribute()
    {
        return $this->logbooks()->count();
    }

    public function getLogbookApprovedCountAttribute()
    {
        return $this->logbooks()->where('status_validasi', 'Approved')->count();
    }

    public function getRasioLogbookAttribute()
    {
        return round(($this->logbook_count / 30.0) * 100, 1);
    }

    /**
     * Calculate missed days (days where date has passed but no logbook entry)
     */
    public function getHariTerlewatCountAttribute()
    {
        $today = Carbon::today();
        $startDate = Carbon::parse($this->tanggal_mulai);
        $endDate = Carbon::parse($this->tanggal_selesai);

        // Days elapsed so far in the 30-day window
        $daysElapsed = min(30, max(0, $startDate->diffInDays($today, false) + 1));
        if ($today->lt($startDate)) {
            return 0;
        }

        $filledDays = $this->logbooks()->count();
        $missed = max(0, $daysElapsed - $filledDays);
        return $missed;
    }

    /**
     * Calculate days gap between finish date and certificate upload
     */
    public function getJarakHariSertifikatAttribute()
    {
        $endDate = Carbon::parse($this->tanggal_selesai);
        $cert = $this->sertifikat;

        if ($cert && $cert->tanggal_upload) {
            $uploadDate = Carbon::parse($cert->tanggal_upload);
            return max(0, $endDate->diffInDays($uploadDate, false));
        }

        $today = Carbon::today();
        if ($today->gt($endDate)) {
            return $endDate->diffInDays($today);
        }

        return 0;
    }
}

