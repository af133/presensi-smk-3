<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('build/assets/icon/smk3.png') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full overflow-hidden">

    <div class="flex h-screen">
        <div class="w-64 flex-shrink-0 h-full">
            @include('admin.component.side-bar')
        </div>

        <div class="flex-1 flex flex-col h-full overflow-hidden">
            @include('admin.component.top-bar')

            <main class="flex-1 overflow-y-auto p-6">
                  @if(session('success') || session('error'))
                        <div x-data="{ show: true }" 
                            x-init="setTimeout(() => show = false, 3000)" 
                            x-show="show"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-x-10"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-500"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 translate-x-10"
                            class="fixed top-5 right-5 z-[999] p-4 rounded-xl shadow-2xl text-white font-medium flex items-center border 
                                    {{ session('success') ? 'bg-green-600/100 border-green-500/30' : 'bg-red-600/80 border-red-500/30' }} 
                                    backdrop-blur-md">
                            
                            <span class="mr-2">{{ session('success') ? '✅' : '⚠️' }}</span>
                            {{ session('success') ?? session('error') }}
                        </div>
                    @endif
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>