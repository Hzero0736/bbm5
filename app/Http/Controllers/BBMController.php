<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BBM;

class BBMController extends Controller
{
    public function index()
    {
        $bbm = BBM::all();
        return view('bbm.index', compact('bbm'));
    }

    public function create()
    {
        return view('bbm.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bbm' => 'required',
            'harga_bbm' => 'required|numeric',
            'satuan_bbm' => 'required'
        ]);

        BBM::create($request->all());
        return redirect()->route('bbm.index')->with('success', 'Data BBM berhasil ditambahkan');
    }

    public function edit(BBM $bbm)
    {
        return view('bbm.edit', compact('bbm'));
    }

    public function update(Request $request, BBM $bbm)
    {
        $request->validate([
            'nama_bbm' => 'required',
            'harga_bbm' => 'required|numeric',
            'satuan_bbm' => 'required'
        ]);

        $bbm->update($request->all());
        return redirect()->route('bbm.index')->with('success', 'Data BBM berhasil diupdate');
    }

    public function destroy(BBM $bbm)
    {
        $bbm->delete();
        return redirect()->route('bbm.index')->with('success', 'Data BBM berhasil dihapus');
    }
}
