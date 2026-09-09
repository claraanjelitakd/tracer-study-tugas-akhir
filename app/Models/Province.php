<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_provinsi',
        'nama_provinsi',
    ];

    public function kabupatens()
    {
        return $this->hasMany(Kabupaten::class);
    }

    public function umps()
    {
        return $this->hasMany(Ump::class, 'kode_provinsi', 'kode_provinsi');
    }

    public function ump()
    {
        return $this->hasOne(Ump::class, 'kode_provinsi', 'kode_provinsi')->latestOfMany('tahun');
    }
}
