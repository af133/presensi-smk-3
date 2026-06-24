@extends('guru.layout')

@section('content')
<div class="max-w-3xl mx-auto  md:p-6" x-data="{ 
    tab: '{{ $studentsTeacher->isNotEmpty() ? 'teacher' : ($studentsPerGuru->isNotEmpty() ? 'perguru' : 'all') }}',
    from: '{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}', 
    to: '{{ request('to', now()->format('Y-m-d')) }}',
    showModal: false,
    previewUrl: ''
}">
    
    <h1 class="text-xl font-bold text-gray-900 mb-6">Laporan Presensi Siswa</h1>

    <form action="{{ route('guru.report.index') }}" method="GET" class="mb-5 flex flex-col gap-3">
        <div class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari nama..." 
                   class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-300">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-900 transition">
                Cari
            </button>
        </div>
        <div class="flex gap-3">
            <div class="flex-1">
                <label class="text-[10px] uppercase font-bold text-gray-400">Dari</label>
                <input type="date" x-model="from" name="from" 
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
            </div>
            <div class="flex-1">
                <label class="text-[10px] uppercase font-bold text-gray-400">Sampai</label>
                <input type="date" x-model="to" name="to" 
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300">
            </div>
            
        </div>
        <div x-show="tab === 'all' || tab === 'perguru'" x-cloak>
            <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Pilih Rombel</label>
            <select name="rombel_id" onchange="this.form.submit()" 
                    class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-300">
                <option value="">Semua Rombel</option>
                @foreach($rombels as $r)
                    <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
    <div class="flex gap-2 mb-5 border-b border-gray-200 overflow-x-auto">
        @if ($user->hasPermission('can_laporan_presensi_siswa_guru'))
            <button @click="tab = 'teacher'" :class="tab === 'teacher' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" class="px-4 py-2 text-sm font-semibold whitespace-nowrap">Wali Kelas</button>
        @endif
        @if ($user->hasPermission('can_laporan_presensi_siswa_perguru'))
            <button @click="tab = 'perguru'" :class="tab === 'perguru' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" class="px-4 py-2 text-sm font-semibold whitespace-nowrap">Siswa yang Saya Ajar</button>
        @endif
        @if ($user->hasPermission('can_laporan_presensi_siswa_all'))
            <button @click="tab = 'all'" :class="tab === 'all' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" class="px-4 py-2 text-sm font-semibold whitespace-nowrap">Semua Siswa</button>
        @endif
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        @if ($user->hasPermission('can_laporan_presensi_siswa_guru'))
            <div x-show="tab === 'teacher'" x-cloak>
                @include('guru.report.table', ['students' => $studentsTeacher, 'scope' => 'wali_kelas'])
            </div>
        @endif
        @if ($user->hasPermission('can_laporan_presensi_siswa_perguru'))
            <div x-show="tab === 'perguru'" x-cloak>
                @include('guru.report.table', ['students' => $studentsPerGuru, 'scope' => 'per_guru'])
            </div>
        @endif
        @if ($user->hasPermission('can_laporan_presensi_siswa_all'))
            <div x-show="tab === 'all'" x-cloak>
                @include('guru.report.table', ['students' => $studentsAll, 'scope' => 'all'])
            </div>
        @endif
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