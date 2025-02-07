<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsTableSeeder extends Seeder
{
    public function run()
    {
        Department::create([
            'kode_department' => 'D001',
            'nama_department' => 'HR',
            'cost_center' => 'CC001'
        ]);

        Department::create([
            'kode_department' => 'D002',
            'nama_department' => 'Finance',
            'cost_center' => 'CC002'
        ]);

        Department::create([
            'kode_department' => 'D003',
            'nama_department' => 'IT',
            'cost_center' => 'CC003'
        ]);
    }
}
