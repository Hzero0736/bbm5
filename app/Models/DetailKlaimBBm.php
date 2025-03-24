<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailKlaimBBM extends Model
{
    use HasFactory;

    protected $table = 'detail_klaim_bbm';

    protected $fillable = [
        'klaim_bbm_id',
        'tanggal',
        'km',
        'bbm_id',
        'liter',
        'total_harga',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'liter' => 'decimal:2',
        'km' => 'decimal:3',
        'total_harga' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // Update total penggunaan di klaim BBM
            if ($model->klaimBbm) {
                $model->klaimBbm->updateTotalPenggunaan();
            }
        });

        static::updated(function ($model) {
            // Update total penggunaan di klaim BBM
            if ($model->klaimBbm) {
                $model->klaimBbm->updateTotalPenggunaan();
            }
        });

        static::deleted(function ($model) {
            // Update total penggunaan di klaim BBM
            if ($model->klaimBbm) {
                $model->klaimBbm->updateTotalPenggunaan();
            }
        });
    }

    public function klaimBbm()
    {
        return $this->belongsTo(KlaimBBM::class, 'klaim_bbm_id');
    }

    public function bbm()
    {
        return $this->belongsTo(BBM::class);
    }
}
