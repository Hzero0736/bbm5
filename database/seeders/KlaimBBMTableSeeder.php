<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KlaimBBM;

class KlaimBBMTableSeeder extends Seeder
{
    public function run()
    {
        KlaimBBM::create([
            'no_acc' => 'KL-20240115-1001',
            'periode' => '2024-01',
            'user_id' => 1,
            'kendaraan_id' => 1,
            'bbm_id' => 1,
            'jumlah_dana' => 500000,
            'saldo_liter' => 200,
            'total_penggunaan_liter' => 50,
            'sisa_saldo_liter' => 150,
        ]);
    }
}
