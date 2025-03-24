<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class SaldoBBM extends Model
{
    use HasFactory;

    protected $table = 'saldo_bbm';

    protected $fillable = [
        'user_id',
        'periode',
        'saldo_awal',
        'total_penggunaan',
        'sisa_saldo',
        'status'
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'total_penggunaan' => 'decimal:2',
        'sisa_saldo' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function klaimBBM()
    {
        return $this->hasMany(KlaimBBM::class, 'saldo_bbm_id');
    }

    /**
     * Mendapatkan sisa saldo untuk periode tertentu
     * 
     * @param string $periode Format: YYYY-MM
     * @param int $userId
     * @return float
     */
    public static function getSisaSaldo($periode, $userId)
    {
        try {
            $saldo = self::where('user_id', $userId)
                ->where('periode', $periode)
                ->first();

            return $saldo ? $saldo->sisa_saldo : 200.00;
        } catch (\Exception $e) {
            Log::error('Error in getSisaSaldo: ' . $e->getMessage());
            return 200.00;
        }
    }

    /**
     * Memperbarui saldo berdasarkan penggunaan
     * 
     * @param string $periode Format: YYYY-MM
     * @param int $userId
     * @param float $totalPenggunaan
     * @return SaldoBBM
     */
    public static function updateSaldo($periode, $userId, $totalPenggunaan)
    {
        $saldo = self::firstOrCreate(
            [
                'user_id' => $userId,
                'periode' => $periode
            ],
            [
                'saldo_awal' => 200.00,
                'total_penggunaan' => 0,
                'sisa_saldo' => 200.00,
                'status' => 'normal'
            ]
        );

        $saldo->total_penggunaan = $totalPenggunaan;
        $saldo->sisa_saldo = $saldo->saldo_awal - $saldo->total_penggunaan;
        $saldo->status = ($saldo->total_penggunaan > 200) ? 'melebihi_batas' : 'normal';
        $saldo->save();

        return $saldo;
    }
}
