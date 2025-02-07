<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlaimBBM extends Model
{
    protected $table = 'klaim_bbm';

    protected $fillable = [
        'no_acc',
        'periode',
        'user_id',
        'kendaraan_id',
        'bbm_id',
        'jumlah_dana',
        'saldo_liter',
        'total_penggunaan_liter',
        'sisa_saldo_liter'
    ];

    protected $casts = [
        'periode' => 'date',
        'jumlah_dana' => 'decimal:2',
        'saldo_liter' => 'decimal:2',
        'total_penggunaan_liter' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function bbm()
    {
        return $this->belongsTo(BBM::class);
    }

    public function details()
    {
        return $this->hasMany(DetailKlaimBBM::class, 'klaim_bbm_id');
    }

    public function getTotalLiterAttribute()
    {
        return $this->details->sum('liter');
    }

    public function getTotalHargaAttribute()
    {
        return $this->details->sum('total_harga');
    }
}
