@extends('guru.layout')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6">

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Laporan Presensi Guru</h1>
        <p class="text-sm text-gray-500 mt-1">Unduh laporan kehadiran per guru</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5"
         x-data="{ from: '{{ now()->startOfMonth()->format('Y-m-d') }}', to: '{{ now()->format('Y-m-d') }}' }">

        {{-- Filter periode --}}
        <p class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wider">Filter Periode</p>
        <div class="flex flex-wrap gap-3 items-end mb-6">
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-400">Dari</label>
                <input type="date" x-model="from"
                       class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-400">Sampai</label>
                <input type="date" x-model="to"
                       class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
        </div>

        {{-- List guru --}}
        <div class="divide-y divide-gray-50">
            @foreach($teachers as $i => $teacher)
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400 w-5">{{ $i + 1 }}</span>
                        <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">
                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $teacher->name }}</p>
                            <p class="text-xs text-gray-400">{{ $teacher->nip ?? 'NIP belum diisi' }}</p>
                        </div>
                    </div>
                    <a :href="`{{ route('waka.report.download', $teacher->id) }}?from=${from}&to=${to}`"
                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh PDF
                    </a>
                </div>
            @endforeach

            @if($teachers->isEmpty())
                <div class="text-center py-10 text-gray-400 text-sm">Tidak ada data guru.</div>
            @endif
        </div>
    </div>
</div>
@endsection