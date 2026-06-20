
<!-- Profile Dropdown -->
<div x-data="{ open: false }" class="relative ml-auto">
    <button @click="open = !open" class="flex items-center gap-2 hover:bg-gray-50 px-3 py-1.5 rounded-xl transition">
        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
        
        <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center bg-emerald-500 text-white text-xs font-bold shadow-sm">
            @if(auth()->user()->photo)
                <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Profile" class="w-full h-full object-cover">
            @else
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            @endif
        </div>
    </button>
    
    <div x-show="open" 
         x-cloak
         @click.away="open = false" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
        
        <!-- Link Edit Profil -->
        <a href="{{ route('guru.profile.edit') }}" 
           class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Edit Profil
        </a>

        <div class="border-t border-gray-100 my-1"></div>

        <!-- Logout -->
        <form action="{{ route('guru.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>