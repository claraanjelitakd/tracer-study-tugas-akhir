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
}
