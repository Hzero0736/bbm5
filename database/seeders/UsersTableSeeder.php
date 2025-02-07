<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $adminUser = User::create([
            'nama' => 'Admin User',
            'nik' => '123456',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'department_id' => 1,
            'posisi' => 'Admin',
            'status' => 'disetujui'
        ]);

        $sectionHeadUser = User::create([
            'nama' => 'Section Head User',
            'nik' => '654321',
            'email' => 'sectionhead@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'department_id' => 2,
            'posisi' => 'Section Head',
            'status' => 'disetujui'
        ]);

        $staffUser = User::create([
            'nama' => 'Staff User',
            'nik' => '789012',
            'email' => 'staff@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'department_id' => 3,
            'posisi' => 'Section Head',
            'status' => 'disetujui'
        ]);

        $adminUser->roles()->attach(1);
        $sectionHeadUser->roles()->attach(2);
        $staffUser->roles()->attach(2);
    }
}
