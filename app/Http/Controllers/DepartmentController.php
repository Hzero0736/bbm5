<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('department.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_department' => 'required|unique:department',
            'nama_department' => 'required',
            'cost_center' => 'required'
        ]);

        Department::create($validated);
        return redirect()->route('departments.index')->with('success', 'Department berhasil ditambahkan');
    }

    public function edit(Department $department)
    {
        return view('department.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'kode_department' => 'required|unique:department,kode_department,' . $department->id,
            'nama_department' => 'required',
            'cost_center' => 'required'
        ]);

        $department->update($validated);
        return redirect()->route('departments.index')->with('success', 'Department berhasil diupdate');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department berhasil dihapus');
    }
}
