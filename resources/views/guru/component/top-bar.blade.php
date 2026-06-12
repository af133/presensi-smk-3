<button 
    @click="$store.sidebar.toggle()" 
    class="hidden md:block p-2 text-gray-600 hover:bg-gray-100 rounded-lg md:hidden flex items-center justify-center"
>
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>
<div x-data="{ open: false }" class="relative ml-auto">
    <button @click="open = !open" class="flex items-center gap-2">
        <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
        <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
    </button>
    
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
        <form action="{{ route('guru.logout') }}" method="POST">
            @csrf
            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
        </form>
    </div>
</div>