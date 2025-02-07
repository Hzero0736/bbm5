<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kendaraan;

class KendaraanTableSeeder extends Seeder
{
    public function run()
    {
        $kendaraans = [
            [
                'nama_kendaraan' => 'Toyota Avanza',
                'no_plat' => 'B 1234 XYZ',
                'keperluan' => 'Kendaraan Operasional',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kendaraan' => 'Honda Civic',
                'no_plat' => 'B 5678 XYZ',
                'keperluan' => 'Kendaraan Operasional',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kendaraan' => 'Suzuki Ertiga',
                'no_plat' => 'B 9012 XYZ',
                'keperluan' => 'Kendaraan Operasional',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        Kendaraan::insert($kendaraans);
    }
}
