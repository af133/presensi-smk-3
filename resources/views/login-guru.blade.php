<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Guru | Presensi</title>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/icon/smk3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-cover bg-center bg-no-repeat" style="background-image: url('https://tugujatim.id/wp-content/uploads/2024/05/a1131603-9b3e-48c5-bf3d-c14c912975aa-1-4.jpeg');">
    
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 bg-gray-900/70 backdrop-blur-sm">
        
        <div class="sm:mx-auto sm:w-full sm:max-w-md bg-gray-800/60 p-8 rounded-2xl shadow-2xl border border-white/10 backdrop-blur-md">
            
            <div class="sm:mx-auto sm:w-full sm:max-w-sm text-center">
                <div class="mx-auto h-16 w-16 mb-4">
                    <img src="{{ asset('build/assets/icon/smk3.png') }}" alt="Logo SMK" class="w-full h-full object-contain">
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-white">Portal Guru</h2>
                <p class="mt-2 text-sm text-gray-400">Silakan masukkan kredensial guru untuk melakukan presensi</p>
            </div>

            <div class="mt-8">
                <form action="{{ route('guru.login.process') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="p-3 text-sm text-red-400 bg-red-900/30 border border-red-500/20 rounded-lg">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-200">NIP / NIk</label>
                        <div class="mt-2">
                            <input id="nip" name="nip" type="text" required 
                                class="block w-full rounded-lg bg-white/5 border-0 py-2.5 px-3 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-emerald-500 sm:text-sm"
                                placeholder="Masukkan NIP atau NIS Anda">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-gray-200">Kata Sandi</label>
                        </div>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" required class="block w-full rounded-lg bg-white/5 border-0 py-2.5 px-3 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-emerald-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 bg-gray-700 text-emerald-600 focus:ring-emerald-600">
                        <label for="remember" class="text-sm text-gray-300">Ingat saya</label>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-all">
                            Masuk ke Portal Guru
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</body>
</html>