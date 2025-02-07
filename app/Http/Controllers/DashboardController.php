<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\KlaimBBM;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        if ($user->roles->contains('nama', 'Admin')) {
            $data = [
                'pendingUsers' => User::where('status', 'menunggu')->count(),
                'totalClaims' => KlaimBBM::count(),
                'todayClaims' => KlaimBBM::whereDate('created_at', $today)->count(),
                'totalDana' => KlaimBBM::sum('jumlah_dana'),
                'totalLiter' => KlaimBBM::sum('total_penggunaan_liter'),
                'recentClaims' => KlaimBBM::with(['user', 'kendaraan', 'bbm', 'details'])
                    ->latest()
                    ->take(8)
                    ->get(),
                'monthlyStats' => KlaimBBM::selectRaw('MONTH(created_at) as month, SUM(jumlah_dana) as total_dana, SUM(total_penggunaan_liter) as total_liter')
                    ->whereYear('created_at', date('Y'))
                    ->groupBy('month')
                    ->get()
            ];
        } else {
            $data = [
                'totalClaims' => KlaimBBM::where('user_id', $user->id)->count(),
                'todayClaims' => KlaimBBM::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count(),
                'totalDana' => KlaimBBM::where('user_id', $user->id)
                    ->sum('jumlah_dana'),
                'totalLiter' => KlaimBBM::where('user_id', $user->id)
                    ->sum('total_penggunaan_liter'),
                'recentClaims' => KlaimBBM::with(['kendaraan', 'bbm', 'details'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->take(8)
                    ->get(),
                'monthlyStats' => KlaimBBM::where('user_id', $user->id)
                    ->selectRaw('MONTH(created_at) as month, SUM(jumlah_dana) as total_dana, SUM(total_penggunaan_liter) as total_liter')
                    ->whereYear('created_at', date('Y'))
                    ->groupBy('month')
                    ->get()
            ];
        }

        return view('dashboard', $data);
    }
}
