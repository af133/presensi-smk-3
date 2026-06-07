@extends('guru.layout')

@section('content')
<div class="max-w-4xl mx-auto md:p-6" x-data="presensi()">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('guru.presensi.list', ['date' => $date]) }}"
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-3">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <h1 class="text-xl font-bold text-gray-900">{{ $schedule->subject->name }}</h1>
        <div class="flex flex-wrap gap-3 mt-1 text-sm text-gray-500">
            <span>📍 {{ $schedule->classroom->name }}</span>
            <span>🏢 {{ $schedule->rombel->name }}</span>
            <span>📅 {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <form action="{{ route('guru.presensi.store', $schedule->id) }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        {{-- Toolbar bulk action --}}
        {{-- <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4 flex flex-wrap items-center gap-3">
            <span class="text-sm font-semibold text-gray-600">Isi semua:</span>
            @foreach(['hadir','izin','sakit','alpha'] as $s)
                <button type="button"
                        @click="setAll('{{ $s }}')"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition
                        {{ $s === 'hadir'  ? 'border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' : '' }}
                        {{ $s === 'izin'   ? 'border-blue-200   text-blue-700   bg-blue-50   hover:bg-blue-100'   : '' }}
                        {{ $s === 'sakit'  ? 'border-amber-200  text-amber-700  bg-amber-50  hover:bg-amber-100'  : '' }}
                        {{ $s === 'alpha'  ? 'border-red-200    text-red-700    bg-red-50    hover:bg-red-100'    : '' }}">
                    Semua {{ ucfirst($s) }}
                </button>
            @endforeach

            <div class="ml-auto text-xs text-gray-400">
                {{ $students->count() }} siswa
            </div>
        </div> --}}

        {{-- Tabel presensi --}}
        <div class="bg-white border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left px-5 py-3 font-semibold text-gray-500 w-8">#</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Nama Siswa</th>
                            @foreach($sessionData as $idx => $sess)
                                <th class="text-center px-4 py-3 font-semibold text-gray-500 min-w-[140px]">
                                    <div class="text-blue-600 text-xs font-bold">
                                        {{ $sess['schedule']->time->start_time }} - {{ $sess['schedule']->time->end_time }}
                                    </div>
                                    <div class="text-gray-400 text-[10px] font-normal">Jam ke-{{ $idx + 1 }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($students as $i => $student)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    {{ $student->name }}
                                    <div class="text-xs text-gray-400">{{ $student->nisn }}</div>
                                </td>
                                @foreach($sessionData as $sess)
                                    @php
                                        $sessId        = $sess['schedule']->id;
                                        $currentStatus = $sess['statusMap']->get($student->id, 'hadir');
                                    @endphp
                                    <td class="px-4 py-3 text-center">
                                        <input type="hidden"
                                               name="presences[{{ $sessId }}][{{ $student->id }}][student_id]"
                                               value="{{ $student->id }}">
                                        <select name="presences[{{ $sessId }}][{{ $student->id }}][status]"
                                                x-ref="sel_{{ $sessId }}_{{ $student->id }}"
                                                @change="updateColor($el)"
                                                class="status-select rounded-xl border px-2 py-1.5 text-xs font-semibold text-center w-full focus:outline-none focus:ring-2 focus:ring-blue-300 transition cursor-pointer"
                                                data-status="{{ $currentStatus }}">
                                            <option value="hadir" {{ $currentStatus === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="izin"  {{ $currentStatus === 'izin'  ? 'selected' : '' }}>Izin</option>
                                            <option value="sakit" {{ $currentStatus === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="alpha" {{ $currentStatus === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 mt-5">
            <a href="{{ route('guru.presensi.list', ['date' => $date]) }}"
               class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                Simpan Presensi
            </button>
        </div>
    </form>
</div>

<script>
function presensi() {
    return {
        init() {
            document.querySelectorAll('.status-select').forEach(el => this.updateColor(el));
        },
        setAll(status) {
            document.querySelectorAll('.status-select').forEach(el => {
                el.value = status;
                this.updateColor(el);
            });
        },
        updateColor(el) {
            const colors = {
                hadir: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                izin:  'bg-blue-50   text-blue-700   border-blue-200',
                sakit: 'bg-amber-50  text-amber-700  border-amber-200',
                alpha: 'bg-red-50    text-red-700    border-red-200',
            };
            // Hapus semua warna lama
            el.className = el.className.replace(/bg-\S+|text-\S+|border-\S+/g, '').trim();
            el.classList.add(
                'status-select', 'rounded-xl', 'border', 'px-2', 'py-1.5',
                'text-xs', 'font-semibold', 'text-center', 'w-full',
                'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-300',
                'transition', 'cursor-pointer',
                ...colors[el.value].split(' ')
            );
        }
    }
}
</script>
@endsection