<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-8">
    <h2 class="text-lg font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none">
            <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
        </button>

        <div x-show="open" 
             @click.away="open = false"
             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Akun Saya</a>
            <div class="border-t border-gray-100"></div>
            <form action="" method="POST">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                    Logout
                </button>
            </form>
        </div>
    </div>
 

</header>