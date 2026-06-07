@extends('guru.layout')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6">

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Laporan Presensi Siswa</h1>
    </div>

    {{-- Form Pencarian --}}
    <form action="{{ route('guru.report.index') }}" method="GET" class="mb-5">
        <div class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari nama atau NISN..."
                   class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-300">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-semibold">Cari</button>
            @if(request('search'))
                <a href="{{ route('guru.report.index') }}" class="px-4 py-2 border rounded-xl text-sm">Reset</a>
            @endif
        </div>
    </form>
    

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5"
            x-data="{ from: '{{ now()->startOfMonth()->format('Y-m-d') }}', to: '{{ now()->format('Y-m-d') }}' }">
                    
                <div class="mt-5 divide-y divide-gray-50">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Siswa</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Action</span>
                </div>

                @forelse($students as $i => $student)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400 w-8">{{ $students->firstItem() + $i }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $student->name }}</p>
                                <p class="text-xs text-gray-400">{{ $student->nisn }}</p>
                            </div>
                        </div>
                        <a :href="`{{ route('walikelas.report.download', $student->id) }}?from=${from}&to=${to}`"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 transition">
                            Unduh PDF
                        </a>
                    </div>
                @empty
                    <p class="text-center py-10 text-gray-400">Siswa tidak ditemukan.</p>
                @endforelse
            </div>
        <div class="mt-6">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection