<?php

namespace Database\Seeders;

use App\Models\DetailKlaimBBM;
use Illuminate\Database\Seeder;

class DetailKlaimBBMTableSeeder extends Seeder
{
    public function run()
    {
        DetailKlaimBBM::create([
            'klaim_bbm_id' => 1,
            'periode' => '2024-01',
            'tanggal' => '2024-01-15',
            'km' => 1000,
            'bbm_id' => 1,
            'liter' => 25,
            'total_harga' => 250000
        ]);
    }
}
