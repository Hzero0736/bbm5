<?php

namespace App\Http\Controllers;

use App\Models\KlaimBBM;
use App\Models\DetailKlaimBBM;
use App\Models\Kendaraan;
use App\Models\BBM;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class KlaimBBMController extends Controller
{
    public function index()
    {
        $claims = Auth::user()->roles->contains('nama', 'Admin')
            ? KlaimBBM::with(['user', 'kendaraan', 'bbm'])->latest()->get()
            : KlaimBBM::with(['user', 'kendaraan', 'bbm'])
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
            'details.*.liter' => 'required|numeric|min:0'
        ]);

        // Cari semua klaim di periode yang sama
        $claimsInPeriod = KlaimBBM::where('periode', $validated['periode'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Hitung total penggunaan di periode yang sama
        $totalPenggunaanPeriode = $claimsInPeriod->sum('total_penggunaan_liter');

        // Saldo awal 200 dikurangi total penggunaan periode yang sama
        $saldoAwal = 200.00 - $totalPenggunaanPeriode;

        $bbm = BBM::findOrFail($request->bbm_id);
        $totalLiter = collect($request->details)->sum('liter');
        $totalDana = $totalLiter * $bbm->harga_bbm;

        // Validasi penggunaan tidak melebihi sisa saldo
        if ($totalLiter > $saldoAwal) {
            return back()->withErrors([
                'message' => "Total penggunaan BBM ({$totalLiter} liter) melebihi sisa saldo ({$saldoAwal} liter)"
            ]);
        }

        // Buat klaim baru
        $claim = KlaimBBM::create([
            'no_acc' => 'KL-' . date('Ymd') . '-' . rand(1000, 9999),
            'periode' => $validated['periode'],
            'user_id' => Auth::id(),
            'kendaraan_id' => $validated['kendaraan_id'],
            'bbm_id' => $bbm->id,
            'jumlah_dana' => $totalDana,
            'saldo_liter' => $saldoAwal,
            'total_penggunaan_liter' => $totalLiter,
            'sisa_saldo_liter' => $saldoAwal - $totalLiter
        ]);

        // Buat detail klaim
        $sisaSaldo = $saldoAwal;
        foreach ($request->details as $detail) {
            $sisaSaldo -= $detail['liter'];
            DetailKlaimBBM::create([
                'klaim_bbm_id' => $claim->id,
                'periode' => $validated['periode'],
                'tanggal' => $detail['tanggal'],
                'km' => $detail['km'],
                'bbm_id' => $bbm->id,
                'liter' => $detail['liter'],
                'total_harga' => $detail['liter'] * $bbm->harga_bbm,
                'sisa_saldo_liter' => $sisaSaldo
            ]);
        }

        return redirect()->route('claims.index')->with('success', 'Klaim BBM berhasil dibuat');
    }



    public function getSisaSaldo($periode)
    {
        $lastClaim = KlaimBBM::where('user_id', Auth::id())
            ->where('periode', $periode)
            ->latest()
            ->first();

        return response()->json([
            'sisa_saldo' => $lastClaim ? $lastClaim->sisa_saldo_liter : 200.00
        ]);
    }


    public function show(KlaimBBM $claim)
    {
        if ($claim->user_id !== Auth::id() && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403, 'Data Tidak Ada.');
        }

        $claim->load(['user', 'kendaraan', 'bbm', 'details']);
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
            'details.*.liter' => 'required|numeric|min:0'
        ]);

        $totalLiter = collect($request->details)->sum('liter');
        if ($totalLiter > 200) {
            return back()->withErrors(['message' => 'Total penggunaan BBM tidak boleh melebihi 200 liter']);
        }

        $bbm = BBM::find($claim->bbm_id);
        $totalDana = $totalLiter * $bbm->harga_bbm;

        $claim->update([
            'periode' => $validated['periode'],
            'kendaraan_id' => $validated['kendaraan_id'],
            'jumlah_dana' => $totalDana,
            'total_penggunaan_liter' => $totalLiter,
            'sisa_saldo_liter' => 200.00 - $totalLiter
        ]);

        $claim->details()->delete();

        $sisaSaldo = 200.00;
        foreach ($validated['details'] as $detail) {
            $sisaSaldo -= $detail['liter'];
            DetailKlaimBBM::create([
                'klaim_bbm_id' => $claim->id,
                'periode' => $validated['periode'],
                'tanggal' => $detail['tanggal'],
                'km' => $detail['km'],
                'bbm_id' => $claim->bbm_id,
                'liter' => $detail['liter'],
                'total_harga' => $detail['liter'] * $bbm->harga_bbm,
                'sisa_saldo_liter' => $sisaSaldo
            ]);
        }

        return redirect()->route('claims.index')->with('success', 'Klaim BBM berhasil diperbarui');
    }

    public function print(KlaimBBM $claim)
    {
        $claim->load(['user', 'kendaraan', 'bbm', 'details']);
        $pdf = PDF::loadView('claims.print', compact('claim'));
        return $pdf->stream("klaim-bbm-{$claim->no_acc}.pdf");
    }

    public function preview(KlaimBBM $claim)
    {
        if ($claim->user_id !== Auth::id() && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403, 'Data Tidak Ada.');
        }

        $claim->load(['user', 'kendaraan', 'bbm', 'details']);
        $departments = Department::all();

        return view('claims.preview', compact('claim', 'departments'));
    }

    public function pdf(KlaimBBM $claim)
    {
        if ($claim->user_id !== Auth::id() && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403, 'Data Tidak Ada.');
        }

        $claim->load(['user', 'kendaraan', 'bbm', 'details']);
        $departments = Department::all();

        $pdf = PDF::loadView('claims.pdf', compact('claim', 'departments'));
        return $pdf->stream("form-klaim-bbm-{$claim->no_acc}.pdf");
    }
}
