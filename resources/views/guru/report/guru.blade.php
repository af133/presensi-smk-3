@extends('guru.layout')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6" 
     x-data="{ 
        from: '{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}', 
        to: '{{ request('to', now()->format('Y-m-d')) }}',
        showModal: false,
        previewUrl: ''
     }">
    
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Laporan Presensi Guru</h1>
            <p class="text-sm text-gray-500 mt-1">Unduh laporan kehadiran per guru berdasarkan periode.</p>
        </div>
        <!-- Tombol Unduh Semua -->
        <a :href="`{{ route('waka.report.download.all') }}?from=${from}&to=${to}`"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-xs font-bold rounded-xl hover:bg-green-700 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            Unduh Semua
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 md:p-5">
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
                                <td class="py-3 px-4 text-gray-400 font-medium">{{ $teachers->firstItem() + $i }}</td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $teacher->name }}</p>
                                            <p class="text-[11px] text-gray-400">{{ $teacher->nip ?? 'NIP tidak tersedia' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right flex items-center justify-end gap-2">
                                    <button @click="previewUrl = '{{ route('waka.report.preview', $teacher->id) }}?from=' + from + '&to=' + to; showModal = true"
                                            class="inline-flex items-center px-2 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 active:scale-95 transition-all text-[11px]">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

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
            <div class="p-4 border-t border-gray-100">
                {{ $teachers->appends(['from' => request('from'), 'to' => request('to')])->links() }}
            </div>
        </div>
    </div>

    <div x-show="showModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" 
         x-cloak>
        <div class="bg-white rounded-2xl w-full max-w-4xl h-[80vh] flex flex-col overflow-hidden shadow-2xl" 
             @click.away="showModal = false">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="font-bold text-gray-800">Preview Laporan</h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-800 font-bold">Tutup</button>
            </div>
            <iframe :src="previewUrl" class="w-full h-full border-0"></iframe>
        </div>
    </div>
</div>
@endsection