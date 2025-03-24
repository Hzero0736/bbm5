<?php

namespace App\Exports;

use App\Models\KlaimBBM;
use App\Models\SaldoBBM;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KlaimBBMExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filter;
    protected $department_id;
    protected $periode;
    protected $user_id;
    protected $status;

    public function __construct($filter, $department_id = null, $periode = null, $user_id = null, $status = null)
    {
        $this->filter = $filter;
        $this->department_id = $department_id;
        $this->periode = $periode;
        $this->user_id = $user_id;
        $this->status = $status;
    }

    public function collection()
    {
        $query = KlaimBBM::with(['user', 'kendaraan', 'bbm', 'saldoBBM']);

        // Filter berdasarkan filter yang dipilih
        if ($this->filter === 'saya') {
            $query->where('user_id', Auth::id());
        } elseif ($this->filter === 'departemen' && $this->department_id) {
            $query->whereHas('user', function ($q) {
                $q->where('department_id', $this->department_id);
            });
        }

        // Filter tambahan
        if ($this->periode) {
            $query->where('periode', $this->periode);
        }

        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        if ($this->status) {
            if ($this->status === 'normal') {
                $query->whereHas('saldoBBM', function ($q) {
                    $q->where('status', 'normal');
                });
            } elseif ($this->status === 'melebihi_batas') {
                $query->whereHas('saldoBBM', function ($q) {
                    $q->where('status', 'melebihi_batas');
                });
            }
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID Klaim',
            'No. ACC',
            'Periode',
            'NIK',
            'Nama Pengguna',
            'Departemen',
            'Cost Center',
            'Kendaraan',
            'Jenis BBM',
            'Saldo Awal',
            'Total Penggunaan (Liter)',
            'Sisa Saldo',
            'Jumlah Dana (Rp)',
            'Status',
            'Catatan',
            'Tanggal Dibuat'
        ];
    }

    public function map($claim): array
    {
        $currentDate = Carbon::parse($claim->periode);
        $previousPeriode = $currentDate->copy()->subMonth()->format('Y-m');
        $saldoAwal = 200.0;

        $previousSaldo = SaldoBBM::where('user_id', $claim->user_id)
            ->where('periode', $previousPeriode)
            ->first();

        if ($previousSaldo) {
            $previousTotalPenggunaan = KlaimBBM::where('periode', $previousPeriode)
                ->where('user_id', $claim->user_id)
                ->sum('total_penggunaan_liter');
            $saldoAwal = 200.0 - $previousTotalPenggunaan;
            if ($saldoAwal < 0) $saldoAwal = 0;
        }

        $claimsByPeriod = KlaimBBM::where('periode', $claim->periode)
            ->where('user_id', $claim->user_id)
            ->where('created_at', '<=', $claim->created_at)
            ->orderBy('created_at')
            ->get();

        $availableBalance = $saldoAwal;
        foreach ($claimsByPeriod as $c) {
            if ($c->id === $claim->id) {
                break;
            }
            $availableBalance -= $c->total_penggunaan_liter;
            if ($availableBalance < 0) $availableBalance = 0;
        }

        $sisaSaldo = $availableBalance - $claim->total_penggunaan_liter;
        $status = $sisaSaldo < 0 ? 'Melebihi Batas' : 'Normal';

        return [
            $claim->klaim_id,
            $claim->no_acc,
            $claim->periode,
            $claim->user->nik,
            $claim->user->nama ?? '-',
            $claim->user->department->nama_department ?? '-',
            $claim->user->department->cost_center ?? '-',
            $claim->kendaraan->nama_kendaraan ?? '-',
            $claim->bbm->nama_bbm ?? '-',
            number_format($availableBalance, 1),
            number_format($claim->total_penggunaan_liter, 1),
            number_format($sisaSaldo, 1),
            number_format($claim->jumlah_dana, 2),
            $status,
            $claim->catatan,
            $claim->created_at->format('d/m/Y H:i')
        ];
    }
}
