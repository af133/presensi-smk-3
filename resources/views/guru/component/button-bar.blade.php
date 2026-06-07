<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
    
    {{-- Jadwal Kelas --}}
    @if(auth()->user()->hasPermission('can_jadwal_kelas'))
        <a href="{{ route('guru.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-[10px] font-bold transition {{ request()->routeIs('guru.dashboard') ? 'text-indigo-600' : 'text-gray-400' }}">
            <span class="text-xl">📅</span>
            <span>Jadwal</span>
        </a>
    @endif

    {{-- Laporan Presensi --}}
    @if(auth()->user()->hasPermission('can_laporan_presensi_siswa_guru') || auth()->user()->hasPermission('can_laporan_presensi_siswa_all'))
        <a href="{{ route('guru.report.index') }}" class="flex flex-col items-center justify-center w-full h-full text-[10px] font-bold transition {{ request()->routeIs('guru.report.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <span class="text-xl">📊</span>
            <span>Presensi</span>
        </a>
    @endif

    {{-- Monitoring Kelas --}}
    @if(auth()->user()->hasPermission('can_monitoring_kelas'))
        <a href="{{ route('monitoring.index') }}" class="flex flex-col items-center justify-center w-full h-full text-[10px] font-bold transition {{ request()->routeIs('monitoring.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <span class="text-xl">🔍</span>
            <span>Monitor</span>
        </a>
    @endif

    {{-- Logout --}}
    <form action="{{ route('admin.logout') }}" method="POST" class="flex flex-col items-center justify-center w-full h-full text-gray-400">
        @csrf
        <button type="submit" class="flex flex-col items-center focus:outline-none">
            <span class="text-xl">🚪</span>
            <span class="text-[10px] font-bold">Keluar</span>
        </button>
    </form>
</nav>