<?php

namespace App\Http\Controllers;

use App\Models\KlaimBBM;
use App\Models\DetailKlaimBBM;
use App\Models\Kendaraan;
use App\Models\BBM;
use App\Models\SaldoBBM;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\KlaimBBMExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class KlaimBBMController extends Controller
{
    public function index()
    {
        $claims = Auth::user()->roles->contains('nama', 'Admin')
            ? KlaimBBM::with(['user', 'kendaraan', 'bbm', 'saldoBBM'])->latest()->get()
            : KlaimBBM::with(['user', 'kendaraan', 'bbm', 'saldoBBM'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $departments = Department::all();

        return view('claims.index', compact('claims', 'departments'));
    }

    public function create()
    {
        $kendaraans = Auth::user()->roles->contains('nama', 'Admin')
            ? Kendaraan::all()
            : Kendaraan::where('user_id', Auth::id())->get();
        $bbms = BBM::all();

        return view('claims.create', compact('kendaraans', 'bbms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode' => 'required|date_format:Y-m',
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'bbm_id' => 'required|exists:bbm,id',
            'details' => 'required|array',
            'details.*.tanggal' => 'required|date',
            'details.*.km' => 'required|numeric',
            'details.*.liter' => 'required|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        try {
            // Normalisasi format periode
            $periode = $validated['periode'];
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $periode)) {
                $periode = substr($periode, 0, 7);
            }

            // Cek apakah ada periode sebelumnya dan hitung saldo awal
            $currentDate = Carbon::parse($periode);
            $previousPeriode = $currentDate->copy()->subMonth()->format('Y-m');

            // Default saldo awal
            $saldoAwal = 200.00;

            // Cari saldo dari periode sebelumnya
            $previousSaldo = SaldoBBM::where('user_id', Auth::id())
                ->where('periode', $previousPeriode)
                ->first();

            // Jika ada periode sebelumnya, ambil sisa saldonya sebagai saldo awal
            if ($previousSaldo) {
                $previousTotalPenggunaan = KlaimBBM::where('periode', $previousPeriode)
                    ->where('user_id', Auth::id())
                    ->sum('total_penggunaan_liter');
                $saldoAwal = 200.00 - $previousTotalPenggunaan;
                if ($saldoAwal < 0) $saldoAwal = 0;
            }

            // Dapatkan saldo BBM untuk periode ini
            $saldoBBM = SaldoBBM::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'periode' => $periode
                ],
                [
                    'saldo_awal' => $saldoAwal,
                    'total_penggunaan' => 0,
                    'sisa_saldo' => $saldoAwal,
                    'status' => 'normal'
                ]
            );

            // Hitung total penggunaan untuk periode ini
            $totalLiterPeriode = KlaimBBM::getTotalPenggunaanPeriode($periode, Auth::id());
            $totalLiter = collect($request->details)->sum('liter');
            $totalPenggunaanBaru = $totalLiterPeriode + $totalLiter;
            $sisaSaldo = $saldoAwal - $totalLiterPeriode;
            $catatan = $request->catatan ?? '';

            // Cek apakah total penggunaan melebihi batas saldo awal
            $melebihi_batas = false;
            if ($totalPenggunaanBaru > $saldoAwal) {
                $melebihi_batas = true;

                // Validasi catatan jika melebihi batas
                if (empty($catatan)) {
                    return back()->withInput()->withErrors([
                        'catatan' => 'Catatan wajib diisi ketika penggunaan BBM melebihi batas saldo'
                    ]);
                }
            }

            $bbm = BBM::findOrFail($request->bbm_id);

            // Cek apakah penggunaan melebihi sisa saldo
            if ($totalLiter > $sisaSaldo && !$melebihi_batas) {
                return back()->withErrors([
                    'message' => "Total penggunaan BBM ({$totalLiter} liter) melebihi sisa saldo ({$sisaSaldo} liter)"
                ]);
            }

            $totalDana = $totalLiter * $bbm->harga_bbm;

            DB::transaction(function () use ($validated, $bbm, $totalLiter, $totalDana, $request, $saldoBBM, $catatan, $periode, $saldoAwal) {
                // Buat klaim BBM baru
                $claim = KlaimBBM::create([
                    'no_acc' => '70106100',
                    'periode' => $periode,
                    'user_id' => Auth::id(),
                    'kendaraan_id' => $validated['kendaraan_id'],
                    'bbm_id' => $bbm->id,
                    'saldo_bbm_id' => $saldoBBM->id,
                    'jumlah_dana' => $totalDana,
                    'total_penggunaan_liter' => $totalLiter,
                    'catatan' => $catatan
                ]);

                // Buat detail klaim
                $sisaSaldoHitung = $saldoBBM->sisa_saldo;
                foreach ($request->details as $detail) {
                    $sisaSaldoHitung -= $detail['liter'];
                    DetailKlaimBBM::create([
                        'klaim_bbm_id' => $claim->id,
                        'tanggal' => $detail['tanggal'],
                        'km' => $detail['km'],
                        'bbm_id' => $bbm->id,
                        'liter' => $detail['liter'],
                        'total_harga' => $detail['liter'] * $bbm->harga_bbm,
                    ]);
                }

                // Update saldo BBM
                $saldoBBM->total_penggunaan = $saldoBBM->total_penggunaan + $totalLiter;
                $saldoBBM->sisa_saldo = $saldoAwal - $saldoBBM->total_penggunaan;
                $saldoBBM->status = ($saldoBBM->total_penggunaan > $saldoAwal) ? 'melebihi_batas' : 'normal';
                $saldoBBM->save();
            });

            return redirect()->route('claims.index')->with('success', 'Klaim BBM berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error in store: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function getSisaSaldo($periode)
    {
        try {
            // Normalisasi format periode
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $periode)) {
                $periode = substr($periode, 0, 7);
            }

            // Cek apakah ada periode sebelumnya dan hitung saldo awal
            $currentDate = Carbon::parse($periode);
            $previousPeriode = $currentDate->copy()->subMonth()->format('Y-m');

            // Default saldo awal
            $saldoAwal = 200.00;

            // Cari saldo dari periode sebelumnya
            $previousSaldo = SaldoBBM::where('user_id', Auth::id())
                ->where('periode', $previousPeriode)
                ->first();

            // Jika ada periode sebelumnya, ambil sisa saldonya sebagai saldo awal
            if ($previousSaldo) {
                $previousTotalPenggunaan = KlaimBBM::where('periode', $previousPeriode)
                    ->where('user_id', Auth::id())
                    ->sum('total_penggunaan_liter');
                $saldoAwal = 200.00 - $previousTotalPenggunaan;
                if ($saldoAwal < 0) $saldoAwal = 0;
            }

            // Hitung total penggunaan untuk periode ini
            $totalPenggunaan = KlaimBBM::where('periode', $periode)
                ->where('user_id', Auth::id())
                ->sum('total_penggunaan_liter');

            $sisaSaldo = $saldoAwal - $totalPenggunaan;
            if ($sisaSaldo < 0) $sisaSaldo = 0;

            return response()->json([
                'saldo_awal' => $saldoAwal,
                'sisa_saldo' => $sisaSaldo
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getSisaSaldo: ' . $e->getMessage());
            return response()->json([
                'saldo_awal' => 200.00,
                'sisa_saldo' => 200.00,
                'error' => 'Terjadi kesalahan saat mengambil data saldo'
            ]);
        }
    }

    public function show(KlaimBBM $claim)
    {
        if ($claim->user_id !== Auth::id() && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403, 'Data Tidak Ada.');
        }

        $claim->load(['user', 'kendaraan', 'bbm', 'details', 'saldoBBM']);
        $departments = Department::all();
        return view('claims.show', compact('claim', 'departments'));
    }

    public function edit(KlaimBBM $claim)
    {
        if ($claim->user_id !== Auth::id() && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403, 'Data Tidak Ada.');
        }
        $claim->load('details');
        $kendaraans = Auth::user()->roles->contains('nama', 'Admin')
            ? Kendaraan::all()
            : Kendaraan::where('user_id', Auth::id())->get();
        $bbms = BBM::all();

        return view('claims.edit', compact('claim', 'kendaraans', 'bbms'));
    }

    public function update(Request $request, KlaimBBM $claim)
    {
        $validated = $request->validate([
            'periode' => 'required|date_format:Y-m',
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'details' => 'required|array',
            'details.*.tanggal' => 'required|date',
            'details.*.km' => 'required|numeric',
            'details.*.liter' => 'required|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        try {
            // Normalisasi format periode
            $periode = $validated['periode'];

            // Cek apakah ada periode sebelumnya dan hitung saldo awal
            $currentDate = Carbon::parse($periode);
            $previousPeriode = $currentDate->copy()->subMonth()->format('Y-m');

            // Default saldo awal
            $saldoAwal = 200.00;

            // Cari saldo dari periode sebelumnya
            $previousSaldo = SaldoBBM::where('user_id', Auth::id())
                ->where('periode', $previousPeriode)
                ->first();

            // Jika ada periode sebelumnya, ambil sisa saldonya sebagai saldo awal
            if ($previousSaldo) {
                $previousTotalPenggunaan = KlaimBBM::where('periode', $previousPeriode)
                    ->where('user_id', Auth::id())
                    ->sum('total_penggunaan_liter');
                $saldoAwal = 200.00 - $previousTotalPenggunaan;
                if ($saldoAwal < 0) $saldoAwal = 0;
            }

            // Dapatkan saldo BBM untuk periode ini
            $saldoBBM = SaldoBBM::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'periode' => $periode
                ],
                [
                    'saldo_awal' => $saldoAwal,
                    'total_penggunaan' => 0,
                    'sisa_saldo' => $saldoAwal,
                    'status' => 'normal'
                ]
            );

            $totalLiterBaru = collect($request->details)->sum('liter');
            $catatan = $request->catatan ?? '';

            // Hitung total penggunaan untuk periode ini (tidak termasuk klaim saat ini)
            $totalLiterPeriode = KlaimBBM::where('periode', $periode)
                ->where('user_id', Auth::id())
                ->where('id', '!=', $claim->id)
                ->sum('total_penggunaan_liter');

            // Cek apakah total penggunaan melebihi batas saldo awal
            $melebihi_batas = false;
            if (($totalLiterPeriode + $totalLiterBaru) > $saldoAwal) {
                $melebihi_batas = true;

                // Validasi catatan jika melebihi batas
                if (empty($catatan)) {
                    return back()->withInput()->withErrors([
                        'catatan' => 'Catatan wajib diisi ketika penggunaan BBM melebihi batas saldo'
                    ]);
                }
            }

            // Cek apakah update melebihi sisa saldo
            $sisaSaldo = $saldoAwal - $totalLiterPeriode;
            if ($totalLiterBaru > $sisaSaldo && !$melebihi_batas) {
                return back()->withErrors(['message' => "Total penggunaan BBM ({$totalLiterBaru} liter) melebihi sisa saldo ({$sisaSaldo} liter)"]);
            }

            DB::transaction(function () use ($claim, $validated, $totalLiterBaru, $saldoBBM, $request, $catatan, $periode, $saldoAwal, $totalLiterPeriode) {
                $bbm = BBM::find($claim->bbm_id);

                // Update klaim utama
                $claim->update([
                    'periode' => $periode,
                    'kendaraan_id' => $validated['kendaraan_id'],
                    'saldo_bbm_id' => $saldoBBM->id,
                    'jumlah_dana' => $totalLiterBaru * $bbm->harga_bbm,
                    'total_penggunaan_liter' => $totalLiterBaru,
                    'catatan' => $catatan
                ]);

                // Hapus detail lama
                $claim->details()->delete();

                // Buat detail baru
                foreach ($validated['details'] as $detail) {
                    DetailKlaimBBM::create([
                        'klaim_bbm_id' => $claim->id,
                        'tanggal' => $detail['tanggal'],
                        'km' => $detail['km'],
                        'bbm_id' => $claim->bbm_id,
                        'liter' => $detail['liter'],
                        'total_harga' => $detail['liter'] * $bbm->harga_bbm,
                    ]);
                }

                // Update saldo BBM
                $totalPenggunaanBaru = $totalLiterPeriode + $totalLiterBaru;
                $saldoBBM->saldo_awal = $saldoAwal;
                $saldoBBM->total_penggunaan = $totalPenggunaanBaru;
                $saldoBBM->sisa_saldo = $saldoAwal - $totalPenggunaanBaru;
                $saldoBBM->status = ($totalPenggunaanBaru > $saldoAwal) ? 'melebihi_batas' : 'normal';
                $saldoBBM->save();
            });

            return redirect()->route('claims.index')->with('success', 'Klaim BBM berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in update: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(KlaimBBM $claim)
    {
        if ($claim->user_id !== Auth::id() && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403, 'Data Tidak Ada.');
        }

        try {
            DB::transaction(function () use ($claim) {
                $periode = $claim->periode;
                $userId = $claim->user_id;

                // Hapus detail dan klaim
                $claim->details()->delete();
                $claim->delete();

                // Cek apakah ada periode sebelumnya dan hitung saldo awal
                $currentDate = Carbon::parse($periode);
                $previousPeriode = $currentDate->copy()->subMonth()->format('Y-m');

                // Default saldo awal
                $saldoAwal = 200.00;

                // Cari saldo dari periode sebelumnya
                $previousSaldo = SaldoBBM::where('user_id', $userId)
                    ->where('periode', $previousPeriode)
                    ->first();

                // Jika ada periode sebelumnya, ambil sisa saldonya sebagai saldo awall
                if ($previousSaldo) {
                    $previousTotalPenggunaan = KlaimBBM::where('periode', $previousPeriode)
                        ->where('user_id', $userId)
                        ->sum('total_penggunaan_liter');
                    $saldoAwal = 200.00 - $previousTotalPenggunaan;
                    if ($saldoAwal < 0) $saldoAwal = 0;
                }

                // Hitung total penggunaan baru untuk periode ini
                $totalPenggunaanBaru = KlaimBBM::where('periode', $periode)
                    ->where('user_id', $userId)
                    ->sum('total_penggunaan_liter');

                // Update saldo BBM
                $saldoBBM = SaldoBBM::where('user_id', $userId)
                    ->where('periode', $periode)
                    ->first();

                if ($saldoBBM) {
                    $saldoBBM->saldo_awal = $saldoAwal;
                    $saldoBBM->total_penggunaan = $totalPenggunaanBaru;
                    $saldoBBM->sisa_saldo = $saldoAwal - $totalPenggunaanBaru;
                    $saldoBBM->status = ($totalPenggunaanBaru > $saldoAwal) ? 'melebihi_batas' : 'normal';
                    $saldoBBM->save();
                }
            });

            return redirect()->route('claims.index')->with('success', 'Klaim BBM berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error in destroy: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus klaim: ' . $e->getMessage()]);
        }
    }

    public function preview(KlaimBBM $claim)
    {
        if ($claim->user_id !== Auth::id() && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403, 'Data Tidak Ada.');
        }

        $claim->load(['user', 'kendaraan', 'bbm', 'details', 'saldoBBM']);
        $departments = Department::all();

        return view('claims.preview', compact('claim', 'departments'));
    }

    public function pdf(KlaimBBM $claim)
    {
        if ($claim->user_id !== Auth::id() && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403, 'Data Tidak Ada.');
        }

        $claim->load(['user', 'kendaraan', 'bbm', 'details', 'saldoBBM']);
        $departments = Department::all();

        $pdf = PDF::loadView('claims.pdf', compact('claim', 'departments'));
        return $pdf->stream("form-klaim-bbm-{$claim->klaim_id}.pdf");
    }

    public function export(Request $request)
    {
        $filter = $request->filter ?? 'semua';
        $department_id = $request->department_id;
        $periode = $request->periode;
        $user_id = $request->user_id;
        $status = $request->status;

        $filename = 'klaim-bbm-' . date('YmdHis') . '.xlsx';

        return Excel::download(
            new KlaimBBMExport($filter, $department_id, $periode, $user_id, $status),
            $filename
        );
    }
}
