@extends('guru.layout')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Monitoring Ruangan</h1>
            <p class="text-sm text-slate-500 mt-1">
                Status penggunaan ruangan berdasarkan jadwal — 
                <span class="font-medium text-slate-700">{{ $today->translatedFormat('l, d F Y') }}</span>
            </p>
        </div>

        {{-- Legenda --}}
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-3 py-1 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Aktif (Hadir)
            </span>
            <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-3 py-1 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Belum Presensi
            </span>
            <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-3 py-1 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Guru Tidak Hadir
            </span>
            <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-3 py-1 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span> Terjadwal
            </span>
            <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-3 py-1 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-gray-200"></span> Kosong
            </span>
        </div>
    </div>

    {{-- Tab Hari --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1 flex gap-1 flex-wrap">
        @forelse($days as $day)
            <a href="{{ route('monitoring.index', ['day_id' => $day->id]) }}"
               class="flex-1 min-w-[80px] text-center px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                      {{ ($selectedDay && $selectedDay->id === $day->id)
                            ? 'bg-slate-900 text-white shadow-md'
                            : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $day->name }}
                @if($day->name === $currentDayName)
                    <span class="ml-1 text-[10px] font-bold 
                                {{ ($selectedDay && $selectedDay->id === $day->id) ? 'text-emerald-300' : 'text-emerald-500' }}">
                        ● 
                    </span>
                @endif
            </a>
        @empty
            <p class="text-sm text-slate-400 px-4 py-2">Belum ada data hari.</p>
        @endforelse
    </div>

    @if(!$selectedDay || $timeSlots->isEmpty() || $classrooms->isEmpty())
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center py-24 text-center">
            <div class="text-5xl mb-4">🏫</div>
            <h3 class="text-lg font-semibold text-slate-700">Belum Ada Data</h3>
            <p class="text-sm text-slate-400 mt-1">
                @if(!$selectedDay)
                    Hari tidak ditemukan di database.
                @elseif($timeSlots->isEmpty())
                    Belum ada jam pelajaran untuk hari <strong>{{ $selectedDay->name }}</strong>.
                @else
                    Belum ada ruangan terdaftar.
                @endif
            </p>
        </div>
    @else

        {{-- Info bar hari aktif --}}
        @if($isToday)
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 flex items-center gap-3 text-sm text-emerald-800">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <span>Menampilkan data <strong>real-time hari ini</strong> — status diperbarui setiap kali halaman dimuat.</span>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex items-center gap-3 text-sm text-blue-800">
            <span>📅</span>
            <span>Menampilkan <strong>jadwal</strong> untuk hari <strong>{{ $selectedDay->name }}</strong>. Status presensi hanya tersedia untuk hari ini.</span>
        </div>
        @endif

        {{-- Tabel Grid --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-800 text-white">
                            <th class="px-4 py-4 text-left font-semibold text-slate-200 w-36 sticky left-0 bg-slate-800 z-10 whitespace-nowrap">
                                Jam Ke
                            </th>
                            @foreach($classrooms as $room)
                            <th class="px-4 py-4 text-center font-semibold text-slate-200 min-w-[160px]">
                                {{ $room->name }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($timeSlots as $i => $slot)
                        <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }} hover:bg-blue-50/40 transition-colors duration-150">

                            {{-- Kolom Jam --}}
                            <td class="px-4 py-4 sticky left-0 z-10 
                                       {{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50' }}
                                       hover:bg-blue-50/40">
                                <div class="font-bold text-slate-800 text-sm">{{ $slot->jam_ke }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} –
                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                </div>
                            </td>

                            {{-- Kolom per Ruangan --}}
                            @foreach($classrooms as $room)
                            @php
                                $cell = $grid[$slot->id][$room->id] ?? ['status' => 'kosong', 'schedule' => null, 'presence' => null, 'label' => 'Tidak Ada Kegiatan'];
                                $status = $cell['status'];
                                $schedule = $cell['schedule'];

                                $colorMap = [
                                    'aktif'           => ['bg' => 'bg-emerald-100', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-800', 'border' => 'border-emerald-200'],
                                    'belum_presensi'  => ['bg' => 'bg-amber-50',    'dot' => 'bg-amber-400',   'text' => 'text-amber-800',   'border' => 'border-amber-200'],
                                    'tidak_hadir'     => ['bg' => 'bg-red-50',      'dot' => 'bg-red-500',     'text' => 'text-red-800',     'border' => 'border-red-200'],
                                    'terjadwal'       => ['bg' => 'bg-blue-50',     'dot' => 'bg-blue-400',    'text' => 'text-blue-800',    'border' => 'border-blue-200'],
                                    'kosong'          => ['bg' => 'bg-gray-50',     'dot' => 'bg-gray-300',    'text' => 'text-gray-400',    'border' => 'border-gray-200'],
                                ];
                                $c = $colorMap[$status] ?? $colorMap['kosong'];
                            @endphp
                            <td class="px-3 py-3 text-center">
                                <div class="rounded-xl border {{ $c['bg'] }} {{ $c['border'] }} p-2.5 min-h-[70px] flex flex-col justify-center items-center gap-1 transition-all duration-200 hover:shadow-sm group cursor-default"
                                     @if($schedule)
                                     x-data="{ tooltip: false }"
                                     @mouseenter="tooltip = true"
                                     @mouseleave="tooltip = false"
                                     @endif
                                     >

                                    {{-- Dot indikator --}}
                                    <span class="inline-flex items-center gap-1.5">
                                        @if($status === 'aktif')
                                        <span class="relative flex h-2.5 w-2.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $c['dot'] }} opacity-60"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $c['dot'] }}"></span>
                                        </span>
                                        @else
                                        <span class="w-2.5 h-2.5 rounded-full {{ $c['dot'] }}"></span>
                                        @endif
                                    </span>

                                    {{-- Label utama --}}
                                    <div class="text-xs font-semibold {{ $c['text'] }} leading-tight text-center">
                                        {{ $cell['label'] }}
                                    </div>

                                    {{-- Info tambahan jika ada jadwal --}}
                                    @if($schedule)
                                    <div class="text-[10px] text-slate-500 leading-tight text-center space-y-0.5">
                                        <div class="truncate max-w-[130px]">{{ $schedule->rombel->name ?? '-' }}</div>
                                        <div class="truncate max-w-[130px] font-medium">{{ $schedule->teacher->name ?? '-' }}</div>
                                    </div>

                                    {{-- Tooltip detail --}}
                                    <div x-show="tooltip"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute z-50 mt-1 w-56 bg-white border border-gray-200 rounded-xl shadow-xl p-3 text-left pointer-events-none">
                                        <div class="font-bold text-slate-800 text-xs mb-2">{{ $schedule->subject->name ?? '-' }}</div>
                                        <div class="space-y-1 text-xs text-slate-600">
                                            <div class="flex justify-between">
                                                <span class="text-slate-400">Kelas</span>
                                                <span class="font-medium">{{ $schedule->rombel->name ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-400">Guru</span>
                                                <span class="font-medium">{{ $schedule->teacher->name ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-400">Ruangan</span>
                                                <span class="font-medium">{{ $room->name }}</span>
                                            </div>
                                            @if($cell['presence'])
                                            <div class="flex justify-between">
                                                <span class="text-slate-400">Check-in</span>
                                                <span class="font-medium text-emerald-600">
                                                    {{ \Carbon\Carbon::parse($cell['presence']->check_in_time)->format('H:i') }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full {{ $c['bg'] }} {{ $c['text'] }} font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $c['dot'] }}"></span>
                                                @switch($status)
                                                    @case('aktif') Aktif - Guru Hadir @break
                                                    @case('belum_presensi') Berlangsung - Belum Presensi @break
                                                    @case('tidak_hadir') Guru Tidak Hadir @break
                                                    @case('terjadwal') Terjadwal @break
                                                    @default Tidak Diketahui
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $classrooms->count() + 1 }}" class="text-center py-16 text-slate-400">
                                Tidak ada jam pelajaran untuk hari ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Summary Cards --}}
        @if($isToday)
        @php
            $totalCells = $timeSlots->count() * $classrooms->count();
            $aktif = 0; $tidakHadir = 0; $belumPresensi = 0; $terjadwal = 0; $kosong = 0;
            foreach($grid as $slotRow) {
                foreach($slotRow as $cell) {
                    match($cell['status']) {
                        'aktif' => $aktif++,
                        'tidak_hadir' => $tidakHadir++,
                        'belum_presensi' => $belumPresensi++,
                        'terjadwal' => $terjadwal++,
                        default => $kosong++
                    };
                }
            }
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-lg">✅</div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $aktif }}</div>
                    <div class="text-xs text-slate-400">Guru Hadir</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 text-lg">❌</div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $tidakHadir }}</div>
                    <div class="text-xs text-slate-400">Tidak Hadir</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 text-lg">⏳</div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $belumPresensi }}</div>
                    <div class="text-xs text-slate-400">Belum Presensi</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 text-lg">🚪</div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $kosong }}</div>
                    <div class="text-xs text-slate-400">Ruangan Kosong</div>
                </div>
            </div>
        </div>
        @endif

    @endif
</div>
@endsection