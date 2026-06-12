<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
    
    {{-- Jadwal Kelas --}}
    @if(auth()->user()->hasPermission('can_jadwal_kelas'))
        <a href="{{ route('guru.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-[10px] font-medium transition {{ request()->routeIs('guru.dashboard') ? 'text-indigo-600' : 'text-gray-500' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span>Jadwal</span>
        </a>
    @endif

    {{-- Laporan Presensi --}}
    @if(auth()->user()->hasPermission('can_laporan_presensi_siswa_guru') || auth()->user()->hasPermission('can_laporan_presensi_siswa_all'))
        <a href="{{ route('guru.report.index') }}" class="flex flex-col items-center justify-center w-full h-full text-[10px] font-medium transition {{ request()->routeIs('guru.report.*') ? 'text-indigo-600' : 'text-gray-500' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span>Presensi</span>
        </a>
    @endif

    {{-- Monitoring Kelas --}}
    @if(auth()->user()->hasPermission('can_monitoring_kelas'))
        <a href="{{ route('monitoring.index') }}" class="flex flex-col items-center justify-center w-full h-full text-[10px] font-medium transition {{ request()->routeIs('monitoring.*') ? 'text-indigo-600' : 'text-gray-500' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span>Monitor</span>
        </a>
    @endif

    {{-- Logout --}}
    <form action="{{ route('admin.logout') }}" method="POST" class="flex flex-col items-center justify-center w-full h-full text-gray-500">
        @csrf
        <button type="submit" class="flex flex-col items-center focus:outline-none">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="text-[10px] font-medium">Keluar</span>
        </button>
    </form>
</nav>