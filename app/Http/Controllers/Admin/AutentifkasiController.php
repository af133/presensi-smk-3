<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutentifkasiController extends Controller
{
    public function loginIndex()
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('admin')) {
                    return redirect()->intended('/admin/dashboard');
            }
        }
        return view('admin.auth.login');
    }

    public function loginProcess(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember'); 

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            if (Auth::user()->hasRole('admin')) {
                return redirect()->intended('/admin/dashboard');
            }
            Auth::logout();
            return back()->withErrors(['email' => 'Anda tidak memiliki akses admin!']);
        }

        return back()->withErrors(['email' => 'Email atau password salah!']);
    }
    public function logout(Request $request)
    {
        Auth::logout(); 
        
        $request->session()->invalidate(); 
        $request->session()->regenerateToken();
        
        return redirect('admin/login');
    }
}
