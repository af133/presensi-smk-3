<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Presence;
use App\Models\StudentPresence;
use App\Models\Student;
use Carbon\Carbon;
use App\Models\Journal;
use App\Models\Day;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Classroom;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Room;

class PresensiController extends Controller
{
    
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $currentDate = Carbon::parse($date)->startOfDay();

        $weekStart = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $dates = collect(range(0, 6))->map(fn($i) => $weekStart->copy()->addDays($i));
        $dayId = $currentDate->dayOfWeekIso;

        $schedules = Schedule::where('teacher_id', Auth::id())
            ->whereHas('time', fn($q) => $q->where('day_id', $dayId))
            ->with(['time', 'subject', 'classroom', 'rombel'])
            ->get()
            ->sortBy('time.start_time');
        $schedules->each(function ($schedule) use ($date) {
            $schedule->presence = Presence::where('schedule_id', $schedule->id)
                ->where('date', $date)
                ->with('journal')
                ->first();
        });
        $merged = collect();
        $used = collect();

        foreach ($schedules as $schedule) {
            if ($used->contains($schedule->id)) continue;

            $group = collect([$schedule]);
            $used->push($schedule->id);
            $current = $schedule;
            while (true) {
                $next = $schedules->first(function ($s) use ($current, $used) {
                    return !$used->contains($s->id)
                        && $s->classroom_id  === $current->classroom_id
                        && $s->rombel_id     === $current->rombel_id
                        && $s->subject_id    === $current->subject_id
                        && $s->time->start_time === $current->time->end_time;
                });

                if (!$next) break;

                $group->push($next);
                $used->push($next->id);
                $current = $next;
            }
            $first = $group->first();
            $last  = $group->last();

            $first->merged_start_time = $first->time->start_time;
            $first->merged_end_time   = $last->time->end_time;
            $first->merged_presences = $group->map->presence->filter()->values();
            $first->schedule_ids     = $group->map->id->values();

            $merged->push($first);
        }

        $schedules = $merged;

        return view('guru.dashboard', compact('schedules', 'currentDate', 'date', 'dates', 'weekStart'));
    }

    public function create(Request $request, $scheduleId)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $schedule = Schedule::with(['time', 'subject', 'classroom', 'rombel'])
            ->findOrFail($scheduleId);
        $dayId = Carbon::parse($date)->dayOfWeekIso;
        $allSchedules = Schedule::where('teacher_id', Auth::id())
            ->where('rombel_id',    $schedule->rombel_id)
            ->where('subject_id',   $schedule->subject_id)
            ->where('classroom_id', $schedule->classroom_id)
            ->whereHas('time', fn($q) => $q->where('day_id', $dayId))
            ->with('time')
            ->get()
            ->sortBy('time.start_time');
        $sessions = collect();
        $used     = collect();
        foreach ($allSchedules as $s) {
            if ($used->contains($s->id)) continue;

            $group   = collect([$s]);
            $used->push($s->id);
            $current = $s;

            while (true) {
                $next = $allSchedules->first(function ($n) use ($current, $used) {
                    return !$used->contains($n->id)
                        && $n->time->start_time === $current->time->end_time;
                });
                if (!$next) break;
                $group->push($next);
                $used->push($next->id);
                $current = $next;
            }
            if ($group->pluck('id')->contains((int) $scheduleId)) {
                $sessions = $group;
                break;
            }
        }
        $students = Student::whereHas('rombels', fn($q) => $q->where('rombels.id', $schedule->rombel_id))
            ->orderBy('name')
            ->get();
        $sessionData = $sessions->map(function ($sess) use ($date, $students) {
            $presence = Presence::where('schedule_id', $sess->id)
                ->where('date', $date)
                ->with('studentPresences')
                ->first();
            $statusMap = $presence
                ? $presence->studentPresences->keyBy('student_id')->map->status
                : collect();

            return [
                'schedule'  => $sess,
                'presence'  => $presence,
                'statusMap' => $statusMap,
            ];
        });

        return view('guru.presensi.create', compact(
            'schedule', 'sessions', 'sessionData', 'students', 'date'
        ));
    }

    public function store(Request $request, $scheduleId)
    {
        $request->validate([
            'date'                          => 'required|date',
            'presences'                     => 'required|array',
            'presences.*.*.student_id'      => 'required|exists:students,id',
            'presences.*.*.status'          => 'required|in:hadir,izin,sakit,alpha',
        ]);

        $date = $request->input('date');
        $scheduleIds = array_keys($request->input('presences'));
        
        DB::transaction(function () use ($request, $date, $scheduleIds) {
            foreach ($scheduleIds as $sessScheduleId) {
                $schedule= Schedule::where('id',$sessScheduleId)->first();
                $rows = $request->input("presences.{$sessScheduleId}", []);
                $presence = Presence::firstOrCreate(
                    ['schedule_id' => $sessScheduleId, 'date' => $date],
                    [
                        'user_id'       => Auth::id(),
                        'check_in_time' => now()->toTimeString(),
                        'start_time' => $schedule->time->start_time,
                        'end_time' => $schedule->time->end_time
                    ]
                );
                foreach ($rows as $row) {
                    StudentPresence::updateOrCreate(
                        [
                            'presence_id' => $presence->id,
                            'student_id'  => $row['student_id'],
                        ],
                        ['status' => $row['status']]
                    );
                }
            }
        });

        return redirect()
            ->route('guru.dashboard', ['date' => $date])
            ->with('success', 'Presensi berhasil disimpan.');
    }
    public function storeJournal(Request $request, $presenceId)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
        ]);
        $presence = Presence::where('id', $presenceId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        Journal::updateOrCreate(
            ['presence_id' => $presence->id],
            ['topic'       => $request->input('topic')]
        );
        return response()->json(['message' => 'Jurnal berhasil disimpan.']);
    }


    public function reportGuru(Request $request)
    {
        $teachers = User::whereHas('roles', fn($q) => $q->where('name', 'guru'))
            ->orderBy('name')
            ->paginate(10); 

        return view('guru.report.guru', compact('teachers'));
    }

    public function downloadAll(Request $request)
    {
        $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        $teachers = User::whereHas('roles', fn($q) => $q->where('name', 'guru'))->get();
        $zip = new \ZipArchive();
        $fileName = 'Semua_Laporan_Guru_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path($fileName);
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($teachers as $teacher) {
                $pdfContent = $this->generatePdfContent($teacher, $request->from, $request->to);
                $zip->addFromString('Laporan_' . $teacher->name . '.pdf', $pdfContent);
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    private function generatePdfContent($teacher, $fromInput, $toInput)
    {
        $from = Carbon::parse($fromInput)->startOfDay();
        $to   = Carbon::parse($toInput)->endOfDay();

        $presences = Presence::where('user_id', $teacher->id)
            ->whereBetween('date', [$from, $to])
            ->with(['schedule.subject', 'schedule.time', 'journal', 'schedule.rombel', 'schedule.classroom'])
            ->get()
            ->filter(fn($p) => $p->schedule !== null && $p->schedule->time !== null)
            ->sortBy(fn($p) => $p->date . $p->schedule->time->start_time);

        $allScheduledDays = Schedule::where('teacher_id', $teacher->id)
            ->with('time')
            ->get()
            ->filter(fn($s) => $s->time !== null);

        $expectedCount = 0;
        $currentDay = $from->copy();
        while ($currentDay->lte($to)) {
            $dayId = $currentDay->dayOfWeekIso;
            $expectedCount += $allScheduledDays->filter(fn($s) => $s->time->day_id == $dayId)->count();
            $currentDay->addDay();
        }

        $rekap = [
            'hadir' => $presences->count(),
            'tidak' => max(0, $expectedCount - $presences->count()),
            'total' => $expectedCount,
        ];

        $grouped = [];
        foreach ($presences->groupBy('date') as $date => $items) {
            $used = [];
            $mergedRows = [];
            foreach ($items as $presence) {
                if (in_array($presence->id, $used) || !$presence->schedule) continue;
                
                $group = [$presence];
                $used[] = $presence->id;
                $current = $presence;

                foreach ($items as $next) {
                    if (in_array($next->id, $used) || !$next->schedule) continue;
                    if ($next->schedule->subject_id === $current->schedule->subject_id &&
                        $next->schedule->rombel_id === $current->schedule->rombel_id &&
                        $next->schedule->classroom_id === $current->schedule->classroom_id &&
                        $next->start_time === $current->end_time) {
                        $group[] = $next;
                        $used[] = $next->id;
                        $current = $next;
                    }
                }
                
                $first = $group[0];
                $mergedRows[] = [
                    'check_in'  => $first->check_in_time,
                    'subject'   => $first->schedule->subject->name ?? '-',
                    'rombel'    => $first->schedule->rombel->name ?? '-',
                    'classroom' => $first->schedule->classroom->name ?? '-',
                    'start'     => $first->start_time,
                    'end'       => end($group)->end_time,
                    'topic'     => $first->journal->topic ?? '-',
                    'sessions'  => count($group),
                ];
            }
            $grouped[$date] = $mergedRows;
        }

        return Pdf::loadView('waka.report.pdf', compact('teacher', 'grouped', 'rekap', 'from', 'to'))
                    ->setPaper('a4', 'portrait')
                    ->output(); 
    }
    public function downloadReportGuru(Request $request, $teacherId)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        $teacher = User::whereHas('roles', fn($q) => $q->where('name', 'guru'))
            ->findOrFail($teacherId);

        $presences = Presence::where('user_id', $teacher->id)
            ->whereBetween('date', [$from, $to])
            ->with([
                'schedule.subject',
                'schedule.time',
                'journal',
                'schedule.rombel',
                'schedule.classroom',
            ])
            ->get()
            ->filter(fn($p) => $p->schedule !== null && $p->schedule->time !== null)
            ->sortBy(fn($p) => $p->date . $p->schedule->time->start_time);

        $allScheduledDays = Schedule::where('teacher_id', $teacher->id)
            ->with('time')
            ->get()
            ->filter(fn($s) => $s->time !== null); 

        $expectedCount = 0;
        $currentDay = $from->copy();
        while ($currentDay->lte($to)) {
            $dayId = $currentDay->dayOfWeekIso;
            $expectedCount += $allScheduledDays->filter(
                fn($s) => $s->time->day_id == $dayId
            )->count();
            $currentDay->addDay();
        }

        $rekap = [
            'hadir' => $presences->count(),
            'tidak' => max(0, $expectedCount - $presences->count()),
            'total' => $expectedCount,
        ];

        $grouped = [];

        foreach ($presences->groupBy('date') as $date => $items) {
            $used       = [];
            $mergedRows = [];

            foreach ($items as $presence) {
                $id = $presence->id;
                if (in_array($id, $used)) continue;

                if ($presence->schedule === null) continue;

                $group   = [$presence];
                $used[]  = $id;
                $current = $presence;

                foreach ($items as $next) {
                    $nextId = $next->id;
                    if (in_array($nextId, $used)) continue;
                    if ($next->schedule === null) continue;

                    $sameSubject = $next->schedule->subject_id   === $current->schedule->subject_id;
                    $sameRombel  = $next->schedule->rombel_id    === $current->schedule->rombel_id;
                    $sameClass   = $next->schedule->classroom_id === $current->schedule->classroom_id;
                    $consecutive = $next->start_time             === $current->end_time;

                    if ($sameSubject && $sameRombel && $sameClass && $consecutive) {
                        $group[]  = $next;
                        $used[]   = $nextId;
                        $current  = $next;
                    }
                }

                $first = $group[0];
                $last  = end($group);

                $mergedRows[] = [
                    'check_in'  => $first->check_in_time,
                    'subject'   => $first->schedule->subject->name   ?? '-',
                    'rombel'    => $first->schedule->rombel->name    ?? '-',
                    'classroom' => $first->schedule->classroom->name ?? '-',
                    'start'     => $first->start_time,
                    'end'       => $last->end_time,
                    'topic'     => $first->journal->topic            ?? '-',
                    'sessions'  => count($group),
                ];
            }

            $grouped[$date] = $mergedRows;
        }

        $pdf = Pdf::loadView('waka.report.pdf', compact(
            'teacher', 'grouped', 'rekap', 'from', 'to'
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan_Guru_' . str_replace(' ', '_', $teacher->name) . '_' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function previewReportGuru(Request $request, $teacherId)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        $teacher = User::whereHas('roles', fn($q) => $q->where('name', 'guru'))
            ->findOrFail($teacherId);

        $presences = Presence::where('user_id', $teacher->id)
            ->whereBetween('date', [$from, $to])
            ->with([
                'schedule.subject',
                'schedule.time',
                'journal',
                'schedule.rombel',
                'schedule.classroom',
            ])
            ->get()
            ->filter(fn($p) => $p->schedule !== null && $p->schedule->time !== null) // guard
            ->sortBy(fn($p) => $p->date . $p->schedule->time->start_time);

        $allScheduledDays = Schedule::where('teacher_id', $teacher->id)
            ->with('time')
            ->get()
            ->filter(fn($s) => $s->time !== null); 

        $expectedCount = 0;
        $currentDay = $from->copy();
        while ($currentDay->lte($to)) {
            $dayId = $currentDay->dayOfWeekIso;
            $expectedCount += $allScheduledDays->filter(
                fn($s) => $s->time->day_id == $dayId
            )->count();
            $currentDay->addDay();
        }

        $rekap = [
            'hadir' => $presences->count(),
            'tidak' => max(0, $expectedCount - $presences->count()),
            'total' => $expectedCount,
        ];

        $grouped = [];

        foreach ($presences->groupBy('date') as $date => $items) {
            $used       = [];
            $mergedRows = [];

            foreach ($items as $presence) {
                $id = $presence->id;
                if (in_array($id, $used)) continue;

                // Guard relasi presence
                if ($presence->schedule === null) continue;

                $group   = [$presence];
                $used[]  = $id;
                $current = $presence;

                foreach ($items as $next) {
                    $nextId = $next->id;
                    if (in_array($nextId, $used)) continue;

                    // Guard relasi next
                    if ($next->schedule === null) continue;

                    $sameSubject = $next->schedule->subject_id   === $current->schedule->subject_id;
                    $sameRombel  = $next->schedule->rombel_id    === $current->schedule->rombel_id;
                    $sameClass   = $next->schedule->classroom_id === $current->schedule->classroom_id;
                    $consecutive = $next->start_time             === $current->end_time;

                    if ($sameSubject && $sameRombel && $sameClass && $consecutive) {
                        $group[]  = $next;
                        $used[]   = $nextId;
                        $current  = $next;
                    }
                }

                $first = $group[0];
                $last  = end($group);

                $mergedRows[] = [
                    'check_in'  => $first->check_in_time,
                    'subject'   => $first->schedule->subject->name   ?? '-',
                    'rombel'    => $first->schedule->rombel->name    ?? '-',
                    'classroom' => $first->schedule->classroom->name ?? '-',
                    'start'     => $first->start_time,
                    'end'       => $last->end_time,
                    'topic'     => $first->journal->topic            ?? '-',
                    'sessions'  => count($group),
                ];
            }

            $grouped[$date] = $mergedRows;
        }

        $pdf = Pdf::loadView('waka.report.pdf', compact(
            'teacher', 'grouped', 'rekap', 'from', 'to'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Preview_Laporan_' . $teacher->name . '.pdf');
    }
      public function monitoringIndex(Request $request)
    {
        $today = Carbon::now();
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $currentDayName = $dayNames[$today->dayOfWeek];
        $selectedDayId = $request->get('day_id');
        $days = Day::orderBy('id')->get();
        if ($selectedDayId) {
            $selectedDay = Day::find($selectedDayId);
        } else {
            $selectedDay = Day::where('name', $currentDayName)->first()
                ?? $days->first();
        }
 
        if (!$selectedDay) {
            return view('monitoring.index', [
                'days' => $days,
                'selectedDay' => null,
                'timeSlots' => collect(),
                'classrooms' => collect(),
                'grid' => [],
                'today' => $today,
                'currentDayName' => $currentDayName,
            ]);
        }
 
        $timeSlots = TimeSlot::where('day_id', $selectedDay->id)
            ->orderBy('start_time')
            ->get();
        $classrooms = Classroom::orderBy('name')->get();
 
        $schedules = Schedule::with(['rombel', 'subject', 'teacher', 'classroom', 'time'])
            ->whereHas('time', function ($q) use ($selectedDay) {
                $q->where('day_id', $selectedDay->id);
            })
            ->get();
        $isToday = $selectedDay->name === $currentDayName;
        $todayDate = $today->toDateString();
 
        $presences = collect();
        if ($isToday) {
            $scheduleIds = $schedules->pluck('id');
            $presences = Presence::whereIn('schedule_id', $scheduleIds)
                ->where('date', $todayDate)
                ->get()
                ->keyBy('schedule_id');
        }
 
        $grid = [];
        foreach ($timeSlots as $slot) {
            $grid[$slot->id] = [];
            foreach ($classrooms as $room) {
                $grid[$slot->id][$room->id] = [
                    'status'   => 'kosong',   
                    'schedule' => null,
                    'presence' => null,
                    'label'    => 'Tidak Ada Kegiatan',
                ];
            }
        }
 
        foreach ($schedules as $schedule) {
            $slotId = $schedule->time_slot_id;
            $roomId = $schedule->classroom_id;
 
            if (!isset($grid[$slotId][$roomId])) {
                continue;
            }
 
            $presence = $presences->get($schedule->id);
            if ($isToday) {
                $now = $today->format('H:i:s');
                $slotObj = $timeSlots->firstWhere('id', $slotId);
 
                if ($presence) {
                    $status = 'aktif';
                    $label  = $schedule->subject->name ?? '-';
                } elseif ($slotObj && $now > $slotObj->end_time) {
                    $status = 'tidak_hadir';
                    $label  = 'Guru Tidak Hadir';
                } elseif ($slotObj && $now >= $slotObj->start_time && $now <= $slotObj->end_time) {
                    $status = 'belum_presensi';
                    $label  = $schedule->subject->name ?? '-';
                } else {
                    $status = 'terjadwal';
                    $label  = $schedule->subject->name ?? '-';
                }
            } else {
                $status = 'terjadwal';
                $label  = $schedule->subject->name ?? '-';
            }
 
            $grid[$slotId][$roomId] = [
                'status'   => $status,
                'schedule' => $schedule,
                'presence' => $presence,
                'label'    => $label,
            ];
        }
 
        return view('monitoring.index', compact(
            'days',
            'selectedDay',
            'timeSlots',
            'classrooms',
            'grid',
            'today',
            'currentDayName',
            'isToday'
        ));
    }
    public function denahIndex(){
        $maps = Room::select(
            'id',
            'name',
            'room_code',
            'floor',
            DB::raw('ST_AsGeoJSON(coordinates) as geojson')
        )->get();
     return view('denah',compact('maps'));   
    }
}