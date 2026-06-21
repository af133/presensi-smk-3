<?php

namespace App\Http\Controllers\Admin;
use App\Models\Subject;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Rombel;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Student;
use Illuminate\Database\QueryException;
class AdminSubjectController extends Controller
{
    public function subjectIndex(Request $request)
    {
        $subjects = Subject::query()
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->paginate(10)
        ->withQueryString();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function subjectStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        Subject::create($request->all());
        
        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil ditambah.');
    }

    public function subjectUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $subject = Subject::findOrFail($id);
        $subject->update($request->all());
        
        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil diupdate.');
    }

   public function subjectDestroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->delete();

            return redirect()
                ->route('admin.subjects.index')
                ->with('success', 'Mata pelajaran berhasil dihapus.');

        } catch (QueryException $e) {

            if ($e->errorInfo[1] == 1451) {
                return redirect()
                    ->route('admin.subjects.index')
                    ->with('error', 'Mata pelajaran tidak dapat dihapus karena masih digunakan oleh data lain.');
            }

            throw $e;
        }
    }
    public function classroomIndex(Request $request)
{
    $classrooms = Classroom::query()
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->paginate(10)
        ->withQueryString(); 

    return view('admin.classrooms.index', compact('classrooms'));
}

    public function classroomStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        Classroom::create($request->all());
        
        return redirect()->route('admin.classrooms.index')->with('success', 'Ruang kelas berhasil ditambah.');
    }

    public function classroomUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $classroom = Classroom::findOrFail($id);
        $classroom->update($request->all());
        
        return redirect()->route('admin.classrooms.index')->with('success', 'Ruang kelas berhasil diupdate.');
    }

    public function classroomDestroy($id)
    {
        Classroom::findOrFail($id)->delete();
        
        return redirect()->route('admin.classrooms.index')->with('success', 'Ruang kelas berhasil dihapus.');
    }
    public function rombelsIndex(Request $request)
{
    $rombels = Rombel::query()
        ->with(['waliKelas', 'academicYear', 'students'])
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->paginate(10)
        ->withQueryString();
    $teachers = User::whereHas('roles', function ($query) {
        $query->where('name','!=', 'admin');
    })->where('status',1)->get();
    $academicYears = AcademicYear::where('is_active',1)->get();
    
    return view('admin.rombels.index', compact('rombels', 'teachers', 'academicYears'));
}
public function rombelsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'guru_id' => 'required|exists:users,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        Rombel::create($request->all());

        return back()->with('success', 'Rombel berhasil ditambahkan.');
    }

    public function rombelsUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'guru_id' => 'required|exists:users,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $rombel = Rombel::findOrFail($id);
        $rombel->update($request->all());

        return back()->with('success', 'Rombel berhasil diupdate.');
    }

    public function rombelsDestroy($id)
    {
        $rombel = Rombel::withCount('students')->findOrFail($id);
        if ($rombel->students_count > 0) {
            return back()->with('error', 'Data tidak bisa dihapus karena masih ada ' . $rombel->students_count . ' siswa terdaftar di rombel ini.');
        }

        $rombel->delete();

        return back()->with('success', 'Rombel berhasil dihapus.');
    }

     public function show($id)
    {
        $rombel = Rombel::with('students')->findOrFail($id);
        $studentsAvailable = Student::whereNotIn('id', $rombel->students->pluck('id'))->get();
        $studentsNoRombel = Student::doesntHave('rombels')->get();
        
        return view('admin.rombels.show', compact('rombel', 'studentsAvailable', 'studentsNoRombel'));
    }

    public function addStudent(Request $request, $id)
    {
        $request->validate(['student_id' => 'required|exists:students,id']);
        
        $rombel = Rombel::findOrFail($id);
        $rombel->students()->syncWithoutDetaching([$request->student_id]);
        
        return back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function removeStudent($id, $student_id)
    {
        $rombel = Rombel::findOrFail($id);
        $rombel->students()->detach($student_id);
        
        return back()->with('success', 'Siswa berhasil dihapus dari rombel.');
    }
    public function bulkAdd(Request $request, $id) {
        $rombel = Rombel::findOrFail($id);
        $rombel->students()->syncWithoutDetaching($request->student_ids);
        return back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function bulkRemove(Request $request, $id) {
        $rombel = Rombel::findOrFail($id);
        $rombel->students()->detach($request->student_ids);
        return back()->with('success', 'Siswa berhasil dihapus.');
    }
    public function bulkMove(Request $request, $id) {
        $targetRombel = Rombel::findOrFail($request->target_rombel_id);
        $rombel = Rombel::findOrFail($id);
        $rombel->students()->detach($request->student_ids);
        $targetRombel->students()->syncWithoutDetaching($request->student_ids);
        
        return back()->with('success', 'Siswa berhasil dipindahkan.');
    }
}
