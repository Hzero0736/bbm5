<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Department;
use App\Exports\KendaraanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KendaraanController extends Controller
{
    public function index()
    {
        $data = [
            'kendaraans' => Kendaraan::with(['user.department'])->latest()->get(),
            'departments' => Department::orderBy('nama_department', 'asc')->get()
        ];

        return view('kendaraan.index', $data);
    }

    public function create()
    {
        $data = [
            'departments' => Department::with('users')->get()
        ];
        return view('kendaraan.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kendaraan' => 'required|max:100',
            'no_plat' => 'required|max:20|unique:kendaraan',
            'keperluan' => 'nullable|max:100',
            'user_id' => 'required|exists:users,id',
        ]);
        Kendaraan::create($validated);

        return redirect()->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan');
    }

    public function edit(Kendaraan $kendaraan)
    {
        if (Auth::id() !== $kendaraan->user_id && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403);
        }
        $data = [
            'kendaraan' => $kendaraan,
            'departments' => Department::with('users')->get()
        ];

        return view('kendaraan.edit', $data);;
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        if (Auth::id() !== $kendaraan->user_id && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'nama_kendaraan' => 'required|max:100',
            'keperluan' => 'nullable|max:100',
            'no_plat' => 'required|max:20|unique:kendaraan,no_plat,' . $kendaraan->id,
        ]);

        $kendaraan->update($validated);

        return redirect()->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil diperbarui');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        if (Auth::id() !== $kendaraan->user_id && !Auth::user()->roles->contains('nama', 'Admin')) {
            abort(403);
        }

        $kendaraan->delete();

        return redirect()->route('kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus');
    }

    public function export(Request $request)
    {
        $filter = $request->filter ?? 'all';
        $department_id = $request->department_id;
        $nama_department = $department_id ? Department::find($department_id)->nama_department : 'all';

        $filename = 'kendaraan_' . $filter;
        if ($department_id) {
            $filename .= '_dept_' . $nama_department;
        }
        $filename .= '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new KendaraanExport($filter, $department_id), $filename);
    }
}
