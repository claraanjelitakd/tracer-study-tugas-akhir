<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perusahaan',
        'province_id',
        'kabupaten_id',
        'alamat',
        'sektor',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function alumnis()
    {
        return $this->hasMany(Alumni::class);
    }
}
