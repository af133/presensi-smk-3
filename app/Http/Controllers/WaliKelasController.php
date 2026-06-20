<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Rombel;
use App\Models\StudentPresence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
class WaliKelasController extends Controller
{
  
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->query('search');
        $rombelId = $request->query('rombel_id');
        
        $studentsAll = collect();
        $studentsTeacher = collect();
        $rombels = Rombel::all();

        if ($user->hasPermission('can_laporan_presensi_siswa_all')) {
            $studentsAll = Student::query()
                ->when($rombelId, fn($q) => $q->whereHas('rombels', fn($q) => $q->where('rombels.id', $rombelId)))
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                ->orderBy('name')
                ->paginate(20, ['*'], 'page_all')->withQueryString();
        }

        if ($user->hasPermission('can_laporan_presensi_siswa_guru')) {
            $rombel = Rombel::where('guru_id', $user->id)->first();
            if ($rombel) {
                $studentsTeacher = Student::whereHas('rombels', fn($q) => $q->where('rombels.id', $rombel->id))
                    ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                    ->orderBy('name')
                    ->paginate(20, ['*'], 'page_teacher')->withQueryString();
            }
        }

        return view('guru.report.index', compact('studentsAll', 'studentsTeacher', 'rombels','user'));
    }
    public function downloadReport(Request $request, $studentId)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date',
        ]);
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();
        $rombel = Rombel::where('guru_id', Auth::id())->firstOrFail();
        $student = Student::whereHas('rombels', fn($q) => $q->where('rombels.id', $rombel->id))
            ->findOrFail($studentId);
        $presences = StudentPresence::where('student_id', $student->id)
            ->whereHas('presence', fn($q) => $q->whereBetween('date', [$from, $to]))
            ->with([
                'presence.schedule.subject',
                'presence.schedule.teacher',
                'presence' 
            ])
            ->get()
            ->sortBy(fn($sp) => $sp->presence->date . $sp->presence->start_time);
        $rekap = [
            'hadir' => $presences->where('status', 'hadir')->count(),
            'sakit' => $presences->where('status', 'sakit')->count(),
            'izin'  => $presences->where('status', 'izin')->count(),
            'alpha' => $presences->where('status', 'alpha')->count(),
            'total' => $presences->count(),
        ];
        $grouped = [];
        foreach ($presences->groupBy(fn($sp) => $sp->presence->date) as $date => $items) {
            $used    = [];
            $mergedRows = [];

            foreach ($items as $sp) {
                $id = $sp->presence_id;
                if (in_array($id, $used)) continue;

                $group   = [$sp];
                $used[]  = $id;
                $current = $sp;
                foreach ($items as $next) {
                    $nextId = $next->presence_id;
                    if (in_array($nextId, $used)) continue;
                    $sameSubject = $next->presence->schedule->subject_id === $current->presence->schedule->subject_id;
                    $sameTeacher = $next->presence->schedule->teacher_id === $current->presence->schedule->teacher_id;
                    $consecutive = $next->presence->start_time === $current->presence->end_time;
                    if ($sameSubject && $sameTeacher && $consecutive) {
                        $group[]  = $next;
                        $used[]   = $nextId;
                        $current  = $next;
                    }
                }
                $first = $group[0];
                $last  = end($group);
                $priority = ['alpha' => 4, 'bolos' => 3, 'izin' => 2, 'sakit' => 1, 'hadir' => 0];
                $worstStatus = collect($group)
                    ->sortByDesc(fn($s) => $priority[$s->status] ?? 0)
                    ->first()->status;

                $mergedRows[] = [
                    'date'       => $date,
                    'subject'    => $first->presence->schedule->subject->name ?? '-',
                    'teacher'    => $first->presence->schedule->teacher->name ?? '-',
                    'start_time' => $first->presence->start_time,
                    'end_time'   => $last->presence->end_time,
                    'status'     => $worstStatus,
                    'sessions'   => count($group),
                ];
            }

            $grouped[$date] = $mergedRows;
        }
        $pdf = Pdf::loadView('walikelas.report.pdf', compact(
            'student', 'rombel', 'grouped', 'rekap', 'from', 'to'
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan_' . str_replace(' ', '_', $student->name) . '_' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
    public function previewReport(Request $request, $studentId)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date',
        ]);
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();
        $rombel = Rombel::where('guru_id', Auth::id())->firstOrFail();
        $student = Student::whereHas('rombels', fn($q) => $q->where('rombels.id', $rombel->id))
            ->findOrFail($studentId);
        $presences = StudentPresence::where('student_id', $student->id)
            ->whereHas('presence', fn($q) => $q->whereBetween('date', [$from, $to]))
            ->with([
                'presence.schedule.subject',
                'presence.schedule.teacher',
                'presence' 
            ])
            ->get()
            ->sortBy(fn($sp) => $sp->presence->date . $sp->presence->start_time);
        $rekap = [
            'hadir' => $presences->where('status', 'hadir')->count(),
            'sakit' => $presences->where('status', 'sakit')->count(),
            'izin'  => $presences->where('status', 'izin')->count(),
            'alpha' => $presences->where('status', 'alpha')->count(),
            'total' => $presences->count(),
        ];
        $grouped = [];
        foreach ($presences->groupBy(fn($sp) => $sp->presence->date) as $date => $items) {
            $used    = [];
            $mergedRows = [];

            foreach ($items as $sp) {
                $id = $sp->presence_id;
                if (in_array($id, $used)) continue;

                $group   = [$sp];
                $used[]  = $id;
                $current = $sp;
                foreach ($items as $next) {
                    $nextId = $next->presence_id;
                    if (in_array($nextId, $used)) continue;
                    $sameSubject = $next->presence->schedule->subject_id === $current->presence->schedule->subject_id;
                    $sameTeacher = $next->presence->schedule->teacher_id === $current->presence->schedule->teacher_id;
                    $consecutive = $next->presence->start_time === $current->presence->end_time;
                    if ($sameSubject && $sameTeacher && $consecutive) {
                        $group[]  = $next;
                        $used[]   = $nextId;
                        $current  = $next;
                    }
                }
                $first = $group[0];
                $last  = end($group);
                $priority = ['alpha' => 4, 'bolos' => 3, 'izin' => 2, 'sakit' => 1, 'hadir' => 0];
                $worstStatus = collect($group)
                    ->sortByDesc(fn($s) => $priority[$s->status] ?? 0)
                    ->first()->status;

                $mergedRows[] = [
                    'date'       => $date,
                    'subject'    => $first->presence->schedule->subject->name ?? '-',
                    'teacher'    => $first->presence->schedule->teacher->name ?? '-',
                    'start_time' => $first->presence->start_time,
                    'end_time'   => $last->presence->end_time,
                    'status'     => $worstStatus,
                    'sessions'   => count($group),
                ];
            }

            $grouped[$date] = $mergedRows;
        }
        
        $pdf = Pdf::loadView('walikelas.report.pdf', compact('student', 'rombel', 'grouped', 'rekap', 'from', 'to'));
    return $pdf->stream('Preview_Laporan.pdf');
    }   
}
