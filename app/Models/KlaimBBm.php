<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class KlaimBBM extends Model
{
    use HasFactory;

    protected $table = 'klaim_bbm';

    protected $fillable = [
        'klaim_id',
        'no_acc',
        'periode',
        'user_id',
        'kendaraan_id',
        'bbm_id',
        'saldo_bbm_id',
        'jumlah_dana',
        'total_penggunaan_liter',
        'catatan'
    ];

    protected $casts = [
        'jumlah_dana' => 'decimal:2',
        'total_penggunaan_liter' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate klaim_id jika belum ada
            if (!$model->klaim_id) {
                $prefix = 'KLM-' . date('Ymd');
                $latestKlaim = self::where('klaim_id', 'like', $prefix . '%')->latest()->first();

                if ($latestKlaim) {
                    $number = intval(substr($latestKlaim->klaim_id, -4)) + 1;
                } else {
                    $number = 1;
                }

                $model->klaim_id = $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }

            // Set no_acc default jika belum ada
            if (!$model->no_acc) {
                $model->no_acc = '70106100';
            }
        });
    }

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

    public function saldoBBM()
    {
        return $this->belongsTo(SaldoBBM::class, 'saldo_bbm_id');
    }

    /**
     * Menghitung total penggunaan BBM berdasarkan detail
     * 
     * @return float
     */
    public function hitungTotalPenggunaan()
    {
        return $this->details()->sum('liter');
    }

    /**
     * Memperbarui total penggunaan dan jumlah dana
     * 
     * @return $this
     */
    public function updateTotalPenggunaan()
    {
        $this->total_penggunaan_liter = $this->hitungTotalPenggunaan();

        // Update jumlah dana berdasarkan harga BBM
        if ($this->bbm) {
            $this->jumlah_dana = $this->total_penggunaan_liter * $this->bbm->harga_bbm;
        }

        $this->save();

        return $this;
    }

    /**
     * Mendapatkan total penggunaan BBM untuk periode tertentu
     * 
     * @param string $periode Format: YYYY-MM
     * @param int|null $userId
     * @return float
     */
    public static function getTotalPenggunaanPeriode($periode, $userId = null)
    {
        try {
            $query = self::where('periode', $periode);

            if ($userId) {
                $query->where('user_id', $userId);
            }

            return $query->sum('total_penggunaan_liter');
        } catch (\Exception $e) {
            Log::error('Error in getTotalPenggunaanPeriode: ' . $e->getMessage());
            return 0;
        }
    }
}
