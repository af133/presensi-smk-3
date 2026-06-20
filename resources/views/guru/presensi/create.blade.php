@extends('guru.layout')

@section('content')
<div class="max-w-4xl mx-auto md:p-6" x-data="presensi()">

    <div class="mb-6 px-4 md:px-0">
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

        <div class="bg-white border border-gray-100 shadow-sm overflow-hidden md:rounded-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-5 py-3 font-semibold text-gray-500 w-8">#</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Nama Siswa</th>
                            @foreach($sessionData as $idx => $sess)
                                <th class="text-center px-4 py-3 font-semibold text-gray-500 min-w-[120px]">
                                    <div class="text-blue-600 text-xs font-bold whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($sess['schedule']->time->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($sess['schedule']->time->end_time)->format('H:i') }}
                                    </div>
                                    <div class="text-gray-400 text-[10px] font-normal uppercase">Jam {{ $idx + 1 }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $i => $student)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-5 py-4 text-gray-400 font-mono">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-medium text-gray-800">
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
                                                @change="updateColor($el)"
                                                class="status-select appearance-none rounded-lg border px-3 py-2 text-xs font-bold text-center w-full focus:outline-none focus:ring-2 transition cursor-pointer"
                                                data-status="{{ $currentStatus }}">
                                            <option value="hadir">Hadir</option>
                                            <option value="izin">Izin</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="alpha">Alpha</option>
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6 px-4 md:px-0 pb-10">
            <a href="{{ route('guru.presensi.list', ['date' => $date]) }}"
               class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                Simpan Presensi
            </button>
        </div>
    </form>
</div>

<script>
function presensi() {
    return {
        init() {
            document.querySelectorAll('.status-select').forEach(el => {
                el.value = el.getAttribute('data-status');
                this.updateColor(el);
            });
        },
        updateColor(el) {
            const colors = {
                hadir: 'bg-emerald-50 text-emerald-700 border-emerald-200 focus:ring-emerald-300',
                izin:  'bg-blue-50 text-blue-700 border-blue-200 focus:ring-blue-300',
                sakit: 'bg-amber-50 text-amber-700 border-amber-200 focus:ring-amber-300',
                alpha: 'bg-red-50 text-red-700 border-red-200 focus:ring-red-300',
            };
            el.classList.remove('bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'focus:ring-emerald-300',
                                'bg-blue-50', 'text-blue-700', 'border-blue-200', 'focus:ring-blue-300',
                                'bg-amber-50', 'text-amber-700', 'border-amber-200', 'focus:ring-amber-300',
                                'bg-red-50', 'text-red-700', 'border-red-200', 'focus:ring-red-300');
            el.classList.add(...colors[el.value].split(' '));
        }
    }
}
</script>
@endsection