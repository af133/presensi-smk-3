<div class="h-full flex flex-col">
    <div class="h-16 flex items-center justify-center bg-slate-800">
        <h1 class="text-white font-bold text-lg">Menu Guru</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">
        
        {{-- Jadwal Kelas --}}
        @if(auth()->user()->hasPermission('can_jadwal_kelas'))
            <a href="{{ route('guru.dashboard') }}" class="block p-3 rounded-lg hover:bg-slate-800 transition">Jadwal Kelas</a>
        @endif
        {{-- Laporan Presensi Siswa--}}
        @if(auth()->user()->hasPermission('can_laporan_presensi_siswa_guru') || auth()->user()->hasPermission('can_laporan_presensi_siswa_all'))
            <a href="{{ route('guru.report.index') }}" class="block p-3 rounded-lg hover:bg-slate-800 transition">Laporan Presensi siswa</a>
        @endif

        {{-- Laporan Presensi Guru --}}
        @if(auth()->user()->hasPermission('can_laporan_presensi_guru'))
            <a href="{{ route('report.index') }}" class="block p-3 rounded-lg hover:bg-slate-800 transition">Laporan Presensi Guru</a>
        @endif

        {{-- Monitoring Kelas --}}
        @if(auth()->user()->hasPermission('can_monitoring_kelas'))
            <a href="{{ route('monitoring.index') }}" class="block p-3 rounded-lg hover:bg-slate-800 transition">Monitoring Kelas</a>
        @endif


    </nav>
</div>