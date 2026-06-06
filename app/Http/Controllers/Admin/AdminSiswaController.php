<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Student;
use App\Models\Rombel;
class AdminSiswaController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('rombels')
        ->when($request->search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
        })
        ->paginate(10)
        ->withQueryString();
        $rombels = Rombel::all();
        return view('admin.siswa.index', compact('students', 'rombels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'nisn'      => 'required|unique:students,nisn', 
            'rombel_id' => 'required'
        ]);
        
        $student = Student::create($request->only(['name', 'nisn']));
        $student->rombels()->attach($request->rombel_id);
        
        return back()->with('success', 'Siswa berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->only(['name', 'nisn']));
        $student->rombels()->sync([$request->rombel_id]); 
        
        return back()->with('success', 'Data siswa diperbarui');
    }

    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return back()->with('success', 'Data siswa dihapus');
    }
}
