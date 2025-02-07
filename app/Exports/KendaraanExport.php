<?php

namespace App\Exports;

use App\Models\Kendaraan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class KendaraanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filter;
    protected $department_id;

    public function __construct($filter, $department_id = null)
    {
        $this->filter = $filter;
        $this->department_id = $department_id;
    }

    public function collection()
    {
        $query = Kendaraan::with(['user.department']);

        switch ($this->filter) {
            case 'my':
                $query->where('user_id', Auth::id());
                break;
            case 'department':
                if ($this->department_id) {
                    $query->whereHas('user', function ($q) {
                        $q->where('department_id', $this->department_id);
                    });
                }
                break;
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Kendaraan',
            'No Plat',
            'Keperluan',
            'Pengguna',
            'Departemen',
            'Tanggal Dibuat'
        ];
    }

    public function map($kendaraan): array
    {
        return [
            $kendaraan->nama_kendaraan,
            $kendaraan->no_plat,
            $kendaraan->keperluan,
            $kendaraan->user->name,
            $kendaraan->user->department->nama_department,
            $kendaraan->created_at->format('d/m/Y H:i')
        ];
    }
}
