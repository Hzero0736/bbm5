<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';

    protected $fillable = [
        'nama_kendaraan',
        'no_plat',
        'keperluan',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function klaimBbm()
    {
        return $this->hasMany(KlaimBBM::class);
    }
}
