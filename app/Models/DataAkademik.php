<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAkademik extends Model
{
    use HasFactory;

    protected $fillable = [
        'nim',
        'nama',
        'angkatan_masuk',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'jenis_kelamin',
        'golongan_darah',
        'warga_negara',
        'nomor_telepon',
        'email_pribadi',
        'email_students',
        'alamat_saat_ini',
        'kelurahan',
        'kecamatan',
        'kabupaten_id',
        'provinsi_id',
        'kode_pos',
        'nik',
        'no_kk',
        'nisn',
        'no_bpjs',
        'status_mahasiswa',
        'tahun_akademik_lulus',
        'tahun_lulus',
        'ipk',
        'total_sks',
        'total_angka_kualitas',
    ];

    /**
     * Relasi ke model Alumni
     */
    public function alumni()
    {
        return $this->hasOne(Alumni::class, 'nim', 'nim');
    }

    public function yudisium()
    {
        return $this->hasOne(Yudisium::class, 'nim', 'nim');
    }
    
    /**
     * Relasi ke model DataOrangTua
     */
    public function orangTua()
    {
        return $this->hasOne(DataOrangTua::class, 'nim', 'nim');
    }
}
