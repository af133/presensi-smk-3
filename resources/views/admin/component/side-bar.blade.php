<aside class="h-full bg-gray-900 text-white flex flex-col w-64 overflow-y-auto">
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-white shadow-lg">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
            <div>
                <p class="text-xs text-gray-400">Akun</p>
                <p class="font-bold text-sm text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 p-4 space-y-4">
        <a href="{{ route('admin.dashboard') }}" 
            class="block px-4 py-2 rounded transition-colors font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                Dashboard
            </a>

        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold px-4 py-2 border-t border-gray-800 mt-4">Manajemen Pengguna</div>
        <div class="space-y-1">
            <div class="space-y-1"> 
                <div  x-data="{ open: {{ request()->routeIs('admin.guru.*') || request()->routeIs('admin.roles.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-2 rounded hover:bg-gray-800 text-gray-200 transition-all {{ request()->routeIs('admin.guru.*') || request()->routeIs('admin.roles.*')  ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                        <span>Guru</span>
                        <span class="transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                        
                    </button>
                    
                    <div x-show="open" class="pl-4 py-1 space-y-1 text-sm">
                        <a href="{{ route('admin.guru.create') }}" 
                        class="block py-2 px-4 rounded transition-colors {{ request()->routeIs('admin.guru.create') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                            Tambah Guru
                        </a>
                        
                        <a href="{{ route('admin.guru.index') }}" 
                        class="block py-2 px-4 rounded transition-colors {{ request()->routeIs('admin.guru.index') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                            Daftar Guru
                        </a>

                        <a href="{{ route('admin.roles.index') }}" 
                        class="block py-2 px-4 rounded transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                            Manajemen Role
                        </a>
                    </div>
                </div>
            </div>
             <a href="{{ route('admin.students.index') }}" 
            class="block px-4 py-2 rounded transition-colors font-medium {{ request()->routeIs('admin.students.*') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                Manajamen Siswa
            </a>
        </div>

        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold px-4 py-2 border-t border-gray-800 mt-4">Manajemen Jadwal</div>
        <div class="space-y-1">
            <a href="{{ route('admin.times.index') }}" 
            class="block px-4 py-2 rounded transition-colors font-medium {{ request()->routeIs('admin.times.index') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                Jam Pelajaran
            </a>
            <a href="{{ route('admin.days.index') }}" 
            class="block px-4 py-2 rounded transition-colors font-medium {{ request()->routeIs('admin.days.index') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                Jadwal Hari Pelajaran
            </a>
        </div>

        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold px-4 py-2 border-t border-gray-800 mt-4">Manajemen Pelajaran</div>
        <div class="space-y-1">
            <a href="{{ route('admin.subjects.index') }}" 
            class="block py-2 px-4 rounded transition-colors {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                Nama Mata Pelajaran
            </a>
            <a href="{{ route('admin.classrooms.index') }}" 
            class="block py-2 px-4 rounded transition-colors {{ request()->routeIs('admin.classrooms.*') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                Nama Ruangan
            </a>
            <a href="{{ route('admin.rombels.index') }}" 
            class="block py-2 px-4 rounded transition-colors {{ request()->routeIs('admin.rombels.*') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                Rombongan Belajar
            </a>
            <a href="{{ route('admin.academic-years.index') }}" 
            class="block py-2 px-4 rounded transition-colors {{ request()->routeIs('admin.academic-years.*') ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 text-gray-200' }}">
                Tahun Akaedemik
            </a>
        </div>
    </nav>

    <div class="p-4 border-t border-gray-800 bg-gray-950">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 text-red-400 hover:bg-red-900/20 rounded transition-all font-semibold">
                Logout
            </button>
        </form>
    </div>
</aside>