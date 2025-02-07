<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKlaimBBM extends Model
{
    protected $table = 'detail_klaim_bbm';

    protected $fillable = [
        'klaim_bbm_id',
        'periode',
        'tanggal',
        'km',
        'bbm_id',
        'liter',
        'total_harga'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'periode' => 'date',
        'liter' => 'decimal:2',
        'total_harga' => 'decimal:2'
    ];

    public function klaimBbm()
    {
        return $this->belongsTo(KlaimBBM::class, 'klaim_bbm_id');
    }

    public function bbm()
    {
        return $this->belongsTo(BBM::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail) {
            $detail->total_harga = $detail->liter * $detail->bbm->harga_bbm;
        });
    }
}
