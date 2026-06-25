<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Rombel;
use App\Models\Schedule;
use App\Models\StudentPresence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WaliKelasController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->query('search');
        $rombelId = $request->query('rombel_id');

        $studentsAll = collect();
        $studentsTeacher = collect();
        $studentsPerGuru = collect();
        $rombels = Rombel::all();

      if ($user->hasPermission('can_laporan_presensi_siswa_all')) {
            $studentsAll = Student::query()
                ->when($rombelId, fn($q) => $q->whereHas('rombels', fn($q) => $q->where('rombels.id', $rombelId)))
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                ->with('rombels')
                ->orderBy('name')
                ->paginate(20, ['*'], 'page_all')->withQueryString();
        }

        if ($user->hasPermission('can_laporan_presensi_siswa_guru')) {
            $rombel = Rombel::where('guru_id', $user->id)->first();
            if ($rombel) {
                $studentsTeacher = Student::whereHas('rombels', fn($q) => $q->where('rombels.id', $rombel->id))
                    ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                    ->with('rombels')
                    ->orderBy('name')
                    ->paginate(20, ['*'], 'page_teacher')->withQueryString();
            }
        }

        if ($user->hasPermission('can_laporan_presensi_siswa_perguru')) {
            $rombelIds = Schedule::where('teacher_id', $user->id)
                ->distinct()
                ->pluck('rombel_id');

            if ($rombelIds->isNotEmpty()) {
                $studentsPerGuru = Student::whereHas('rombels', fn($q) => $q->whereIn('rombels.id', $rombelIds))
                    ->when($rombelId, fn($q) => $q->whereHas('rombels', fn($q) => $q->where('rombels.id', $rombelId)))
                    ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                    ->with('rombels')
                    ->orderBy('name')
                    ->distinct()
                    ->paginate(20, ['*'], 'page_perguru')->withQueryString();
            }
        }

        return view('guru.report.index', compact('studentsAll', 'studentsTeacher', 'studentsPerGuru', 'rombels', 'user'));
    }


    private function authorizeStudentAccess(Request $request, $studentId): array
    {
        $user = Auth::user();
        $requestedScope = $request->query('scope');

        $tryAll = function () use ($user, $studentId) {
            if (!$user->hasPermission('can_laporan_presensi_siswa_all')) {
                return null;
            }
            $student = Student::find($studentId);
            return $student ? ['student' => $student, 'scope' => 'all'] : null;
        };

        $tryWaliKelas = function () use ($user, $studentId) {
            if (!$user->hasPermission('can_laporan_presensi_siswa_guru')) {
                return null;
            }
            $rombel = Rombel::where('guru_id', $user->id)->first();
            if (!$rombel) {
                return null;
            }
            $student = Student::whereHas('rombels', fn($q) => $q->where('rombels.id', $rombel->id))
                ->where('id', $studentId)
                ->first();
            return $student ? ['student' => $student, 'scope' => 'wali_kelas'] : null;
        };

        $tryPerGuru = function () use ($user, $studentId) {
            if (!$user->hasPermission('can_laporan_presensi_siswa_perguru')) {
                return null;
            }
            $rombelIds = Schedule::where('teacher_id', $user->id)->distinct()->pluck('rombel_id');
            if ($rombelIds->isEmpty()) {
                return null;
            }
            $student = Student::whereHas('rombels', fn($q) => $q->whereIn('rombels.id', $rombelIds))
                ->where('id', $studentId)
                ->first();
            return $student ? ['student' => $student, 'scope' => 'per_guru'] : null;
        };

        $resolvers = [
            'all'        => $tryAll,
            'wali_kelas' => $tryWaliKelas,
            'per_guru'   => $tryPerGuru,
        ];
        if ($requestedScope && isset($resolvers[$requestedScope])) {
            $result = $resolvers[$requestedScope]();
            if ($result) {
                return $result;
            }
            abort(403, 'Anda tidak memiliki akses ke laporan siswa ini melalui tab tersebut.');
        }
        foreach ($resolvers as $resolver) {
            $result = $resolver();
            if ($result) {
                return $result;
            }
        }

        abort(403, 'Anda tidak memiliki akses ke laporan siswa ini.');
    }

    /**
     * @param Request $request
     * @param Student $student
     * @param int|null $teacherId
     */
    private function buildReportData(Request $request, Student $student, ?int $teacherId = null)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date',
        ]);
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        $presences = StudentPresence::where('student_id', $student->id)
            ->whereHas('presence', function ($q) use ($from, $to, $teacherId) {
                $q->whereBetween('date', [$from, $to]);
                if ($teacherId) {
                    $q->where('user_id', $teacherId);
                }
            })
            ->with([
                'presence.user',
                'presence',
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
                    $sameSubject = $next->presence->subject_name === $current->presence->subject_name;
                    $sameTeacher = $next->presence->user_id === $current->presence->user_id;
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
                    'date'           => $date,
                    'subject'        => $first->presence->subject_name ?? '-',
                    'teacher'        => $first->presence->user->name ?? '-',
                    'rombel'         => $first->presence->rombel_name ?? '-',
                    'classroom'      => $first->presence->classroom_name ?? '-',
                    'academic_year'  => $first->presence->academic_years ?? '-',
                    'start_time'     => $first->presence->start_time,
                    'end_time'       => $last->presence->end_time,
                    'status'         => $worstStatus,
                    'sessions'       => count($group),
                ];
            }

            $grouped[$date] = $mergedRows;
        }

        return compact('from', 'to', 'rekap', 'grouped');
    }

    public function downloadReport(Request $request, $studentId)
    {
        ['student' => $student, 'scope' => $scope] = $this->authorizeStudentAccess($request, $studentId);
        $teacherFilterId = $scope === 'per_guru' ? Auth::id() : null;

        ['from' => $from, 'to' => $to, 'rekap' => $rekap, 'grouped' => $grouped] = $this->buildReportData($request, $student, $teacherFilterId);
        $lastRow = collect($grouped)->flatten(1)->last();
        $rombelInfo = [
            'name'          => $lastRow['rombel'] ?? '-',
            'classroom'     => $lastRow['classroom'] ?? '-',
            'academic_year' => $lastRow['academic_year'] ?? '-',
        ];

        $pdf = Pdf::loadView('walikelas.report.pdf', compact(
            'student', 'rombelInfo', 'grouped', 'rekap', 'from', 'to'
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan_' . str_replace(' ', '_', $student->name) . '_' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function previewReport(Request $request, $studentId)
    {
        ['student' => $student, 'scope' => $scope] = $this->authorizeStudentAccess($request, $studentId);

        $teacherFilterId = $scope === 'per_guru' ? Auth::id() : null;

        ['from' => $from, 'to' => $to, 'rekap' => $rekap, 'grouped' => $grouped] = $this->buildReportData($request, $student, $teacherFilterId);

        $lastRow = collect($grouped)->flatten(1)->last();
        $rombelInfo = [
            'name'          => $lastRow['rombel'] ?? '-',
            'classroom'     => $lastRow['classroom'] ?? '-',
            'academic_year' => $lastRow['academic_year'] ?? '-',
        ];

        $pdf = Pdf::loadView('walikelas.report.pdf', compact('student', 'rombelInfo', 'grouped', 'rekap', 'from', 'to'));
        return $pdf->stream('Preview_Laporan.pdf');
    }
}