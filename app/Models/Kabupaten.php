<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'kode_kabupaten',
        'nama_kabupaten',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
