@extends('admin.layout')

@section('header', 'Dashboard Utama')

@section('content')
<div class="space-y-6">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Siswa</h3>
            <p class="text-3xl font-bold mt-2 text-gray-800">{{ $stats['total_siswa'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Guru</h3>
            <p class="text-3xl font-bold mt-2 text-gray-800">{{ $stats['total_guru'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-xs text-gray-500 uppercase font-bold tracking-wider">Rombel Aktif</h3>
            <p class="text-3xl font-bold mt-2 text-gray-800">{{ $stats['total_rombel'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-xs text-gray-500 uppercase font-bold tracking-wider">Presensi Hari Ini</h3>
            <p class="text-3xl font-bold mt-2 text-indigo-600">{{ $stats['presensi_hari_ini'] }}</p>
        </div>
    </div>

    <!-- Recent Journals Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-700">Aktivitas Mengajar Terbaru</h3>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr class="text-gray-400 text-sm uppercase">
                    <th class="px-6 py-3">Mata Pelajaran</th>
                    <th class="px-6 py-3">Guru</th>
                    <th class="px-6 py-3">Topik Materi</th>
                    <th class="px-6 py-3">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recent_journals as $journal)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $journal->presence->schedule->subject->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $journal->presence->schedule->teacher->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ Str::limit($journal->topic, 40) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $journal->created_at->format('H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada jurnal yang diinput hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection