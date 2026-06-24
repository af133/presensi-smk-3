<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                open: window.innerWidth > 768,
                toggle() { this.open = !this.open }
            })
        })
    </script>
</head>
<body class="h-full antialiased text-gray-800">

    <div id="loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-100 transition-opacity duration-500">
        <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
    </div>

    @if(session('success') || session('error') || $errors->any())
        @endif

    <div class="flex h-screen overflow-hidden" x-data>
        
        <div class="fixed inset-y-0 left-0 z-50 bg-gray-900 transition-all duration-300 ease-in-out shadow-xl" 
             :class="$store.sidebar.open ? 'w-64 translate-x-0' : '-translate-x-full md:w-0 md:translate-x-0'">
            @include('admin.component.side-bar')
        </div>

        <div x-show="$store.sidebar.open" @click="$store.sidebar.toggle()" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        <div class="flex-1 flex flex-col h-full overflow-hidden transition-all duration-300" 
             :class="$store.sidebar.open ? 'md:ml-64' : 'ml-0'">
            
            @include('admin.component.top-bar')

            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-100">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            const loader = document.getElementById('loader');
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 500);
        });
    </script>
</body>
</html>