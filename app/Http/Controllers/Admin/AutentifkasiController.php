<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutentifkasiController extends Controller
{
    public function index()
    {
        return "Berhasil! Anda telah melewati gerbang middleware admin.";
    }
}
