<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BBM extends Model
{
    protected $table = 'bbm';

    protected $fillable = [
        'nama_bbm',
        'harga_bbm',
        'satuan_bbm'
    ];

    protected $casts = [
        'harga_bbm' => 'decimal:2'
    ];

    public function klaimBbm()
    {
        return $this->hasMany(KlaimBBM::class);
    }

    public function detailKlaim()
    {
        return $this->hasMany(DetailKlaimBBM::class);
    }
}
