<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BBM;

class BBMTableSeeder extends Seeder
{
    public function run()
    {
        BBM::create([
            'nama_bbm' => 'Pertalite',
            'harga_bbm' => 10000.00,
            'satuan_bbm' => 'Liter'
        ]);

        BBM::create([
            'nama_bbm' => 'Pertamax',
            'harga_bbm' => 9000.00,
            'satuan_bbm' => 'Liter'
        ]);
    }
}
