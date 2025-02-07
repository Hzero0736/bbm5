<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            DepartmentsTableSeeder::class,
            UsersTableSeeder::class,
            KendaraanTableSeeder::class,
            BBMTableSeeder::class,
            KlaimBBMTableSeeder::class,
            DetailKlaimBBMTableSeeder::class,
        ]);
    }
}
