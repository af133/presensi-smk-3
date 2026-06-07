<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Presence;
use App\Models\User;
use App\Models\Subject;
use App\Models\Rombel;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class JurnalReportController extends Controller
{
    public function index(Request $request)
    {
        $teachers      = User::whereHas('roles', fn($q) => $q->where('name', 'guru'))->orderBy('name')->get();
        $subjects      = Subject::orderBy('name')->get();
        $rombels       = Rombel::with('academicYear')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('guru.report.jurnal', compact('teachers', 'subjects', 'rombels', 'academicYears'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'from_date'        => 'required|date',
            'to_date'          => 'required|date|after_or_equal:from_date',
            'teacher_id'       => 'nullable|exists:users,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'rombel_id'        => 'nullable|exists:rombels,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $rows          = $this->getReportData($request);
        $teachers      = User::whereHas('roles', fn($q) => $q->where('name', 'guru'))->orderBy('name')->get();
        $subjects      = Subject::orderBy('name')->get();
        $rombels       = Rombel::with('academicYear')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('guru.report.jurnal', compact('rows', 'teachers', 'subjects', 'rombels', 'academicYears'))
            ->with('filters', $request->all());
    }

    public function download(Request $request)
    {
        $request->validate([
            'from_date'        => 'required|date',
            'to_date'          => 'required|date|after_or_equal:from_date',
            'teacher_id'       => 'nullable|exists:users,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'rombel_id'        => 'nullable|exists:rombels,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $rows     = $this->getReportData($request);
        $fromDate = Carbon::parse($request->from_date)->translatedFormat('d F Y');
        $toDate   = Carbon::parse($request->to_date)->translatedFormat('d F Y');
        $filename = 'Laporan_Jurnal_' . $request->from_date . '_sd_' . $request->to_date . '.doc';

        $filterLabels = [];
        if ($request->filled('teacher_id')) {
            $t = User::find($request->teacher_id);
            $filterLabels[] = 'Guru: ' . ($t->name ?? '-');
        }
        if ($request->filled('subject_id')) {
            $s = Subject::find($request->subject_id);
            $filterLabels[] = 'Mata Pelajaran: ' . ($s->name ?? '-');
        }
        if ($request->filled('rombel_id')) {
            $r = Rombel::find($request->rombel_id);
            $filterLabels[] = 'Kelas: ' . ($r->name ?? '-');
        }

        $html = $this->buildWordHtml($rows, $fromDate, $toDate, $filterLabels);

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-word')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // -----------------------------------------------------------------------
    // DEBUG sementara — hapus setelah konfirmasi data muncul
    // -----------------------------------------------------------------------
    public function debug(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to',   now()->format('Y-m-d'));

        $totalJournals  = Journal::count();
        $totalPresences = Presence::count();

        $journalsInRange = Journal::whereHas('presence', fn($q) =>
            $q->whereBetween('date', [$from, $to])
        )->count();

        // Ambil sampel 5 jurnal terakhir beserta relasinya
        $samples = Journal::with(['presence.schedule.time', 'presence.schedule.subject', 'presence.schedule.teacher'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($j) => [
                'journal_id'   => $j->id,
                'topic'        => $j->topic,
                'presence_id'  => $j->presence_id,
                'presence_date'=> $j->presence?->date,
                'schedule_id'  => $j->presence?->schedule_id,
                'teacher'      => $j->presence?->schedule?->teacher?->name,
                'subject'      => $j->presence?->schedule?->subject?->name,
                'time_slot'    => $j->presence?->schedule?->time?->jam_ke,
            ]);

        return response()->json([
            'total_journals_all'         => $totalJournals,
            'total_presences_all'        => $totalPresences,
            'journals_in_range'          => $journalsInRange,
            'range'                      => "$from to $to",
            'samples_5_latest_journals'  => $samples,
        ]);
    }

    // -----------------------------------------------------------------------
    // Core query — gunakan JOIN agar lebih reliable dan mudah di-debug
    // -----------------------------------------------------------------------
    private function getReportData(Request $request): array
    {
        // 1. Ambil query dasar
        $query = Presence::with([
            'journal',
            'schedule.time.day',
            'schedule.rombel.academicYear',
            'schedule.subject',
            'schedule.teacher',
            'schedule.classroom',
        ])
        ->has('journal') // Wajib ada jurnal
        // Gunakan whereDate agar aman dari masalah timestamp
        ->whereDate('date', '>=', $request->from_date)
        ->whereDate('date', '<=', $request->to_date);

        // 2. Filter dengan Logging (ini untuk melacak kenapa data hilang)
        $query->whereHas('schedule', function ($sq) use ($request) {
            if ($request->filled('teacher_id')) {
                $sq->where('teacher_id', (int) $request->teacher_id);
            }
            if ($request->filled('subject_id')) {
                $sq->where('subject_id', (int) $request->subject_id);
            }
            if ($request->filled('rombel_id')) {
                $sq->where('rombel_id', (int) $request->rombel_id);
            }
            if ($request->filled('academic_year_id')) {
                $sq->whereHas('rombel', fn($rq) =>
                    $rq->where('academic_year_id', (int) $request->academic_year_id)
                );
            }
        });

        // Debug: Log jumlah data yang ditemukan SEBELUM sorting & merge
        $results = $query->get();
        Log::info('Debug Jurnal Report: Ditemukan ' . $results->count() . ' baris presensi.');
        
        if ($results->count() === 0) {
            Log::warning('Jurnal Report Kosong. Filter yang dikirim: ' . json_encode($request->all()));
        }

        // 3. Urutkan
        $presences = $results->sortBy(function ($p) {
            return ($p->date ?? '0000-00-00') . ' ' . ($p->schedule?->time?->start_time ?? '00:00:00');
        })->values();

        // 4. Merge Logic (tetap sama)
        $merged = [];
        foreach ($presences as $presence) {
            $schedule = $presence->schedule;
            $journal  = $presence->journal;
            if (!$schedule || !$journal) continue;

            $slot = $schedule->time;
            $last = count($merged) > 0 ? $merged[count($merged) - 1] : null;

            $canMerge = $last
                && $last['date'] === $presence->date
                && $last['teacher_id'] === $schedule->teacher_id
                && $last['subject_id'] === $schedule->subject_id
                && $last['rombel_id'] === $schedule->rombel_id
                && $last['classroom_id'] === $schedule->classroom_id
                && $last['topic'] === $journal->topic
                && ($last['end_time'] ?? null) === ($slot?->start_time ?? null);

            if ($canMerge) {
                $merged[count($merged) - 1]['end_time'] = $slot->end_time;
                $merged[count($merged) - 1]['jam_ke_end'] = $slot->jam_ke;
            } else {
                $merged[] = [
                    'date' => $presence->date,
                    'teacher_name' => $schedule->teacher->name ?? '-',
                    'teacher_nip' => $schedule->teacher->nip ?? '-',
                    'subject_name' => $schedule->subject->name ?? '-',
                    'rombel_name' => $schedule->rombel->name ?? '-',
                    'classroom_name' => $schedule->classroom->name ?? '-',
                    'academic_year' => $schedule->rombel->academicYear->name ?? '-',
                    'day_name' => $slot?->day->name ?? '-',
                    'jam_ke_start' => $slot?->jam_ke ?? '-',
                    'jam_ke_end' => $slot?->jam_ke ?? '-',
                    'start_time' => $slot?->start_time ?? '-',
                    'end_time' => $slot?->end_time ?? '-',
                    'topic' => $journal->topic,
                    'check_in_time' => $presence->check_in_time,
                    'teacher_id' => $schedule->teacher_id,
                    'subject_id' => $schedule->subject_id,
                    'rombel_id' => $schedule->rombel_id,
                    'classroom_id' => $schedule->classroom_id,
                ];
            }
        }
        return $merged;
    }

    private function buildWordHtml(array $rows, string $fromDate, string $toDate, array $filterLabels): string
    {
        $filterText = count($filterLabels) > 0
            ? implode(' | ', $filterLabels)
            : 'Semua Guru / Semua Mata Pelajaran';

        $tableRows = '';
        foreach ($rows as $i => $row) {
            $no      = $i + 1;
            $date    = Carbon::parse($row['date'])->translatedFormat('d F Y');
            $jamKe   = $row['jam_ke_start'] === $row['jam_ke_end']
                       ? $row['jam_ke_start']
                       : $row['jam_ke_start'] . ' – ' . $row['jam_ke_end'];
            $waktu   = Carbon::parse($row['start_time'])->format('H:i')
                       . ' – '
                       . Carbon::parse($row['end_time'])->format('H:i');
            $checkIn = $row['check_in_time']
                       ? Carbon::parse($row['check_in_time'])->format('H:i')
                       : '-';

            $topicEsc = htmlspecialchars($row['topic'] ?? '-');
            $bg = ($i % 2 === 0) ? '#ffffff' : '#f5f7fa';

            $tableRows .= "
            <tr style='background:{$bg};'>
                <td style='border:1px solid #ccc;padding:6px 8px;text-align:center;'>{$no}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;'>{$date}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;'>{$row['day_name']}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;'>{$row['teacher_name']}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;'>{$row['subject_name']}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;'>{$row['rombel_name']}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;'>{$row['classroom_name']}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;text-align:center;'>{$jamKe}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;text-align:center;'>{$waktu}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;text-align:center;'>{$checkIn}</td>
                <td style='border:1px solid #ccc;padding:6px 8px;'>{$topicEsc}</td>
            </tr>";
        }

        if (empty($rows)) {
            $tableRows = "<tr><td colspan='11' style='text-align:center;padding:20px;color:#888;'>Tidak ada data jurnal ditemukan.</td></tr>";
        }

        $total = count($rows);
        $now   = Carbon::now()->translatedFormat('d F Y, H:i');

        return <<<HTML
<html xmlns:o='urn:schemas-microsoft-com:office:office'
      xmlns:w='urn:schemas-microsoft-com:office:word'
      xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta charset='UTF-8'>
<style>
  body  { font-family: Arial, sans-serif; font-size: 11pt; margin: 2cm; }
  h1    { font-size: 14pt; font-weight: bold; text-align: center; margin-bottom: 4px; }
  h2    { font-size: 11pt; font-weight: normal; text-align: center; margin-top: 0; }
  .info { font-size: 10pt; margin-bottom: 6px; }
  table { border-collapse: collapse; width: 100%; font-size: 10pt; }
  th    { background: #1e3a5f; color: white; border: 1px solid #ccc; padding: 7px 8px; text-align: center; }
  td    { vertical-align: top; }
  .footer { margin-top: 30px; font-size: 10pt; }
  .ttd    { display: inline-block; width: 200px; text-align: center; margin-top: 10px; }
</style>
</head>
<body>
<h1>LAPORAN JURNAL PEMBELAJARAN</h1>
<h2>SMK Negeri 3</h2>
<hr style='border:2px solid #1e3a5f;margin:8px 0 16px;'>
<p class='info'><b>Periode    :</b> {$fromDate} s.d. {$toDate}</p>
<p class='info'><b>Filter     :</b> {$filterText}</p>
<p class='info'><b>Total Data :</b> {$total} entri</p>
<br>
<table>
  <thead>
    <tr>
      <th width='3%'>No</th>
      <th width='9%'>Tanggal</th>
      <th width='7%'>Hari</th>
      <th width='12%'>Guru</th>
      <th width='11%'>Mata Pelajaran</th>
      <th width='8%'>Kelas</th>
      <th width='8%'>Ruangan</th>
      <th width='7%'>Jam Ke</th>
      <th width='8%'>Waktu</th>
      <th width='6%'>Check-in</th>
      <th>Materi / Topik</th>
    </tr>
  </thead>
  <tbody>
    {$tableRows}
  </tbody>
</table>
<div class='footer'>
  <p>Dicetak pada: {$now}</p>
  <br><br>
  <div class='ttd'>
    Wakil Kepala Sekolah,<br><br><br><br>
    ____________________<br>
    NIP.
  </div>
</div>
</body>
</html>
HTML;
    }
}