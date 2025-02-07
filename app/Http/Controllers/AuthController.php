<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->status === 'menunggu') {
                Auth::logout();
                return back()->with('error', 'Akun Anda masih menunggu persetujuan admin.');
            }

            if ($user->status === 'ditolak') {
                Auth::logout();
                return back()->with('error', 'Akun Anda ditolak dengan alasan: ' . $user->rejection_reason . 'Silahkan hubungi admin');
            }

            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Berhasil login!');
        }

        return back()->with('error', 'Email atau password salah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout!');
    }
}
