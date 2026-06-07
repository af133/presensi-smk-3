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

    // Memproses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nip' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt(['nip' => $request->nip, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('guru.dashboard');
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
