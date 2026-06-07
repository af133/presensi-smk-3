@extends('guru.layout')

@section('content')
<div class="space-y-6" x-data="jurnalReport()">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Laporan Jurnal Pembelajaran</h1>
            <p class="text-sm text-slate-500 mt-1">Rekap topik/materi yang telah diajarkan oleh setiap guru</p>
        </div>
        {{-- Tombol debug — hapus di production --}}
        <a href="{{ route('waka.laporan.jurnal.debug') }}" target="_blank"
           class="text-xs text-slate-400 underline hover:text-slate-600">
            🔍 Debug Data
        </a>
    </div>

    {{-- FORM FILTER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter Laporan
        </h2>

        <form method="POST" action="{{ route('waka.laporan.jurnal.preview') }}" id="filterForm">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Dari Tanggal --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                        Dari Tanggal <span class="text-red-400">*</span>
                    </label>
                    <input type="date" name="from_date" required
                           value="{{ old('from_date', $filters['from_date'] ?? now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-slate-700
                                  focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                </div>

                {{-- Sampai Tanggal --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                        Sampai Tanggal <span class="text-red-400">*</span>
                    </label>
                    <input type="date" name="to_date" required
                           value="{{ old('to_date', $filters['to_date'] ?? now()->format('Y-m-d')) }}"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-slate-700
                                  focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                </div>

                {{-- Tahun Ajaran --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Tahun Ajaran</label>
                    <select name="academic_year_id"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-slate-700
                                   focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                        <option value="">— Semua Tahun —</option>
                        @foreach($academicYears as $ay)
                            @php
                                $selectedAy = old('academic_year_id', $filters['academic_year_id']
                                    ?? ($ay->is_active ? $ay->id : ''));
                            @endphp
                            <option value="{{ $ay->id }}" {{ $selectedAy == $ay->id ? 'selected' : '' }}>
                                {{ $ay->name }}{{ $ay->is_active ? ' ✓ Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Guru --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Guru</label>
                    <select name="teacher_id"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-slate-700
                                   focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                        <option value="">— Semua Guru —</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}"
                                    {{ old('teacher_id', $filters['teacher_id'] ?? '') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}{{ $t->nip ? ' — ' . $t->nip : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Mata Pelajaran --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Mata Pelajaran</label>
                    <select name="subject_id"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-slate-700
                                   focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                        <option value="">— Semua Mapel —</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}"
                                    {{ old('subject_id', $filters['subject_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Kelas / Rombel</label>
                    <select name="rombel_id"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-slate-700
                                   focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition">
                        <option value="">— Semua Kelas —</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}"
                                    {{ old('rombel_id', $filters['rombel_id'] ?? '') == $r->id ? 'selected' : '' }}>
                                {{ $r->name }} — {{ $r->academicYear->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap gap-3 mt-5 pt-5 border-t border-gray-100">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-slate-900 text-white px-5 py-2.5 rounded-xl
                               text-sm font-semibold hover:bg-slate-700 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Tampilkan Laporan
                </button>

                @if(isset($rows) && count($rows) > 0)
                <button type="button" @click="downloadReport()"
                        class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-xl
                               text-sm font-semibold hover:bg-emerald-700 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Word (.doc)
                </button>
                @endif

                <a href="{{ route('waka.laporan.jurnal.index') }}"
                   class="inline-flex items-center gap-2 bg-white border border-gray-200 text-slate-600
                          px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- HASIL LAPORAN --}}
    @if(isset($rows))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Hasil Laporan</h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Ditemukan <strong class="text-slate-700">{{ count($rows) }}</strong> entri jurnal
                    @if(isset($filters['from_date']))
                        &nbsp;·&nbsp; Periode:
                        {{ \Carbon\Carbon::parse($filters['from_date'])->translatedFormat('d M Y') }}
                        s.d.
                        {{ \Carbon\Carbon::parse($filters['to_date'])->translatedFormat('d M Y') }}
                    @endif
                </p>
            </div>
            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-semibold">
                {{ count($rows) }} entri
            </span>
        </div>

        @if(count($rows) === 0)
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="text-5xl mb-4">📋</div>
                <h3 class="text-base font-semibold text-slate-700">Tidak Ada Jurnal</h3>
                <p class="text-sm text-slate-400 mt-1">
                    Tidak ditemukan jurnal pada rentang tanggal yang dipilih.<br>
                    Coba perlebar rentang tanggal atau ubah filter.
                </p>
                <p class="mt-3 text-xs text-slate-300">
                    Pastikan guru sudah mengisi jurnal saat melakukan presensi.
                </p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-800 text-white text-xs">
                        <th class="px-3 py-3 text-center w-10">No</th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Tanggal</th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Hari</th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Guru</th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Mata Pelajaran</th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Kelas</th>
                        <th class="px-3 py-3 text-left whitespace-nowrap">Ruangan</th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">Jam Ke</th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">Waktu</th>
                        <th class="px-3 py-3 text-center whitespace-nowrap">Check-in</th>
                        <th class="px-3 py-3 text-left min-w-[200px]">Materi / Topik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($rows as $i => $row)
                    @php
                        $jamKe = $row['jam_ke_start'] === $row['jam_ke_end']
                            ? $row['jam_ke_start']
                            : $row['jam_ke_start'] . ' – ' . $row['jam_ke_end'];
                        $waktu = \Carbon\Carbon::parse($row['start_time'])->format('H:i')
                               . ' – '
                               . \Carbon\Carbon::parse($row['end_time'])->format('H:i');
                        $checkIn = $row['check_in_time']
                            ? \Carbon\Carbon::parse($row['check_in_time'])->format('H:i')
                            : null;
                    @endphp
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }} hover:bg-blue-50/30 transition-colors duration-100">
                        <td class="px-3 py-3 text-center text-slate-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-slate-700 font-medium text-xs">
                            {{ \Carbon\Carbon::parse($row['date'])->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap text-slate-500 text-xs">{{ $row['day_name'] }}</td>
                        <td class="px-3 py-3">
                            <div class="font-semibold text-slate-800 text-xs">{{ $row['teacher_name'] }}</div>
                            @if($row['teacher_nip'] !== '-')
                            <div class="text-[10px] text-slate-400">{{ $row['teacher_nip'] }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                {{ $row['subject_name'] }}
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            <div class="text-xs font-medium text-slate-700">{{ $row['rombel_name'] }}</div>
                            <div class="text-[10px] text-slate-400">{{ $row['academic_year'] }}</div>
                        </td>
                        <td class="px-3 py-3 text-xs text-slate-500">{{ $row['classroom_name'] }}</td>
                        <td class="px-3 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $jamKe }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-center text-xs text-slate-500 whitespace-nowrap">{{ $waktu }}</td>
                        <td class="px-3 py-3 text-center">
                            @if($checkIn)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    {{ $checkIn }}
                                </span>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-xs text-slate-600 leading-relaxed max-w-xs">
                            {{ $row['topic'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mb-4 text-3xl">📖</div>
        <h3 class="text-base font-semibold text-slate-700">Pilih Filter untuk Melihat Laporan</h3>
        <p class="text-sm text-slate-400 mt-1 max-w-sm">
            Isi rentang tanggal lalu klik <strong>Tampilkan Laporan</strong>.
        </p>
    </div>
    @endif

</div>

{{-- Hidden download form --}}
<form id="downloadForm" method="GET" action="{{ route('waka.laporan.jurnal.download') }}"
      target="_blank" style="display:none;">
    <input type="hidden" name="from_date"        id="dl_from_date">
    <input type="hidden" name="to_date"          id="dl_to_date">
    <input type="hidden" name="teacher_id"       id="dl_teacher_id">
    <input type="hidden" name="subject_id"       id="dl_subject_id">
    <input type="hidden" name="rombel_id"        id="dl_rombel_id">
    <input type="hidden" name="academic_year_id" id="dl_academic_year_id">
</form>

<script>
function jurnalReport() {
    return {
        downloadReport() {
            const form   = document.getElementById('filterForm');
            const fields = ['from_date','to_date','teacher_id','subject_id','rombel_id','academic_year_id'];
            fields.forEach(f => {
                const el = form.querySelector(`[name="${f}"]`);
                document.getElementById('dl_' + f).value = el ? el.value : '';
            });
            document.getElementById('downloadForm').submit();
        }
    }
}
</script>
@endsection