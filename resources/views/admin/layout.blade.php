<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('build/assets/icon/smk3.png') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
</head>
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
    <body class="h-full overflow-hidden">
    <div class="flex h-screen" x-data>
        
        <div class="transition-all duration-300 ease-in-out bg-gray-900" 
             :class="$store.sidebar.open ? 'w-64' : 'w-0 overflow-hidden'">
            @include('admin.component.side-bar')
        </div>

        <div class="flex-1 flex flex-col h-full overflow-hidden">
            @include('admin.component.top-bar')

            <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>