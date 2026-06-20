@extends('guru.layout')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Laporan Presensi Guru</h1>
        <p class="text-sm text-gray-500 mt-1">Unduh laporan kehadiran per guru berdasarkan periode.</p>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 md:p-5"
         x-data="{ from: '{{ now()->startOfMonth()->format('Y-m-d') }}', to: '{{ now()->format('Y-m-d') }}' }">

        {{-- Filter Section --}}
        <div class="mb-6">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Filter Periode</p>
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-gray-600">Tanggal Mulai</label>
                    <input type="date" x-model="from"
                           class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-gray-600">Tanggal Selesai</label>
                    <input type="date" x-model="to"
                           class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition">
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="-mx-4 md:mx-0 border border-gray-100 rounded-none md:rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100">
                            <th class="w-12 py-3 px-4 text-left font-semibold text-gray-500">#</th>
                            <th class="py-3 px-2 text-left font-semibold text-gray-500">Nama Guru</th>
                            <th class="py-3 px-4 text-right font-semibold text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($teachers as $i => $teacher)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-3 px-4 text-gray-400 font-medium">{{ $i + 1 }}</td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-3">
                                     
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $teacher->name }}</p>
                                            <p class="text-[11px] text-gray-400">{{ $teacher->nip ?? 'NIP tidak tersedia' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a :href="`{{ route('waka.report.download', $teacher->id) }}?from=${from}&to=${to}`"
                                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 active:scale-95 transition-all text-[11px] shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400">Tidak ada data guru yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection