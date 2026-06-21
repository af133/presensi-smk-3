<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class AutenfikasiController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
            
            if (auth()->user()->hasRole('guru')) {
                return redirect()->route('guru.dashboard');
            }
        }
        return view('login-guru');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nip' => 'required|string',
            'password' => 'required',
        ]);
        if (Auth::guard('web')->attempt(['nip' => $request->nip, 'password' => $request->password], 
        $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = auth()->user();
            if($user->hasPermission('can_jadwal_kelas')){
                $route='guru.dashboard';
            }
            else if($user->hasPermission('can_laporan_presensi_siswa_all')||$user->hasPermission('can_laporan_presensi_siswa_perguru')||$user->hasPermission('can_laporan_presensi_siswa_guru')){
                $route= 'guru.report.index';
            }
            else if ($user->hasPermission('can_laporan_presensi_guru')){
                 $route='report.index';
            }
            else if ($user->hasPermission('can_monitoring_kelas')){
                $route='monitoring.index';
            }else{
                $route='guru.profile.edit';
            }
            return redirect()->route($route);
        }
        throw ValidationException::withMessages([
            'nip' => 'NIP/NIK atau Password salah.',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout(); 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
