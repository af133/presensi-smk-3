<div class="h-full flex flex-col bg-slate-900 border-r border-slate-800 shadow-xl">
    <div class="px-6 pt-8 pb-6">
        <h1 class="text-slate-400 font-bold text-xs uppercase tracking-widest mb-6">Dashboard Guru</h1>
       <div class="flex items-center gap-3 p-3 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-sm">
            <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center shadow-lg border border-white/10">
                @if(auth()->user()->photo)
                    <img src="{{ 'storage/app/public/' . auth()->user()->photo }}" alt="Profile" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="overflow-hidden">
                <p class="text-white text-sm font-bold truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-blue-300 text-[10px] font-medium tracking-wide">{{ auth()->user()->nip ?? 'STAFF' }}</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 px-4 py-2 space-y-1">
        
        @php
            $navLinks = [
                ['route' => 'guru.dashboard', 'label' => 'Jadwal Kelas', 'perm' => 'can_jadwal_kelas'],
                ['route' => 'guru.report.index', 'label' => 'Laporan Siswa', 'perm' => 'can_laporan_presensi_siswa_guru'],
                ['route' => 'report.index', 'label' => 'Laporan Guru', 'perm' => 'can_laporan_presensi_guru'],
                ['route' => 'monitoring.index', 'label' => 'Monitoring Kelas', 'perm' => 'can_monitoring_kelas'],
            ];
        @endphp

        @foreach($navLinks as $link)
            @if(auth()->user()->hasPermission($link['perm']) || ($link['perm'] == 'can_laporan_presensi_siswa_guru' && auth()->user()->hasPermission('can_laporan_presensi_siswa_all')))
                <a href="{{ route($link['route']) }}" 
                   class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
                   {{ request()->routeIs($link['route']) 
                      ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' 
                      : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($link['route']) ? 'bg-white' : 'bg-slate-600 group-hover:bg-slate-400' }}"></span>
                    <span class="text-sm font-medium">{{ $link['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <div class="p-4 border-t border-slate-800">
        <form action="{{ route('guru.logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-slate-500 hover:text-red-400 transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar
            </button>
        </form>
    </div>
</div>