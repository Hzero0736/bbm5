<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Role::create(['nama' => 'Admin']);
        Role::create(['nama' => 'Section Head']);
    }
}
