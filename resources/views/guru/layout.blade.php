<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Guru - SMK 3</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('build/assets/icon/smk3.png') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    @if(session('success') || session('error') || $errors->any())
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 5000)" 
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 translate-x-10"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-10"
                class="fixed top-5 right-5 z-[999] p-4 rounded-xl shadow-2xl text-white font-medium border min-w-[300px]
                        {{ session('success') ? 'bg-green-600/100 border-green-500/30' : 'bg-red-600/80 border-red-500/30' }} 
                        backdrop-blur-md">
                
                <div class="flex items-center">
                    <span class="mr-2">{{ session('success') ? '✅' : '⚠️' }}</span>
                    <span class="font-bold">
                        {{ session('success') ? 'Berhasil!' : 'Terjadi Kesalahan' }}
                    </span>
                </div>

                <div class="mt-2 text-sm">
                    @if(session('success'))
                        {{ session('success') }}
                    @elseif(session('error'))
                        {{ session('error') }}
                    @elseif($errors->any())
                        <ul class="list-disc ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif

    <div class="min-h-screen flex" x-data>
        
       <!-- Aside Sidebar -->
        <aside x-cloak 
            x-show="true" 
            class="hidden md:block fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 md:relative md:translate-x-0"
            :class="$store.sidebar.open ? 'translate-x-0' : '-translate-x-full'">
            @include('guru.component.side-bar')
        </aside>

        <!-- Overlay -->
        <div x-cloak
            x-show="$store.sidebar.open" 
            @click="$store.sidebar.toggle()" 
            class=" hidden md:block fixed inset-0 z-40 bg-black/50 md:hidden">
        </div>
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                @include('guru.component.top-bar')
            </header>

            <main class="flex-1 overflow-y-auto p-6 pb-20 md:pb-6">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="md:hidden">
        @include('guru.component.button-bar')
    </div>
    <div id="loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white">
        <div class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-slate-500 font-medium text-sm animate-pulse">Memuat data...</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('loader');
            loader.style.transition = 'opacity 0.5s ease';
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        });
    </script>

</body>
</html>