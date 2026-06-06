<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use App\Models\Schedule;
use App\Models\Day;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Rombel;
use App\Models\Subject;
use App\Models\User;

class AdminScheduleController extends Controller
{
    // Tambahkan di Controller kamu
    public function index(Request $request)
    {
        $times = TimeSlot::query()
            ->when($request->day_id, function ($query, $day_id) {
                return $query->where('day_id', $day_id);
            })
            ->get();

        $days = Day::all(); 
        return view('admin.times.index', compact('times', 'days'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'day_id' => 'required|exists:days,id',
            'jam_ke' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        TimeSlot::create($data);
        return back()->with('success', 'Jam pelajaran berhasil ditambahkan!');
    }

    public function update(Request $request, $id) {
        $request->validate([
            'day_id' => 'required|exists:days,id',
            'jam_ke' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $time = TimeSlot::findOrFail($id);
        $time->update($request->all());
        
        return redirect()->route('admin.times.index')->with('success', 'Data berhasil diupdate');
    }
    public function daysIndex()
    {
        // Mengambil semua hari (pastikan seeder sudah dijalankan agar data ada)
        $days = Day::all(); 
        return view('admin.days.index', compact('days'));
    }
    public function manage($day_id)
    {
        // 1. Ambil data hari untuk judul
        $day = Day::findOrFail($day_id);

        // 2. Ambil semua slot waktu di hari tersebut beserta jadwal yang ada di dalamnya
        $timeSlots = TimeSlot::where('day_id', $day_id)
            ->with(['schedules.rombel', 'schedules.subject', 'schedules.teacher', 'schedules.classroom'])
            ->get();

        // 3. Ambil data pendukung untuk dropdown modal
        $rombels = Rombel::all();
        $subjects = Subject::all();
        $teachers = User::whereHas('roles', function($query) {
            $query->where('name','!=', 'admin');
        })->get();
        
        $classrooms = Classroom::all();

        return view('admin.days.manage', compact('day', 'timeSlots', 'rombels', 'subjects', 'teachers', 'classrooms'));
    }
    public function manageStore(Request $request)
    {
        $request->validate([
            'time_slot_id' => 'required',
            'rombel_id'    => 'required',
            'subject_id'   => 'required',
            'teacher_id'   => 'required',
            'classroom_id' => 'required',
        ]);
        // dd($request->all());

        Schedule::create([
            'time_slot_id' => $request->time_slot_id,
            'rombel_id'    => $request->rombel_id,
            'subject_id'   => $request->subject_id,
            'teacher_id'   => $request->teacher_id,
            'classroom_id' => $request->classroom_id,
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function manageUpdate(Request $request, $id)
    {
        $request->validate([
            'rombel_id'    => 'required',
            'subject_id'   => 'required',
            'teacher_id'   => 'required',
            'classroom_id' => 'required',
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update($request->all());

        return back()->with('success', 'Jadwal berhasil diupdate!');
    }

    public function manageDestroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus!');
    }
}
