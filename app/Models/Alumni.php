<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'prodi_id',
        'angkatan',
        'F1',
        'F2A',
        'F2B',
        'F2C',
        'F2D',
        'ipk',
        'tanggal_lahir',
        'sac_points',
    ];

    /**
     * Get the user that owns the alumni record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke master Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Helper: Ekstrak Angkatan dan Kode Prodi dari NIM
     */
    public static function parseNim($nim)
    {
        if (strlen($nim) >= 4) {
            $kodeProdi = substr($nim, 0, 2);
            $tahunKode = substr($nim, 2, 2);
            
            // Konversi ke tahun (misal '24' -> 2024, '98' -> 1998)
            $angkatan = (int) $tahunKode > 50 ? 1900 + (int) $tahunKode : 2000 + (int) $tahunKode;
            
            return [
                'kode_prodi' => $kodeProdi,
                'angkatan' => $angkatan,
            ];
        }
        return null;
    }
}
