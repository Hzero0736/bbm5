<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';

    protected $fillable = [
        'kode_department',
        'nama_department',
        'cost_center',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
