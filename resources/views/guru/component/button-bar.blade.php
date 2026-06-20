<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around items-stretch h-16 z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
    @if(auth()->user()->hasPermission('can_jadwal_kelas'))
        <a href="{{ route('guru.dashboard') }}"
           class="flex flex-col items-center justify-center flex-1 gap-1 text-[10px] font-medium transition
           {{ request()->routeIs('guru.dashboard') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Jadwal</span>
        </a>
    @endif

    @if(auth()->user()->hasPermission('can_laporan_presensi_siswa_guru') || auth()->user()->hasPermission('can_laporan_presensi_siswa_all'))
        <a href="{{ route('guru.report.index') }}"
           class="flex flex-col items-center justify-center flex-1 gap-1 text-[10px] font-medium transition
           {{ request()->routeIs('guru.report.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="leading-tight text-center">Presensi<br>Siswa</span>
        </a>
    @endif

    @if(auth()->user()->hasPermission('can_laporan_presensi_guru'))
        <a href="{{ route('report.index') }}"
           class="flex flex-col items-center justify-center flex-1 gap-1 text-[10px] font-medium transition
           {{ request()->routeIs('report.index') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="leading-tight text-center">Presensi<br>Guru</span>
        </a>
    @endif

    @if(auth()->user()->hasPermission('can_monitoring_kelas'))
        <a href="{{ route('monitoring.index') }}"
           class="flex flex-col items-center justify-center flex-1 gap-1 text-[10px] font-medium transition
           {{ request()->routeIs('monitoring.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>Monitor</span>
        </a>
    @endif

</nav>