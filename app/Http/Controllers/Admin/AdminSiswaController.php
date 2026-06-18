<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Student;
use App\Models\Rombel;
use OpenSpout\Reader\XLSX\Reader;
class AdminSiswaController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('rombels')
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
                });
            })
            ->when($request->rombel_id, function($query, $rombel_id) {
                $query->whereHas('rombels', function($q) use ($rombel_id) {
                    $q->where('rombels.id', $rombel_id);
                });
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
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx']);

        $reader = new Reader();
        $reader->open($request->file('file')->getPathname());

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index === 1) continue;
                $cells = $row->toArray();
                if (empty($cells[0])) continue;
                Student::updateOrCreate(
                    ['nisn' => $cells[0]],
                    ['name' => $cells[1]] 
                );
            }
            break;
        }

        $reader->close();
        return back()->with('success', 'Data siswa berhasil diimport!');
    }
}
