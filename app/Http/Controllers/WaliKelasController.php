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

        if ($user->hasRole('bk')) {
            $rombel = null;
            $students = Student::when($search, function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->paginate(20) 
                ->withQueryString(); 
        } else {
            $rombel = Rombel::where('guru_id', $user->id)->firstOrFail();
            
            $students = Student::whereHas('rombels', fn($q) => $q->where('rombels.id', $rombel->id))
                ->when($search, function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString();
        }

        return view('guru.report.index', compact('students', 'rombel'));
    }
    public function downloadReport(Request $request, $studentId)
{
    $request->validate([
        'from' => 'required|date',
        'to'   => 'required|date|after_or_equal:from',
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
            'presence.schedule.time',
            'presence.schedule.teacher',
        ])
        ->get()
        ->sortBy(fn($sp) => $sp->presence->date . $sp->presence->schedule->time->start_time);

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
            $id = $sp->presence->schedule_id;
            if (in_array($id, $used)) continue;

            $group   = [$sp];
            $used[]  = $id;
            $current = $sp;
            foreach ($items as $next) {
                $nextId = $next->presence->schedule_id;
                if (in_array($nextId, $used)) continue;

                $sameSubject = $next->presence->schedule->subject_id === $current->presence->schedule->subject_id;
                $sameTeacher = $next->presence->schedule->teacher_id === $current->presence->schedule->teacher_id;
                $consecutive = $next->presence->schedule->time->start_time === $current->presence->schedule->time->end_time;

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
                'start_time' => $first->presence->schedule->time->start_time,
                'end_time'   => $last->presence->schedule->time->end_time,
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
}
