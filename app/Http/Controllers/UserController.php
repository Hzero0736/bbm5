<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register()
    {
        $roles = Role::all();
        $department = Department::all();
        return view('auth.register', compact('roles', 'department'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'required|exists:department,id',
            'posisi' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'posisi' => $request->posisi,
            'status' => 'menunggu'
        ]);

        $user->roles()->attach($request->role_id);

        return redirect()->route('register')
            ->with('success', 'Registrasi berhasil. Mohon tunggu persetujuan admin.');
    }

    public function pendingApprovals()
    {
        $pendingUsers = User::where('status', 'menunggu')->get();
        $approvedUsers = User::where('status', 'disetujui')->get();
        $rejectedUsers = User::where('status', 'ditolak')->get();

        return view('admin.pending-approvals', compact('pendingUsers', 'approvedUsers', 'rejectedUsers'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'disetujui';
        $user->update();

        return redirect()->back()->with('success', 'User berhasil disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $user = User::findOrFail($id);
        $user->status = 'ditolak';
        $user->rejection_reason = $request->rejection_reason;
        $user->update();

        return redirect()->back()->with('success', 'User berhasil ditolak');
    }

    public function profile()
    {
        return view('profile.index');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $user = User::find(Auth::id());
        $user->nama = $request->nama;
        $user->save();

        return redirect()->route('profile.index')
            ->with('success', 'Profile berhasil diupdate');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::find(Auth::id());
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diupdate');
    }
}
